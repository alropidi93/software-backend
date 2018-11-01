<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaPedidoTransferenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineaPedidoDeTransferencia', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idProducto');
            $table->integer('cantidad');
            $table->integer('idPedidoTransferencia');
            $table->integer('idLineaSolicitudCompra')->nullable();
            
            $table->boolean('deleted');
            $table->timestamps();

          
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idPedidoTransferencia')->references('id')->on('pedidoDeTransferencia')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idLineaSolicitudCompra')->references('id')->on('lineaSolicitudDeCompra')->onUpdate('cascade')->onDelete('cascade');   
           
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lineaPedidoDeTransferencia');
    }
}
