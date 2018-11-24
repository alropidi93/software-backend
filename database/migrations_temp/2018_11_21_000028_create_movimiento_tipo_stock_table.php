<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientoTipoStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientoTipoStock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idProducto');
            $table->integer('idAlmacen');
            $table->integer('idTipoStock');
            $table->integer('idUsuario');
            $table->integer('cantidad');
            $table->char('signo');
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idTipoStock')->references('id')->on('tipoStock')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idAlmacen')->references('id')->on('almacen')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idUsuario')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientoTipoStock');
    }
}
