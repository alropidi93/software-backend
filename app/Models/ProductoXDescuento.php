<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductoXDescuento extends Pivot
{

    protected $table = 'productoxdescuento';
    public $timestamps = true;

    public $incrementing = false;
    protected $primaryKey = ['idTienda', 'idProducto','idDescuento'];
    protected $fillable = ['idTienda','idProducto','idDescuento','deleted'];
    
    public function __contruct(){
        $this->descuento;
        $this->tienda;
      }
      
  }

   