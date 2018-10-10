<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('idPersonaNatural')->primary();
            $table->string('password',60);
            $table->integer('idTipoUsuario');
            $table->integer('idTienda')->nullable();
            $table->boolean('deleted');
            
            
     
            $table->timestamps();


            $table->foreign('idTipoUsuario')->references('id')->on('tipoUsuario')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idPersonaNatural')->references('id')->on('personaNatural')->onUpdate('cascade')->onDelete('cascade');
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
