<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'nombre',
      'descripcion',
      'deleted',
      
    ];
    
}
