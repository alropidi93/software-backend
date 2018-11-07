<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineasolicitudtransferenciafkUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lineaPedidoDeTransferencia', function (Blueprint $table) {
            $table->integer('idLineaSolicitudCompra')->nullable();
            $table->foreign('idLineaSolicitudCompra')->references('id')->on('lineaSolicitudDeCompra')->onUpdate('cascade')->onDelete('cascade');
        });
      
    }
}
