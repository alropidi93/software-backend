<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescuentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idTienda');
            $table->integer('idProducto')->nullable();
            $table->integer('idCategoria')->nullable();
            $table->boolean('es2x1');
            $table->double('porcentaje');
            $table->date('fechaIni');
            $table->date('fechaFin');
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idTienda')->references('id')->on('tienda')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idCategoria')->references('id')->on('categoria')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descuento');
    }
}
