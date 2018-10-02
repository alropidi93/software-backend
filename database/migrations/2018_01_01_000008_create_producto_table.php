<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',200);
            $table->integer('stockMin');
            $table->text('descripcion');
            $table->integer('idTipoProducto')->nullable;
            $table->integer('idUnidadMedida');
            $table->string('categoria',300);
            $table->double('precio');
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idTipoProducto')->references('id')->on('tipoProducto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idUnidadMedida')->references('id')->on('unidadMedida')->onUpdate('cascade')->onDelete('cascade');
     
         
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto');
    }
}
