<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('estado',30);
            $table->text('observacion')->nullable();
            $table->text('respuesta')->nullable();
            
            $table->boolean('deleted');
            $table->timestamps();

          
            $table->foreign('id')->references('id')->on('pedidoDeTransferencia')->onUpdate('cascade')->onDelete('cascade');
            
                
           
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transferencia');
    }
}
