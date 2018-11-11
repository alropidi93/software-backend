<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonaJuridicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personaJuridica', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ruc',12)->unique();
            $table->string('email',100);
            $table->string('razonSocial',200);
            $table->string('direccion',500);
            $table->string('telefono',50);
            $table->boolean('deleted');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persona_juridica');
    }
}
