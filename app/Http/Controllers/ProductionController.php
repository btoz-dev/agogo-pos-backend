<?php

namespace App\Http\Controllers;

use App\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\Product;
use DB;

class ProductionController extends Controller
{
    public function getProduct()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    public function getAvailProduct()
    {
        $products = DB::table('products')->where('stock', '>=', 1)->get();
        return response()->json($products, 200);
    }

    public function getNotAvailProduct()
    {
        $products = DB::table('products')->where('stock', '<=', 0)->get();
        return response()->json($products, 200);
    }

    public function getTrxByProduct($id)
    {
        $order = DB::table('order_details')
            ->where('product_id', $id)
            ->where( 'created_at', '>', Carbon::now()->subDays(1))
            ->sum('qty');
        $preorder = DB::table('preorder_details')
            ->where('product_id', $id)
            ->where( 'created_at', '>', Carbon::now()->subDays(1))
            ->sum('qty');
        $getStock = DB::table('products')
            ->select('stock')
            ->where('id', $id) 
            ->get();
        $stock_awal = $getStock[0]->stock + $preorder + $order;

        return response()->json(array(
            'count_order'   => $order,
            'count_preorder'=> $preorder,
            'stok_kemarin'  => $stock_awal
        ),200);
    }

    public function updateStock(Request $request,$id)
    {

        return $request;

        $products = DB::table('products')->where('id', $id)->update(['stock' => 15]);
        // $products->stock = $request->input('sisa_stock');
        // $products->save();
        return response()->json(['status' => 'success'], 200);
    }

    public function getPreorderByProduct($id)
    {
        $preorder = DB::table('preorder_details')->where('product_id', $id)->count();
        return response()->json($preorder, 200);
    }

    public function postProduction(Request $request)
    {
        DB::beginTransaction();
        try {
            $result = collect($request)->map(function ($value) {
                return [
                'product_id'            => $value['product_id'],
                'produksi1'             => $value['produksi1'],
                'produksi2'             => $value['produksi2'],
                'produksi3'             => $value['produksi3'],
                'total_produksi'        => $value['total_produksi'],
                'penjualan_toko'        => $value['penjualan_toko'],
                'penjualan_pemesanan'   => $value['penjualan_pemesanan'],
                'total_penjualan'       => $value['total_penjualan'],
                'ket_rusak'             => $value['ket_rusak'],
                'ket_lain'              => $value['ket_lain'],
                'total_lain'            => $value['total_lain'],
                'catatan'               => $value['catatan'],
                'stock_awal'            => $value['stock_awal'],
                'sisa_stock'            => $value['sisa_stock'],
                ];
            })->all();
            // return response($result);

            foreach ($result as $key => $row) {                
                $production = Production::create([
                    'product_id'            => $row['product_id'],
                    'produksi1'             => $row['produksi1'],
                    'produksi2'             => $row['produksi2'],
                    'produksi3'             => $row['produksi3'],
                    'total_produksi'        => $row['total_produksi'],
                    'penjualan_toko'        => $row['penjualan_toko'],
                    'penjualan_pemesanan'   => $row['penjualan_pemesanan'],
                    'total_penjualan'       => $row['total_penjualan'],
                    'ket_rusak'             => $row['ket_rusak'],
                    'ket_lain'              => $row['ket_lain'],
                    'total_lain'            => $row['total_lain'],
                    'catatan'               => $row['catatan'],
                    'stock_awal'            => $row['stock_awal'],
                    'sisa_stock'            => $row['sisa_stock'],
                    ]);                
                    // return response($row['product_id']);
                //return response($getCount[0]['stock']);
                               
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produksi Berhasil',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }



    }

    public function laporan(Request $request)
    {
        $stock = Production::orderBy('created_at', 'DESC')->with('product');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59';

            $stock = $stock->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $stock = $stock->take(10)->skip(0)->get();
        }

        return view('productions.laporan', [
            'stock' => $stock,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            // 'total_harga' => $this->countTotal_transaksi($kas),
            
        ]);
    }


}
