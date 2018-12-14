<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoriaXTiendaXDescuento extends Pivot
{
    protected $table = 'categoriaxtiendaxdescuento';
    public $timestamps = true;

    public $incrementing = false;
    protected $primaryKey = ['idTienda', 'idCategoria','idDescuento'];
    protected $fillable = ['idTienda','idCategoria','idDescuento','deleted'];
    
    public function __contruct(){
        $this->descuento;
        $this->tienda;
    }
      
  }

   