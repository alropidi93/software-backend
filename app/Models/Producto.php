<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'stockMin',
      'descripcion',
      'idTipoProducto',
      'idUnidadMedida',
      'categoria',
      'precio',
      'deleted'
    ];
}
