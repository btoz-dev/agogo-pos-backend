<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Cookie;
use App\User;
use App\Order;
use App\Refund;
use App\Product;
use App\Preorder;
use App\Kas;
use App\Customer;
use Carbon\Carbon;
use App\Order_detail;
use Illuminate\Http\Request;
use App\Exports\OrderInvoice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function addOrder()
    {
        $products = Product::orderBy('created_at', 'DESC')->get();
        return view('orders.add', compact('products'));
    }

    public function getProduct($id)
    {
        $products = Product::findOrFail($id);
        return response()->json($products, 200);
    }

    public function addToCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);
        $getCart = json_decode($request->cookie('cart'), true);

        if ($getCart) {
            if (array_key_exists($request->product_id, $getCart)) {
                $getCart[$request->product_id]['qty'] += $request->qty;
                return response()->json($getCart, 200)
                    ->cookie('cart', json_encode($getCart), 120);
            }
        }

        $getCart[$request->product_id] = [
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => $request->qty
        ];
        return response()->json($getCart, 200)
            ->cookie('cart', json_encode($getCart), 120);
    }

    public function getCart()
    {
        $cart = json_decode(request()->cookie('cart'), true);
        return response()->json($cart, 200);
    }

    public function removeCart($id)
    {
        $cart = json_decode(request()->cookie('cart'), true);
        unset($cart[$id]);
        return response()->json($cart, 200)->cookie('cart', json_encode($cart), 120);
    }

    public function checkout()
    {
        return view('orders.checkout');
    }

    public function storeOrder(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'name' => 'required|string|max:100',
            'address' => 'required',
            'phone' => 'required|numeric'
        ]);

        $cart = json_decode($request->cookie('cart'), true);
        $result = collect($cart)->map(function ($value) {
            return [
                'code' => $value['code'],
                'name' => $value['name'],
                'qty' => $value['qty'],
                'price' => $value['price'],
                'result' => $value['price'] * $value['qty']
            ];
        })->all();

        DB::beginTransaction();
        try {
            $customer = Customer::firstOrCreate([
                'email' => $request->email
            ], [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone
            ]);

            $order = Order::create([
                'invoice' => $this->generateInvoice(),
                'customer_id' => $customer->id,
                'user_id' => auth()->user()->id,
                'total' => array_sum(array_column($result, 'result'))
            ]);

            foreach ($result as $key => $row) {
                $order->order_detail()->create([
                    'product_id' => $key,
                    'qty' => $row['qty'],
                    'price' => $row['price']
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $order->invoice,
            ], 200)->cookie(Cookie::forget('cart'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function generateInvoice()
    {
        $order = Order::orderBy('id', 'DESC');
        if ($order->count() > 0) {
            $order = $order->first();
            $explode = explode('-', $order->invoice);
            $count = $explode[1] + 1;
            return 'TK-' . $count;
        }
        return 'TK-1';
    }

    public function checkLastInvoice()
    {
        $order = Order::orderBy('id', 'DESC');
        if ($order->count() > 0) {
            $order = $order->first();
            $explode = explode('-', $order->invoice);
            $count = $explode[1] + 1;
            $result =  'TK-' . $count;
            return response()->json(array(
                'current_invoice' => $result), 200);        
        }
        $result = 'TK-1';
        return response()->json(array(
            'current_invoice' => $result), 200);

    }

    public function generateInvoiceRefunds()
    {
        $refund = Refund::orderBy('id', 'DESC');
        if ($refund->count() > 0) {
            $refund = $refund->first();
            $explode = explode('-', $refund->invoice);
            $count = $explode[1] + 1;
            return 'RF-' . $count;
        }
        return 'RF-1';
    }

    public function index(Request $request)
    {
        // $customers = Customer::orderBy('name', 'ASC')->get();
        Session::put('lap_order_sd', null);
        Session::put('lap_order_ed', null);

        $users = User::role('kasir')->orderBy('name', 'ASC')->get();
        $orders = Order_detail::join('orders', function ($join) {
                $join->on('order_details.order_id', '=', 'orders.id')
                ->where('orders.status','PAID')
                ->orderBy('created_at', 'DESC')
                ;
                })
                ->select('order_details.product_id', DB::raw('SUM(price) AS price'), DB::raw('SUM(qty) AS qty'))
                ->groupBy('order_details.product_id' );
        // $orders = $orders->where('status','PAID')->get();
       
        if (!empty($request->user_id)) {
            $orders = $orders->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                // 'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 23:59:59';

            $orders = $orders->whereBetween('order_details.created_at', [$start_date, $end_date])->get();
            Session::put('lap_order_sd', $start_date);
            Session::put('lap_order_ed', $end_date);
        } else {
            
            $start_date = Carbon::now()->toDateString() . ' 00:00:01';
            $end_date = Carbon::now()->toDateString() . ' 23:59:59';
            $orders = $orders->whereBetween('order_details.created_at', [$start_date, $end_date])->get();

        }

        // $phd_today = Carbon::now()->toDateString();
        $phd_today = Carbon::parse($request->start_date)->format('d/m/Y');
        // 'phd_today' => $phd_today,
        



        return view('orders.index', [
            'orders' => $orders,
            'phd_today' => $phd_today,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            'total_harga' => $this->countTotal_harga($orders),
            // 'customers' => $customers,
            'users' => $users
        ]);
    }

    public function paid_order(Request $request)
    {
        // $customers = Customer::orderBy('name', 'ASC')->get();
        $users = User::role('kasir')->orderBy('name', 'ASC')->get();
        $orders = Order::where(['status' => 'PAID'])->get();
        // $orders = $orders->where('status','PAID')->get();
        // return $orders;
       
        if (!empty($request->user_id)) {
            $orders = $orders->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                // 'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 23:59:59';

            // $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])->get();$orders = $orders
            $orders = $orders->where('created_at', '>=', $start_date)->where('created_at', '<', $end_date);            
        } else {
            
            $start_date = Carbon::now()->toDateString() . ' 00:00:01';
            $end_date = Carbon::now()->toDateString() . ' 23:59:59';
            // $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])->get();
            $orders = $orders->where('created_at', '>=', $start_date)->where('created_at', '<', $end_date);

        }

        // return $end_date;
        $phd_today = Carbon::now()->toDateString();
        // return $phd_today;

        return view('orders.paid_order', [
            'orders' => $orders,
            'phd_today' => $phd_today,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            'total_harga' => $this->countTotal_harga($orders),
            // 'customers' => $customers,
            'users' => $users
        ]);
    }

    private function countTotal_harga($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('price')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    public function laporan_penjualan(Request $request)
    {

        Session::put('lap_bulanan_sd', null);
        Session::put('lap_bulanan_ed', null);
        
        $users = User::role('kasir')->orderBy('name', 'ASC')->get();
        $orders = Kas::select(DB::raw("DATE(created_at) as trx_date"),
                                // DB::raw('sum(subtotal) as subtotal'),
                                DB::raw('sum(transaksi) as total_transaksi'),
                                DB::raw('sum(diskon) as diskon'))
                        // ->where('status', 'PAID')
                        // ->where('created_at', '>=', $start_date)
                        // ->where('created_at', '<', $end_date)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->groupBy(DB::raw("DATE(created_at)"))
                        ->get();

        // return $orders;
        // $firstDayofcurMonth = Carbon::now()->startOfMonth()->toDateString();
        // $lastDayofCurMonth = Carbon::now()->endOfMonth()->toDateString();

        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:01';
        $end_date   = Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59';

        Session::put('lap_bulanan_sd', $start_date);
        Session::put('lap_bulanan_ed', $end_date);


        if (!empty($request->user_id)) {
            $orders = $orders->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            // return $orders;
            $this->validate($request, [
                'start_date' => 'nullable|date',
                'end_date'   => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date   = Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59';
            $orders = Kas::select(DB::raw("DATE(created_at) as trx_date"),
                                DB::raw('sum(transaksi) as total_transaksi'),
                                DB::raw('sum(diskon) as diskon'))
                                ->where('created_at', '>=', $start_date)
                                ->where('created_at', '<', $end_date)
                                ->groupBy(DB::raw("DATE(created_at)"))
                                ->get();

            // return $orders;

          
            // $orders     = $orders->where('created_at', '>=', $start_date)->where('created_at', '<', $end_date)->get();
            Session::put('lap_bulanan_sd', $start_date);
            Session::put('lap_bulanan_ed', $end_date);
        } 

        // $data = $orders[0]->created_at + $preorders[0]->created_at;
        
        $firstDayofcurMonth = Carbon::now()->startOfMonth()->toDateString();
        $lastDayofCurMonth = Carbon::now()->endOfMonth()->toDateString();
        
        

        return view('orders.laporan_bulanan', [
            'orders' => $orders,            
            'firstDayofcurMonth'=> $firstDayofcurMonth,
            'lastDayofCurMonth' => $lastDayofCurMonth,
            'total_harga'       => $this->countTotal_transaksi($orders),
            'total_subtotal'    => $this->countSubTotal_transaksi($orders),
            'total_discount'    => $this->countDiscount_transaksi($orders),            
            'users' => $users
        ]);
    }

    private function countTotal_transaksi($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('total_transaksi')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    private function countSubTotal_transaksi($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $total_transaksi = $orders->pluck('total_transaksi')->all();
            $diskon = $orders->pluck('diskon')->all();
            $total = array_sum($total_transaksi) + array_sum($diskon);
        }
        return $total;
    }

    private function countDiscount_transaksi($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('diskon')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    

    private function countCustomer($orders)
    {
        $customer = [];
        if ($orders->count() > 0) {
            foreach ($orders as $row) {
                $customer[] = $row->customer->email;
            }
        }
        return count(array_unique($customer));
    }

    private function countTotal($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('total')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    private function countItem($order)
    {
        $data = 0;
        if ($order->count() > 0) {
            foreach ($order as $row) {
                $qty = $row->order_detail->pluck('qty')->all();
                $val = array_sum($qty);
                $data += $val;
            }
        }
        return $data;
    }

    public function invoicePdf()
    {
        $start_date = Session::get('lap_order_sd');
        $end_date = Session::get('lap_order_ed');
        $today = Carbon::today()->toDateString();

        $start = Carbon::parse($start_date)->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($end_date)->format('Y-m-d') . ' 23:59:59';

       
        $orders = Order_detail::join('orders', function ($join) use ($start,$end) {
            $join->on('order_details.order_id', '=', 'orders.id')
            ->where('orders.status','PAID')
            ->whereBetween('order_details.created_at', [$start, $end])
            ->orderBy('created_at', 'DESC')
            ;
            })
            ->select('order_details.product_id', DB::raw('SUM(price) AS price'), DB::raw('SUM(qty) AS qty'))
            ->groupBy('order_details.product_id')->get();  
        
        $total_harga = $this->countTotal_harga($orders);
        
        $start_date_lap = Carbon::parse($start_date)->format('d/m/Y');        
        

        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])
            ->loadView('orders.report.invoice', compact('orders','start_date_lap','total_harga'));
        return $pdf->stream();
    }

    public function invoiceBulananPdf()
    {
        $start_date = Session::get('lap_bulanan_sd');
        $end_date = Session::get('lap_bulanan_ed');
        $today = Carbon::today()->toDateString();

        // return $start_date;
              
        // $start = Carbon::parse($start_date)->format('Y-m-d') . ' 00:00:01';
        // $end = Carbon::parse($end_date)->format('Y-m-d') . ' 23:59:59';

            // $orders = Order::select(DB::raw("DATE(created_at) as trx_date"),
            // DB::raw('sum(subtotal) as subtotal'),
            // DB::raw('sum(total) as total'),
            // DB::raw('sum(discount) as discount'))
            // ->where('status', 'PAID')
            // ->where('created_at', '>=', $start)
            // ->where('created_at', '<', $end)
            // ->groupBy(DB::raw("DATE(created_at)"))
            // ->get();

            $orders = Kas::select(DB::raw("DATE(created_at) as trx_date"),
                                DB::raw('sum(transaksi) as total_transaksi'),
                                DB::raw('sum(diskon) as diskon'))
                                ->where('created_at', '>=', $start_date)
                                ->where('created_at', '<', $end_date)
                                ->groupBy(DB::raw("DATE(created_at)"))
                                ->get();
            
            // $orders = Kas::select(DB::raw("DATE(created_at) as trx_date"),
            // // DB::raw('sum(subtotal) as subtotal'),
            // DB::raw('sum(transaksi) as total_transaksi'),
            // DB::raw('sum(diskon) as diskon'))
            // // ->where('status', 'PAID')
            // // ->where('created_at', '>=', $start_date)
            // // ->where('created_at', '<', $end_date)
            // ->whereMonth('created_at', Carbon::now()->month)
            // ->groupBy(DB::raw("DATE(created_at)"))
            // ->get();

            $total_harga = $this->countTotal_transaksi($orders);
            $total_subtotal = $this->countSubTotal_transaksi($orders);
            $total_discount = $this->countDiscount_transaksi($orders); 

            $start_date_lap = Carbon::parse($start_date)->format('d/m/Y');
            $end_date_lap = Carbon::parse($end_date)->format('d/m/Y');
       
        
        // return $order;
        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true]
        )->loadView('orders.report.invoiceBulanan', 
        compact('orders','total_harga','total_subtotal','total_discount','today','start_date_lap','end_date_lap'));

        
        return $pdf->stream();
    }

    public function invoiceExcel($invoice)
    {
        return (new OrderInvoice($invoice))->download('invoice-' . $invoice . '.xlsx');
    }

    public function postOrder(Request $request)
    {

        DB::beginTransaction();
        try {
            // return response($request[0]['user_id']);

            if(!empty( $request[0]['invoice']) ){            
                $setInvoice = $request[0]['invoice'] ;
            }
            else {
                $setInvoice = $this->generateInvoice();
            }

            if(!empty( $request[0]['id']) ){            
                $order = Order::create(array(
                    'id'            => $request[0]['id'],
                    'invoice'       => $setInvoice,
                    // 'customer_id' => $customer->id,
                    'user_id'       => $request[0]['user_id'],
                    'subtotal'      => $request[0]['subtotal'],
                    'discount'      => $request[0]['diskon'],
                    'total'         => $request[0]['total'],
                    'uang_dibayar'  => $request[0]['dibayar'],
                    'uang_kembali'  => $request[0]['kembali'],
                    'status'        => $request[0]['status']
                ));
            }
            else {
                $order = Order::create(array(
                    'invoice'       => $setInvoice,
                    // 'customer_id' => $customer->id,
                    'user_id'       => $request[0]['user_id'],
                    'subtotal'      => $request[0]['subtotal'],
                    'discount'      => $request[0]['diskon'],
                    'total'         => $request[0]['total'],
                    'uang_dibayar'  => $request[0]['dibayar'],
                    'uang_kembali'  => $request[0]['kembali'],
                    'status'        => $request[0]['status']
                ));
            }

            

            $result = collect($request)->map(function ($value) {
                return [
                    'product_id'    => $value['product_id'],
                    'qty'           => $value['qty'],
                    'price'         => $value['price'],
                ];
            })->all();
            // return response($result);

            foreach ($result as $key => $row) {
                $getCount = Product::where(['id' => $row['product_id']])->get();
                
                if ($getCount[0]['stock'] >= $row['qty']) {
                    $order->order_detail()->create([
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ]);
                        DB::table('products')->where('id', $row['product_id'])->decrement('stock', $row['qty']);
                        DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $row['qty']);
                        DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $row['qty']);
                        DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $row['qty']);
                            // ->increment('total_penjualan', $row['qty']); 
                            
                }
                else {
                    throw new \Exception('Stock ' . $getCount[0]['name'] . ' Tidak Mencukupi');
                }
                // return response($row['product_id']);
                //return response($getCount[0]['stock']);
                               
            }
            

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $order->invoice,
            ], 200)->cookie(Cookie::forget('cart'));
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUnpaidOrders()
    {
        $orders = Order::where(['status' => 'UNPAID'])->get();
        return response()->json($orders, 200);
    }

    public function getPaidOrders()
    {
        $orders = Order::where(['status' => 'PAID'])->get();
        return response()->json($orders, 200);
    }

    public function getOrderDetail($id) {

        $order_detail = Order_detail::with(array('product'=>function($query){
            $query->select('name','id','price');
        }))->where('order_id', $id)->get();
        // $order_detail = Order_detail::with('product')->where('order_id', $id)->get();
        // $product = Product::where('id',$order_detail[0]['product_id'])->get();
        // $result = compact('order_detail','product');
        // return response($order_detail[0]['product_id']);

        return response()->json($order_detail, 200);
    }

    public function keepOrder(Request $request)
    {
       
        DB::beginTransaction();
        try {
            if (!empty($request[0]['order_id'])) {
                
                $delOrder = Order::find($request[0]['order_id']);
                $delOrder-> delete(); 

                $order = Order::create(array(
                    'id'            => $request[0]['order_id'],
                    'invoice'       => $request[0]['invoice'],
                    // 'invoice' => $this->generateInvoice(),
                    // 'customer_id' => $customer->id,
                    'user_id'       => $request[0]['user_id'],
                    'subtotal'      => $request[0]['subtotal'],
                    'discount'      => $request[0]['diskon'],
                    'total'         => $request[0]['total'],
                    'uang_dibayar'  => $request[0]['dibayar'],
                    'uang_kembali'  => $request[0]['kembali'],
                    'status'        => $request[0]['status']
                ));
            }
            else {                  
                $order = Order::create(array(
                    // 'id'            => $request[0]['order_id'],
                    // 'invoice'       => $request[0]['invoice'],
                    'invoice' => $this->generateInvoice(),
                    // 'customer_id' => $customer->id,
                    'user_id'       => $request[0]['user_id'],
                    'subtotal'      => $request[0]['subtotal'],
                    'discount'      => $request[0]['diskon'],
                    'total'         => $request[0]['total'],
                    'uang_dibayar'  => $request[0]['dibayar'],
                    'uang_kembali'  => $request[0]['kembali'],
                    'status'        => $request[0]['status']
                ));
            }
            

            $result = collect($request)->map(function ($value) {
                return [
                    'product_id'    => $value['product_id'],
                    'qty'           => $value['qty'],
                    'price'         => $value['price'],
                ];
            })->all();
            // return response($result);

            foreach ($result as $key => $row) {
                $getCount = Product::where(['id' => $row['product_id']])->get();
                
                if ($getCount[0]['stock'] > $row['qty']) {
                    $order->order_detail()->create([
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ]);
                        // DB::table('products')->where('id', $row['product_id'])->decrement('stock', $row['qty']); 
                }
                else {
                    throw new \Exception('Stock ' . $getCount[0]['name'] . ' Tidak Mencukupi');
                }
                // return response($row['product_id']);
                //return response($getCount[0]['stock']);
                               
            }
            

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $order->invoice,
            ], 200)->cookie(Cookie::forget('cart'));
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        $order-> delete();
        return response()->json([
            'status' => 'data deleted',
            'message' => $order->invoice,
        ], 200);
    }

    public function postRefunds(Request $request){

        //Check apakah user punya role 
        $get_role = User::role(['admin', 'manager'])
            ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {

        
        DB::beginTransaction();
        try {
            $refund = Refund::create(array(
                'invoice'       => $this->generateInvoiceRefunds(),
                'order_id'      => $request[0]['order_id'],
                'preorder_id'   => $request[0]['preorder_id'],
                'total'         => $request[0]['total'],
            ));

            $result = collect($request)->map(function ($value) {
                return [
                    'order_id'    => $value['order_id'],
                    'preorder_id' => $value['preorder_id'],
                    'product_id'  => $value['product_id'],
                    'qty'         => $value['qty'],
                    'price'       => $value['price'],
                    'total'       => $value['total'],
                ];
            })->all();
            // return response($result);

            foreach ($result as $key => $row) {  
                // Kurangin Total Amount di Summary Order
                if ($row['order_id'] != null) {

                // DB::table('orders')->where('id', $row['order_id'])
                //     ->decrement('subtotal', $row['total']);
                // DB::table('orders')->where('id', $row['order_id'])
                //     ->decrement('total', $row['total']);
                // DB::table('orders')->where('id', $row['order_id'])
                //     ->increment('uang_kembali', $row['total']);
                // Hapus Produk yg di refun di order_detail
                DB::table('order_details')
                    ->where('order_id', $row['order_id'])
                    ->where('product_id', $row['product_id'])->delete();
                }
                else {
                // Kurangin Total Amount di Summary Preorder
                // DB::table('preorders')->where('id', $row['preorder_id'])
                //     ->decrement('total', $row['total']);
                // DB::table('preorders')->where('id', $row['preorder_id'])
                //     ->decrement('subtotal', $row['total']);
                // DB::table('preorders')->where('id', $row['preorder_id'])
                //     ->increment('uang_kembali', $row['total']);
                // Hapus Produk yg di refun di preorder_detail
                DB::table('preorder_details')
                    ->where('preorder_id', $row['preorder_id'])
                    ->where('product_id', $row['product_id'])->delete();
                }

                $refund->refund_detail()->create([
                    'product_id' => $row['product_id'],
                    'qty'        => $row['qty'],
                    'price'      => $row['price']
                ]);
                DB::table('products')->where('id', $row['product_id'])
                ->increment('stock', $row['qty']);
                                       
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $refund->invoice,
            ], 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ], 400);
            }
        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid PIN'
            ], 400);
        }
    }
}
