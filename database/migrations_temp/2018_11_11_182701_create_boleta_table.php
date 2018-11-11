<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoletaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boleta', function (Blueprint $table) {
            $table->integer('idCliente')->primary();
            $table->double('igv');
            $table->boolean('deleted');
            
            $table->timestamps();
                        
            $table->foreign('idCliente')->references('id')->on('personaNatural')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boleta');
    }
}
