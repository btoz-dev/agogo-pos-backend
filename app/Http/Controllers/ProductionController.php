<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Product;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function getProduct()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    public function getAvailProduct()
    {
        $batas = 0;
        $products = DB::table('products')->where('stock', '>=', 0)->get();
        return response()->json($products, 200);
    }
}
