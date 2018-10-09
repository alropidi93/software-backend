<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipoUsuario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',200);
            $table->boolean('deleted');
            $table->integer('key')->unique();
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
        Schema::dropIfExists('tipoUsuario');
    }
}
