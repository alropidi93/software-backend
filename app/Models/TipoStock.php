<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoStock extends Model
{
    protected $table = 'tipoStock';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'tipo',
      'key',
      'deleted'
    ];

    // public function movimientoTipoStock(){
    //   return $this->belongsTo('App\Models\MovimientoTipoStock', 'idTipoStock', 'id');
    // }
}