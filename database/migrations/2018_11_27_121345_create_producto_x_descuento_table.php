<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoXDescuentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productoxdescuento', function (Blueprint $table) {
            $table->integer('idTienda');
            $table->integer('idProducto');
            $table->integer('idDescuento');
            $table->boolean('deleted');
            $table->timestamps();
            $table->primary(['idTienda','idProducto','idDescuento']);
            $table->foreign('idTienda')->references('id')->on('tienda')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idDescuento')->references('id')->on('descuento')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productoxdescuento');
    }
}
