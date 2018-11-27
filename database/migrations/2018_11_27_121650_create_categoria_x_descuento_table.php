<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaXDescuentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoriaxdescuento', function (Blueprint $table) {
            $table->integer('idTienda');
            $table->integer('idCategoria');
            $table->integer('idDescuento');
            $table->boolean('deleted');
            $table->timestamps();
            $table->primary(['idTienda','idCategoria','idDescuento']);
            $table->foreign('idTienda')->references('id')->on('tienda')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idCategoria')->references('id')->on('categoria')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('categoriaxdescuento');
    }
}
