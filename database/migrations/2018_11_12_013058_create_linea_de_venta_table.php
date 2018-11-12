<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaDeVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineaDeVenta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idProducto');
            $table->integer('cantidad');
            $table->integer('idComprobantePago')->nullable();
            $table->integer('idCotizacion')->nullable();
            
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idComprobantePago')->references('id')->on('comprobantePago')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lineaDeVenta');
    }
}
