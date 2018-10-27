<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',200);
            $table->integer('idTienda')->nullable();
            $table->boolean('deleted');
         
     
            $table->timestamps();


            $table->foreign('idTienda')->references('id')->on('tienda')->onUpdate('cascade')->onDelete('cascade');
           
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('almacen');
    }
}
