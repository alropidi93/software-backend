<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTiendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tienda', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',200);
            $table->string('distrito',100);
            $table->string('ubicacion',500);
            $table->string('direccion',500);
            $table->string('telefono',20);
            $table->boolean('deleted');
            $table->integer('idJefeTienda')->nullable();
            $table->integer('idJefeAlmacen')->nullable();
            
     
            $table->timestamps();

           
            $table->foreign('idJefeTienda')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idJefeAlmacen')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tienda');
    }
}
