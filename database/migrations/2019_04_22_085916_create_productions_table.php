<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('produksi1')->nullable();
            $table->integer('produksi2')->nullable();
            $table->integer('produksi3')->nullable();
            $table->integer('total_produksi')->nullable();
            $table->integer('penjualan_toko')->nullable();
            $table->integer('penjualan_pemesanan')->nullable();
            $table->integer('total_penjualan')->nullable();
            $table->integer('ket_rusak')->nullable();
            $table->integer('ket_lain')->nullable();
            $table->integer('total_lain')->nullable();
            $table->string('catatan')->nullable();
            $table->integer('stock_awal');
            $table->integer('sisa_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productions');
    }
}
