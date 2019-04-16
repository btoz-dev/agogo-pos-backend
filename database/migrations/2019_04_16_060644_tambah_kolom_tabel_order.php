<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahKolomTabelOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->integer('subtotal')->nullable()->after('user_id');
            $table->integer('discount')->nullable()->after('subtotal');
            $table->integer('add_fee')->nullable()->after('discount');
            $table->integer('uang_dibayar')->nullable()->after('total');
            $table->integer('uang_kembali')->nullable()->after('uang_dibayar');
            $table->string('status')->nullable()->after('uang_kembali');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
