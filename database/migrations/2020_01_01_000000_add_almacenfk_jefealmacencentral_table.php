<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlmacenfkJefealmacencentralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('almacen', function (Blueprint $table) {
            $table->integer('idJefeAlmacenCentral')->nullable();
            $table->foreign('idJefeAlmacenCentral')->references('idPersonaNatural')->on('usuario')->onUpdate('cascade')->onDelete('cascade');
        });
      
    }
}
