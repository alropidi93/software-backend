<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaSolicitudDeCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineaSolicitudDeCompra', function (Blueprint $table) {
            
        
            $table->increments('id');
        
            
            $table->integer('idProducto');
            $table->integer('cantidad');
            $table->integer('idSolicitudDeCompra');
            $table->integer('idProveedor');
            
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idSolicitudDeCompra')->references('id')->on('solicitudDeCompra')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProveedor')->references('id')->on('proveedor')->onUpdate('cascade')->onDelete('cascade');
           

      
           
     
         
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lineaSolicitudDeCompra');
    }
}
