<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            

            
            $table->integer('idCategoria')->nullable();
            $table->dropColumn('categoria');
            $table->foreign('idCategoria')->references('id')->on('categoria')->onUpdate('cascade')->onDelete('cascade');
           
     
         
        });
      
    }


}
