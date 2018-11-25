<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDevolucionToLineaDeVenta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lineaDeVenta', function (Blueprint $table) {
            $table->integer('idDevolucion')->nullable();
            $table->double('subtotalLinea')->nullable();
            $table->foreign('idDevolucion')->references('id')->on('devolucion')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lineaDeVenta', function (Blueprint $table) {
            $table->dropColumn('idDevolucion');
            $table->dropColumn('subtotalLinea');
        });
    }
}
