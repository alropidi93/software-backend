<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePedidoTransferenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidoDeTransferencia', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idUsuario');
            $table->integer('idAlmacenO');
            $table->integer('idAlmacenD');
          
            $table->text('descripcion');
            $table->boolean('deleted');
            $table->timestamps();

          
            $table->foreign('idUsuario')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idAlmacenO')->references('id')->on('almacen')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idAlmacenD')->references('id')->on('almacen')->onUpdate('cascade')->onDelete('cascade');
            


     
           
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidoTransferencia');
    }
}
