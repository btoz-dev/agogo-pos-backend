<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipsToRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refunds', function(Blueprint $table) {
            $table->integer('preorder_id')->nullable()->unsigned()->change();
            $table->foreign('preorder_id')->references('id')->on('preorders')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            $table->integer('order_id')->nullable()->unsigned()->change();
            $table->foreign('order_id')->references('id')->on('orders')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->integer('user_id')->unsigned()->change();
            $table->foreign('user_id')->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('cascade');

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
