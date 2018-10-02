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
<<<<<<< HEAD
      'idUnidadMedida',
=======
      'idUnidadMedia',
>>>>>>> f947215e6a2b523eeb6cfc10efd07bf476554a86
      'categoria',
      'precio',
      'deleted'
    ];
}
