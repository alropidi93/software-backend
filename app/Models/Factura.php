<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'factura';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idComprobantePago',
      'idCliente',
      'igv',
      'deleted'     
    ];

    public function comprobantePago() {
      return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }
    public function cliente() {
      return $this->belongsTo('App\Models\PersonaJuridica','idCliente','id');
    }
}
