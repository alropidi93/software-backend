<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUbicacionToTienda extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tienda', function (Blueprint $table) {
            $table->double('latitud')->nullable();
            $table->double('longitud')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tienda', function (Blueprint $table) {
            $table->dropColumn('latitud');
            $table->dropColumn('longitud');
        });
    }
}
