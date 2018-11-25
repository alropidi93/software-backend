<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudProducto extends Model
{
    protected $table = 'solicitudProducto';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',      
      'descripcion',
      'cantidad',
      'deleted',
      
    ];
    
}
