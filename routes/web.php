<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function() {
    return redirect(route('login'));
});
Auth::routes();
Route::group(['middleware' => 'auth'], function() {

    // Route::group(['middleware' => ['role:admin']], function () {
    //     Route::resource('/role', 'RoleController')->except([
    //         'create', 'show', 'edit', 'update'
    //     ]);

    //     Route::resource('/users', 'UserController')->except([
    //         'show'
    //     ]);
    //     Route::get('/users/roles/{id}', 'UserController@roles')->name('users.roles');
    //     Route::put('/users/roles/{id}', 'UserController@setRole')->name('users.set_role');
    //     Route::post('/users/permission', 'UserController@addPermission')->name('users.add_permission');
    //     Route::get('/users/role-permission', 'UserController@rolePermission')->name('users.roles_permission');
    //     Route::put('/users/permission/{role}', 'UserController@setRolePermission')->name('users.setRolePermission');
    // });
    
    Route::resource('/role', 'RoleController');
    Route::resource('/users', 'UserController');
    Route::get('roles/{id}', 'UserController@roles')->name('users.roles');
    Route::put('roles/{id}', 'UserController@setRole')->name('users.set_role');
    Route::post('permission', 'UserController@addPermission')->name('users.add_permission');
    Route::get('role-permission', 'UserController@rolePermission')->name('users.roles_permission');
    Route::get('default-avatar', 'UserController@avatar')->name('users.avatar');
    Route::put('setDefaultAvatar', 'UserController@setDefaultAvatar')->name('users.setDefaultAvatar');
    Route::put('/users/permission/{role}', 'UserController@setRolePermission')->name('users.setRolePermission');
    Route::resource('/kategori', 'CategoryController');
    Route::resource('/produk', 'ProductController');
    

    // Route::group(['middleware' => ['permission:show products|create products|delete products']], function() {
    //     Route::resource('/kategori', 'CategoryController')->except([
    //         'create', 'show'
    //     ]);
    //     Route::resource('/produk', 'ProductController');
    // });

    Route::group(['middleware' => ['role:kasir']], function() {
        Route::get('/transaksi', 'OrderController@addOrder')->name('order.transaksi');
        Route::get('/checkout', 'OrderController@checkout')->name('order.checkout');
        Route::post('/checkout', 'OrderController@storeOrder')->name('order.storeOrder');
    });

    // Route::group(['middleware' => ['role:admin,kasir']], function() {
    Route::group(['middleware'], function() {
        Route::get('/paid_order', 'OrderController@paid_order')->name('order.paid_order');
        Route::get('/order', 'OrderController@index')->name('order.index');
        Route::get('/laporan_penjualan', 'OrderController@laporan_penjualan')->name('order.laporan_penjualan');
        Route::get('/order/pdf', 'OrderController@invoicePdf')->name('order.pdf');
        Route::get('/order/excel/{invoice}', 'OrderController@invoiceExcel')->name('order.excel');
    });

    Route::group(['middleware'], function() {

        Route::get('/laporan_kas', 'KasController@laporan')->name('kas.laporan');
        Route::get('/kas/pdf', 'KasController@invoicePdf')->name('kas.pdf');
    });

    Route::group(['middleware'], function() {

        Route::get('/laporan_produksi', 'ProductionController@laporan')->name('production.laporan');
    });

    Route::group(['middleware'], function() {

        Route::get('/preorder', 'PreorderController@laporan_pemesanan')->name('preorder.index');
        Route::get('/preorder/pdf', 'PreorderController@invoicePdf')->name('preorder.pdf');
        Route::get('/preorder/excel/{invoice}', 'PreorderController@invoiceExcel')->name('preorder.excel');
    });

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
});