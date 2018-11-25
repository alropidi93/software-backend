<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevolucionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolucion', function (Blueprint $table) {
            $table->increments('id');
            $table->double('monto');
            $table->text('descripcion');
            $table->integer('idComprobantePago')->nullable();
            $table->integer('idUsuario')->nullable();
            $table->integer('idPersonaNatural')->nullable();
            $table->integer('idPersonaJuridica')->nullable();
            $table->boolean('deleted');
            $table->timestamps();

            $table->foreign('idComprobantePago')->references('id')->on('comprobantePago')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idUsuario')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idPersonaNatural')->references('id')->on('personaNatural')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idPersonaJuridica')->references('id')->on('personaJuridica')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolucion');
    }
}
