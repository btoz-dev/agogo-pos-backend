<?php

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use App\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\Product as ProductResource;
use App\Product;
use App\Http\Resources\Category as CategoryResource;
use App\Category;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/cart', 'OrderController@addToCart');
Route::get('/cart', 'OrderController@getCart');
Route::delete('/cart/{id}', 'OrderController@removeCart');
Route::post('/customer/search', 'CustomerController@search');
Route::get('/chart', 'HomeController@getChart');

//User API
Route::resource('user', 'UserController');
Route::get('/users', function () {
    return new UserCollection(User::all());
});

//Auth API
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});



//Produk API
Route::resource('product', 'ProductController');
Route::get('/products', function () {
    return new ProductCollection(Product::all());
});
// Route::get('/product/{id}', 'OrderController@getProduct');


//User API
Route::resource('category', 'CategoryController');
Route::get('/categories', function () {
    return new CategoryCollection(Category::all());
});
// Route::resource('category', 'CategoryController');


//Order API
Route::post('/orders', 'OrderController@postOrder');
Route::get('/orders', 'OrderController@getOrders');
Route::get('/order/{id}', 'OrderController@getOrderDetail');



// //Route Kategori
// Route::apiResource('/categories', 'CategoryController');
// //Route Produk
// Route::group(['prefix' => 'categories'], function () {
//     Route::resource('/{categories}/products', 'ProductController');
// });

// Route::group(['prefix' => 'auth'], function () {
//     Route::post('login', 'AuthController@login');
//     Route::post('signup', 'AuthController@signup');
  
// Route::group(['middleware' => 'auth:api'], function() {
//         Route::get('logout', 'AuthController@logout');
//         Route::get('user', 'AuthController@user');
//     });
// });