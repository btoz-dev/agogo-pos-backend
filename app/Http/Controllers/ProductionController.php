<?php

namespace App\Http\Controllers;

use App\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\Product;
use DB;
use Symfony\Component\Process\ProcessBuilder;

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
        $production = DB::table('productions')
            ->where('product_id', $id)
            ->where('created_at', '>', Carbon::today())
            ->orderBy('created_at','DESC')->first();

        if ($production == null ) {

            $date_order = DB::table('order_details')
            ->select('created_at')
            ->where('product_id', $id)
            ->orderBy('created_at', 'DESC')->first();

            $start_date = Carbon::parse($date_order->created_at)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($date_order->created_at)->format('Y-m-d') . ' 23:59:59';

            $order = DB::table('order_details')
            ->join('orders','order_details.order_id', '=', 'orders.id')
            ->where('order_details.product_id', $id)
            ->whereBetween('order_details.created_at', [$start_date, $end_date])
            ->where('orders.status','PAID')
            // ->get();
            ->sum('qty');

            $preorder = DB::table('preorder_details')
            ->join('preorders','preorder_details.preorder_id', '=', 'preorders.id')
            ->where('product_id', $id)
            ->whereBetween('preorder_details.created_at', [$start_date, $end_date])
            ->where('preorders.status','PAID')            
            // ->where('status','PAID')
            ->sum('qty');

            $getStock = DB::table('products')
            ->select('stock')
            ->where('id', $id) 
            ->get();
            $stock_awal = $getStock[0]->stock + $preorder + $order;

            return response()->json(array(
                'last_trx_date'      =>  $date_order->created_at,
                'count_order'   => $order,
                'count_preorder'=> $preorder,
                'stok_kemarin'  => $stock_awal,
                'production'  => $production,
    
            ),200);

            // dd($date_order);
            // return $preorder;

        }else {

            $curent_date = Carbon::now()->format('Y-m-d');
            
            // $curent_date = Carbon::now();

            $start_date = Carbon::parse($curent_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($curent_date)->format('Y-m-d') . ' 23:59:59';

            $order = DB::table('order_details')
            ->join('orders','order_details.order_id', '=', 'orders.id')
            ->where('order_details.product_id', $id)
            ->whereBetween('order_details.created_at', [$start_date, $end_date])
            ->where('orders.status','PAID')
            // ->get();
            ->sum('qty');

            $preorder = DB::table('preorder_details')
            ->join('preorders','preorder_details.preorder_id', '=', 'preorders.id')
            ->where('product_id', $id)
            ->whereBetween('preorder_details.created_at', [$start_date, $end_date])
            ->where('preorders.status','PAID')            
            // ->where('status','PAID')
            ->sum('qty');

            $getStock = DB::table('products')
            ->select('stock')
            ->where('id', $id) 
            ->get();
            $stock_awal = $getStock[0]->stock + $preorder + $order;

            // dd($curent_date);


            return response()->json(array(
                'last_trx_date' => $curent_date,
                'count_order'   => $order,
                'count_preorder'=> $preorder,
                'stok_kemarin'  => $stock_awal,
                'production'    => $production,
    
            ),200);
            
        }
        // $production = DB::table('productions')->rightJoin('products','productions.product_id', '=', 'products.id')->distinct()->get();
        // $production = $production->unique('product_id');
        $order = DB::table('order_details')
            ->where('product_id', $id)
            ->where('created_at', '>', date('Y-m-d', strtotime("-1 days")))
            // ->where('status','PAID')
            ->sum('qty');
        $preorder = DB::table('preorder_details')
            ->where('product_id', $id)
            ->where('created_at', '>', date('Y-m-d', strtotime("-1 days")))
            // ->where('status','PAID')
            ->sum('qty');
        $getStock = DB::table('products')
            ->select('stock')
            ->where('id', $id) 
            ->get();
        $stock_awal = $getStock[0]->stock + $preorder + $order;

        return response()->json(array(
            'count_order'   => $order,
            'count_preorder'=> $preorder,
            'stok_kemarin'  => $stock_awal,
            'production'  => $production,

        ),200);
    }

    public function getAllTrx()
    {
        $production = DB::table('products')->leftJoin('productions','productions.product_id', '=', 'products.id')
        ->distinct()
        ->get();
            // ->where('product_id', $id)
            // ->where('created_at', '>', Carbon::today())
        // $order = DB::table('order_details')
        //     // ->where('product_id', $id)
        //     ->where('created_at', '>', date('Y-m-d', strtotime("-1 days")))
        //     ->sum('qty');
        // $preorder = DB::table('preorder_details')
        //     // ->where('product_id', $id)
        //     ->where('created_at', '>', date('Y-m-d', strtotime("-1 days")))
        //     ->sum('qty');
        // $getStock = DB::table('products')
        //     ->select('stock')
        //     // ->where('id', $id) 
        //     ->get();
        // $stock_awal = $getStock[0]->stock + $preorder + $order;

        return response()->json(
            // 'count_order'   => $order,
            // 'count_preorder'=> $preorder,
            // 'stok_kemarin'  => $stock_awal,
            // 'production'  => 
            $production

        ,200);
    }

    public function getAllTrxByProduct()
    {

        $getTrx = DB::table('products')
        ->Join('preorder_details', 'products.id', '=', 'preorder_details.product_id')
        ->Join('order_details', 'products.id', '=', 'order_details.product_id')
        ->select('products.id', 'products.stock',
                    DB::raw('COALESCE(sum(preorder_details.qty),0) as total_preorder'),
                    DB::raw('COALESCE(sum(order_details.qty),0) as total_order'),
                    DB::raw('products.stock +  sum(order_details.qty) + sum(preorder_details.qty) as stock_awal'))
        ->groupBy('products.id','products.stock')
        ->where('order_details.created_at', '>', Carbon::now()->subDays(30))
        ->where('preorder_details.created_at', '>', Carbon::now()->subDays(30))
        ->get();

        return response()->json($getTrx,200);
    }

    public function updateStock(Request $request,$id)
    {

        // return $request[0]['sisa_stock'];
        $sisa_stock = $request[0]['sisa_stock'];

        $products = DB::table('products')->where('id', $id)->update(['stock' => $sisa_stock]);
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
            $stock = $stock->get();
        }

        return view('productions.laporan', [
            'stock' => $stock,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            // 'total_harga' => $this->countTotal_transaksi($kas),
            
        ]);
    }

    public function GetLastDate()
    {
        $date = Production::select('created_at')->orderBy('created_at','DESC')->first();
        if ($date == null) {
            $date = 'no production';
        }
        return response()->json([            
            'date'   => $date
        ], 200);
    }


}
