<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidadMedida';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'unidad',
      'deleted'
    ];
}
