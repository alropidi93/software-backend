<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioXTiendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarioxtienda', function (Blueprint $table) {
            
        
            $table->integer('idUsuario');
            $table->integer('idTienda');
            $table->boolean('miembroPrincipal');
            $table->boolean('deleted');
            $table->timestamps();
            $table->primary(['idUsuario','idTienda']);
            $table->foreign('idUsuario')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('usuarioxtienda');
    }
}
