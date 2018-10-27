<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoXProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productoxproveedor', function (Blueprint $table) {
            
        
            $table->integer('idProducto');
            $table->integer('idProveedor');
        
            $table->boolean('deleted');
            $table->timestamps();

            $table->primary(['idProducto','idProveedor']);
            $table->foreign('idProducto')->references('id')->on('producto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idProveedor')->references('id')->on('proveedor')->onUpdate('cascade')->onDelete('cascade');

      
           
     
         
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productoxproveedor');
    }
}
