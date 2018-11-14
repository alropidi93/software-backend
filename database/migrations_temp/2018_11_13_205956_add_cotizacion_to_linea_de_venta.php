<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCotizacionToLineaDeVenta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lineaDeVenta', function($table) {
            $table->foreign('idCotizacion')->references('id')->on('cotizacion')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lineaDeVenta', function($table) {
            $table->dropColumn('idCotizacion');
        });
    }
}
