<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    protected $table = 'comprobanteDePago';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idCajero',
      'subtotal',
      'deleted'     
    ];

    public function usuario() {
      return $this->belongsTo('App\Models\Usuario','idCajero','id');
    }
    
}
