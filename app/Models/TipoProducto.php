<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipoProducto';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'tipo',
      'deleted'
    ];
}
