<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipoStock', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo',100);
            $table->integer('key')->nullable();
 
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
        Schema::dropIfExists('tipoStock');
    }
}
