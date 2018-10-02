<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonaNaturalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personaNatural', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',200);
            $table->string('apellidos',300);
            $table->string('dni',12);
            $table->date('fechaNac');
            $table->char('genero');
            $table->string('email',100);
            $table->string('direccion',500);
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
        Schema::dropIfExists('personaNatural');
    }
}
