<?php

namespace App\Http\Controllers;

use DB;
use App\Preorder;
use Illuminate\Http\Request;
use App\Preorder_detail;

class PreorderController extends Controller
{

    public function generateInvoice()
    {
        $preorder = Preorder::orderBy('created_at', 'DESC');
        if ($preorder->count() > 0) {
            $preorder = $preorder->first();
            $explode = explode('-', $preorder->invoice);
            $count = $explode[1] + 1;
            return 'PS-' . $count;
        }
        return 'PS-1';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Preorder  $preorder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $preorder_detail = Preorder_detail::with(array('product'=>function($query){
            $query->select('name','id');
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
