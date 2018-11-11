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

    public function cajero() {
      return $this->belongsTo('App\Models\Usuario','idCajero','id');
    }
    
}
