<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTiendafkUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario', function (Blueprint $table) {
            
            
            $table->foreign('idTienda')->references('id')->on('tienda')->onUpdate('cascade')->onDelete('cascade');
        });
      
    }
}
