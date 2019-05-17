<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Preorder;
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

    public function laporan_pemesanan(Request $request)
    {
        // $customers = Customer::orderBy('name', 'ASC')->get();
        $users = User::role('kasir')->orderBy('name', 'ASC')->get();
        $preorders = Preorder::orderBy('created_at', 'DESC')
                    ->where('status','PAID');

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
        } else {
            $start_date = Carbon::now()->toDateString() . ' 00:00:01';
            $end_date = Carbon::now()->toDateString() . ' 23:59:59';
            $preorders = $preorders->whereBetween('created_at', [$start_date, $end_date])->get();
        }

        // return $preorders[0]->preorder->status;

        return view('preorders.index', [
            'preorders' => $preorders,
            // 'sold' => $this->countItem($orders),
            'total_harga' => $this->countTotal_harga($preorders),
            // 'total_customer' => $this->countCustomer($orders),
            // 'customers' => $customers,
            'users' => $users
        ]);
    }

    public function index()
    {
        $preorders = Preorder::where(['status' => 'UNPAID'])->get();
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
        DB::beginTransaction();
        try {
            // return response($request[0]['user_id']);

            $preorder = Preorder::create(array(
                'invoice' => $this->generateInvoice(),
                // 'customer_id' => $customer->id,
                'nama'          => $request[0]['nama'],
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
                'uang_dibayar'  => $request[0]['dibayar'],
                'uang_kembali'  => $request[0]['kembali'],
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
        $preorder = Preorder::find($id);
        $preorder-> delete();
        return response()->json([
            'status' => 'data deleted',
            'message' => $preorder->invoice,
        ], 200);
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
       
        DB::beginTransaction();
        try {

            $delPreorder = Preorder::find($request[0]['preorder_id']);
            $delPreorder-> delete();

            $preorder = Preorder::create(array(
                'id'            => $request[0]['preorder_id'],
                'invoice'       => $request[0]['invoice'],
                'nama'          => $request[0]['nama'],
                'tgl_selesai'   => $request[0]['tgl_selesai'],
                'alamat'        => $request[0]['alamat'],
                'telepon'       => $request[0]['telepon'],
                'catatan'       => $request[0]['catatan'],
                'user_id'       => $request[0]['user_id'],
                'subtotal'      => $request[0]['subtotal'],
                'discount'      => $request[0]['diskon'],
                'total'         => $request[0]['total'],
                'uang_dibayar'  => $request[0]['dibayar'],
                'uang_kembali'  => $request[0]['kembali'],
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
}
