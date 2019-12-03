<?php

namespace App\Http\Controllers;

use DB;
use Session;
use PDF;
use App\User;
use App\Preorder;
use App\Product;
use Carbon\Carbon;
use App\Preorder_detail;
use Illuminate\Http\Request;

class PreorderController extends Controller
{

    public function generateInvoice()
    {
        $preorder = Preorder::orderBy('id', 'DESC');
        if ($preorder->count() > 0) {
            $preorder = $preorder->first();
            $explode = explode('-', $preorder->invoice);
            $count = $explode[1] + 1;
            return 'PS-' . $count;
        }
        return 'PS-1';
    }

    public function checkLastInvoice()
    {
        $preorder = Preorder::orderBy('id', 'DESC');
        if ($preorder->count() > 0) {
            $preorder = $preorder->first();
            $explode = explode('-', $preorder->invoice);
            $count = $explode[1] + 1;
            $result =  'PS-' . $count;
            return response()->json(array(
                'current_invoice' => $result), 200);        
        }
        $result = 'PS-1';
        return response()->json(array(
            'current_invoice' => $result), 200);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function countTotal_harga($preorders)
    {
        $total = 0;
        if ($preorders->count() > 0) {
            $sub_total = $preorders->pluck('total')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    private function countUangMuka_transaksi($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('uang_muka')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    private function countSisaHarusBayar_transaksi($orders)
    {
        $total = 0;
        if ($orders->count() > 0) {
            $sub_total = $orders->pluck('sisa_harus_bayar')->all();
            $total = array_sum($sub_total);
        }
        return $total;
    }

    public function laporan_pemesanan(Request $request)
    {
        Session::put('lap_start_date', null);
        Session::put('lap_end_date', null);
        // $customers = Customer::orderBy('name', 'ASC')->get();
        $users = User::role('kasir')->orderBy('name', 'ASC')->get();
        // $preorders = Preorder::orderBy('created_at', 'DESC');
        $preorders = Preorder::where('status','!=', 'CANCEL');
                    // ->where('status','PAID');

        $cancel_preorders = Preorder::orderBy('created_at', 'DESC')
                    ->where('status','CANCEL');
        // if (!empty($request->customer_id)) {
        //     $orders = $orders->where('customer_id', $request->customer_id);
        // }

        if (!empty($request->user_id)) {
            $preorders = $preorders->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59';
            
            $preorders = $preorders->whereBetween('created_at', [$start_date, $end_date])->get();
            $cancel_preorders = $cancel_preorders->whereBetween('created_at', [$start_date, $end_date])->get();

            Session::put('lap_start_date', $start_date);
            Session::put('lap_end_date', $end_date);
        } else {
            $start_date = Carbon::now()->toDateString() . ' 00:00:01';
            $end_date = Carbon::now()->toDateString() . ' 23:59:59';

            $preorders = $preorders->whereBetween('created_at', [$start_date, $end_date])->get();
            $cancel_preorders = $cancel_preorders->whereBetween('created_at', [$start_date, $end_date])->get();
            Session::put('lap_start_date', $start_date);
            Session::put('lap_end_date', $end_date);
        }

        // return $preorders[0]->preorder->status;
        $phd_today = Carbon::now()->toDateString();

        return view('preorders.index', [
            'preorders' => $preorders,
            'cancel_preorders' => $cancel_preorders,
            'phd_today' => $phd_today,
            // 'sold' => $this->countItem($orders),
            'total_harga' => $this->countTotal_harga($preorders),
            'total_uang_muka' => $this->countUangMuka_transaksi($preorders),
            'total_harus_bayar' => $this->countSisaHarusBayar_transaksi($preorders),
            // 'customers' => $customers,
            'total_harga_cancel' => $this->countTotal_harga($cancel_preorders),
            'total_uang_muka_cancel' => $this->countUangMuka_transaksi($cancel_preorders),            
            'users' => $users
        ]);
    }

    public function index()
    {
        // $preorders = Preorder::where(['status' => 'UNPAID'])->get();
        
        $preorders = Preorder::where(['status' => 'UNPAID'])->with(array('user'=>function($query){
            $query->select('id','username');
        }))->get();

        return response()->json($preorders, 200);
    }

    public function paid_preorder()
    {
        $preorders = Preorder::where(['status' => 'PAID'])->with(array('user'=>function($query){
            $query->select('id','username');
        }))->get();        
        
        return response()->json($preorders, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $get_role = User::role(['admin', 'manager'])
        ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {        
        DB::beginTransaction();
        try {
            // return response($request[0]['user_id']);

            $preorder = Preorder::create(array(
                'invoice' => $this->generateInvoice(),
                // 'customer_id' => $customer->id,
                'nama'          => $request[0]['nama'],
                'tgl_pesan'     => $request[0]['tgl_pesan'],
                'tgl_selesai'   => $request[0]['tgl_selesai'],
                'waktu_selesai' => $request[0]['waktu_selesai'],
                'alamat'        => $request[0]['alamat'],
                'telepon'       => $request[0]['telepon'],
                'catatan'       => $request[0]['catatan'],
                'user_id'       => $request[0]['user_id'],
                'subtotal'      => $request[0]['subtotal'],
                'discount'      => $request[0]['diskon'],
                'add_fee'       => $request[0]['add_fee'],
                'uang_muka'     => $request[0]['uang_muka'],
                'total'         => $request[0]['total'],
                'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
                'uang_dibayar'  => $request[0]['uang_dibayar'],
                'uang_kembali'  => $request[0]['uang_kembali'],
                'status'        => $request[0]['status']
            ));

            $result = collect($request)->map(function ($value) {
                return [
                    'product_id'    => $value['product_id'],
                    'qty'           => $value['qty'],
                    'price'         => $value['price'],
                ];
            })->all();
            // return response($result);

            foreach ($result as $key => $row) {
                    $preorder->preorder_detail()->create([
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ]);                
                // return response($row['product_id']);
                //return response($getCount[0]['stock']);                               
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $preorder->invoice,
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
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Preorder  $preorder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $preorder_detail = Preorder_detail::with(array('product'=>function($query){
            $query->select('name','id','price');
        }))->where('preorder_id', $id)->get();
        // $product = Product::where('id',$order_detail[0]['product_id'])->get();
        // $result = compact('order_detail','product');
        // return response($order_detail[0]['product_id']);

        return response()->json($preorder_detail, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Preorder  $preorder
     * @return \Illuminate\Http\Response
     */
    public function edit(Preorder $preorder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Preorder  $preorder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Preorder $preorder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Preorder  $preorder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $preorder = Preorder::find($id);
        // $preorder-> delete();
        // return response()->json([
        //     'status' => 'data deleted',
        //     'message' => $preorder->invoice,
        // ], 200);
        $preorder = Preorder::find($id);
        $preorder->status = 'CANCEL';
        $preorder->save();
        return response()->json($preorder, 200);
    }

    public function cancelPreorder(Request $request,$id)
    {

        // return $request[0]['sisa_stock'];
        // $sisa_stock = $request[0]['sisa_stock'];
        $get_role = User::role(['admin', 'manager'])
        ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {        
        $preorder = DB::table('preorders')->where('id', $id)->update(['status' => 'CANCEL']);
        // $products->stock = $request->input('sisa_stock');
        // $products->save();
        return response()->json(['status' => 'success'], 200);
        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
    }

    public function payPreorder($id)
    {
        $preorder = Preorder::find($id);
        $preorder->status = 'PAID';
        $preorder->save();
        return response()->json($preorder, 200);
    }

    public function editPreorder(Request $request)
    {
        $get_role = User::role(['admin', 'manager'])
        ->where('username', $request[0]['username_approval'])->count();

    //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {        
        DB::beginTransaction();
        try {

            $delPreorder = Preorder::find($request[0]['preorder_id']);
            $delPreorder-> delete();

            $preorder = Preorder::create(array(
                'id'            => $request[0]['preorder_id'],
                'invoice'       => $request[0]['invoice'],
                'nama'          => $request[0]['nama'],
                'tgl_pesan'     => $request[0]['tgl_pesan'],
                'tgl_selesai'   => $request[0]['tgl_selesai'],
                'waktu_selesai' => $request[0]['waktu_selesai'],
                'alamat'        => $request[0]['alamat'],
                'telepon'       => $request[0]['telepon'],
                'catatan'       => $request[0]['catatan'],
                'user_id'       => $request[0]['user_id'],
                'subtotal'      => $request[0]['subtotal'],
                'discount'      => $request[0]['diskon'],
                'add_fee'       => $request[0]['add_fee'],
                'uang_muka'     => $request[0]['uang_muka'],
                'total'         => $request[0]['total'],
                'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
                'uang_dibayar'  => $request[0]['uang_dibayar'],
                'uang_kembali'  => $request[0]['uang_kembali'],
                'status'        => $request[0]['status']
            ));



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
                    $preorder->preorder_detail()->create([
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ]);                

                    DB::table('products')->where('id', $row['product_id'])->decrement('stock', $row['qty']); 
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_pemesanan', $row['qty']);
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $row['qty']);
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $row['qty']);
                        
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
                'message' => $preorder->invoice,
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
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
    }

    public function bayarPreorder(Request $request)
    {        

        $get_role = User::role(['admin', 'manager'])
        ->where('username', $request[0]['username_approval'])->count();

    //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {        
        DB::beginTransaction();
        try {
        

            $delPreorder = Preorder::find($request[0]['preorder_id']);
            $delPreorder-> delete();

            $preorder = Preorder::create(array(
                'id'            => $request[0]['preorder_id'],
                'invoice'       => $request[0]['invoice'],
                'nama'          => $request[0]['nama'],
                'tgl_pesan'     => $request[0]['tgl_pesan'],
                'tgl_selesai'   => $request[0]['tgl_selesai'],
                'waktu_selesai' => $request[0]['waktu_selesai'],
                'alamat'        => $request[0]['alamat'],
                'telepon'       => $request[0]['telepon'],
                'catatan'       => $request[0]['catatan'],
                'user_id'       => $request[0]['user_id'],
                'subtotal'      => $request[0]['subtotal'],
                'discount'      => $request[0]['diskon'],
                'add_fee'       => $request[0]['add_fee'],
                'uang_muka'     => $request[0]['uang_muka'],
                'total'         => $request[0]['total'],
                'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
                'uang_dibayar'  => $request[0]['uang_dibayar'],
                'uang_kembali'  => $request[0]['uang_kembali'],
                'status'        => $request[0]['status'],
                'hari_pelunasan' => $request[0]['hari_pelunasan']
            ));



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
                    $preorder->preorder_detail()->create([
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ]);                

                    DB::table('products')->where('id', $row['product_id'])->decrement('stock', $row['qty']); 
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_pemesanan', $row['qty']);
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $row['qty']);
                    DB::table('productions')->where('product_id', $row['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $row['qty']);
                    
                        
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
                'message' => $preorder->invoice,
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
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
        
    }
        
    

    public function invoicePdf()
    {
        $start_date = Session::get('lap_start_date');
        $end_date = Session::get('lap_end_date');
        $today = Carbon::today()->toDateString();
        // return $end_date;
        // $preorders = Preorder::where('status', 'PAID')->get();
        $preorders = Preorder::where('status','!=', 'CANCEL')->get();
        $cancel_preorders = Preorder::orderBy('created_at', 'DESC')->where('status','CANCEL');
        

               
            $start = Carbon::parse($start_date)->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($end_date)->format('Y-m-d') . ' 23:59:59';

            $preorders = $preorders->where('created_at','>',$start)
            ->where('created_at','<',$end);

            $cancel_preorders = $cancel_preorders->where('created_at','>',$start)
            ->where('created_at','<',$end);


            $total_harga = $this->countTotal_harga($preorders);
            $total_uang_muka = $this->countUangMuka_transaksi($preorders);
            $total_harus_bayar = $this->countSisaHarusBayar_transaksi($preorders);
                // 'customers' => $customers,
            $total_harga_cancel = $this->countTotal_harga($cancel_preorders);
            $total_uang_muka_cancel = $this->countUangMuka_transaksi($cancel_preorders);

            $start_date_lap = Carbon::parse($start_date)->format('d/m/Y');
            $end_date_lap = Carbon::parse($end_date)->format('d/m/Y');
       
        
        // return $order;
        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])
        ->loadView('preorders.report.invoice', 
        compact('preorders','total_harga','total_uang_muka','total_harus_bayar','total_harga_cancel','total_uang_muka_cancel','today','start_date_lap','end_date_lap'));

        
        return $pdf->stream();
    }
}
