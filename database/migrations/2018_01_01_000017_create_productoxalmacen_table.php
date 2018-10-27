<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoXAlmacenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productoxalmacen', function (Blueprint $table) {
            $table->integer('idProducto');
            $table->integer('idAlmacen');
            $table->integer('idTipoStock');
            $table->integer('cantidad');
            $table->boolean('deleted');
            $table->timestamps();
            $table->primary(['idProducto','idAlmacen','idTipoStock']);
            $table->foreign('idTipoStock')->references('id')->on('tipoStock')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idAlmacen')->references('id')->on('almacen')->onUpdate('cascade')->onDelete('cascade');
            


     
           
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productoxalmacen');
    }
}
