<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Product;
use Illuminate\Support\Facades\DB;
use App\Production;

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

    public function getOrderByProduct($id)
    {
        $order = DB::table('order_details')->where('product_id', $id)->count();
        return response()->json($order, 200);
    }

    public function getPreorderByProduct($id)
    {
        $order = DB::table('preorder_details')->where('product_id', $id)->count();
        return response()->json($order, 200);
    }

    public function postProduction(Request $request)
    {
        DB::beginTransaction();
        try {
            $production = Production::create(array(
                'product_id'            => $request[0]['product_id'],
                'produksi1'             => $request[0]['produksi1'],
                'produksi2'             => $request[0]['produksi2'],
                'produksi3'             => $request[0]['produksi3'],
                'total_produksi'        => $request[0]['total_produksi'],
                'penjualan_toko'        => $request[0]['penjualan_toko'],
                'penjualan_pemesanan'   => $request[0]['penjualan_pemesanan'],
                'total_penjualan'       => $request[0]['total_penjualan'],
                'ket_rusak'             => $request[0]['ket_rusak'],
                'ket_lain'              => $request[0]['ket_lain'],
                'total_lain'            => $request[0]['total_lain'],
                'catatan'               => $request[0]['catatan'],
                'stock_awal'            => $request[0]['stock_awal'],
                'sisa_stock'            => $request[0]['sisa_stock'],
            ));
            // return response($result);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $production,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }


}
