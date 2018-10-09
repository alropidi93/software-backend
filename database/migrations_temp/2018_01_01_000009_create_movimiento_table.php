<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento', function (Blueprint $table) {
            $table->increments('id');
           
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->integer('idUsuario');
            $table->boolean('deleted');
            $table->timestamps();

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
        Schema::dropIfExists('movimiento');
    }
}
