<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->integer('idComprobantePago')->primary();
            $table->integer('idCliente')->nullable();
            $table->double('igv');
            $table->boolean('deleted');
            
            $table->timestamps();
            
            $table->foreign('idComprobantePago')->references('id')->on('comprobantePago')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idCliente')->references('id')->on('personaJuridica')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factura');
    }
}
