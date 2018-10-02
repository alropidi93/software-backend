<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadDeMedida extends Model
{
    protected $table = 'unidadDeMedida';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'unidad',
      'deleted'
    ];
}
