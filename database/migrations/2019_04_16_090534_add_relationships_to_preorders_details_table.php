<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipsToPreordersDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('preorder_details', function(Blueprint $table) {
            $table->integer('preorder_id')->unsigned()->change();
            $table->foreign('preorder_id')->references('id')->on('preorders')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->integer('product_id')->unsigned()->change();
            $table->foreign('product_id')->references('id')->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
