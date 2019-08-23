<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice')->unique();
            $table->string('nama');
            $table->date('tgl_selesai');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('catatan')->nullable();            
            $table->integer('user_id');
            $table->integer('subtotal');
            $table->integer('discount')->nullable();            
            $table->integer('add_fee')->nullable();
            $table->integer('total');
            $table->integer('uang_muka')->nullable();
            $table->integer('uang_dibayar')->nullable();
            $table->integer('uang_kembali')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('preorders');
    }
}
