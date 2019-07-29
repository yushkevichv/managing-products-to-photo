<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainerProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_product_type', function (Blueprint $table) {
            $table->unsignedBigInteger('container_id');
            $table->unsignedInteger('product_type_id');

            $table->foreign('product_type_id')
                ->references('id')
                ->on('product_types')
                ->onDelete('cascade');

            $table->foreign('container_id')
                ->references('id')
                ->on('containers')
                ->onDelete('cascade');

            $table->primary(['container_id', 'product_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_product_type');
    }
}
