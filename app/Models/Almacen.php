<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacen';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idTienda',
      'deleted',
      
    ];

    public function tienda() {
        return $this->belongsTo('App\Models\Tienda','idTienda','id');
    }
    
    
}