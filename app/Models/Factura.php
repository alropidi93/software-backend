<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'factura';
    protected $primaryKey = 'idComprobantePago';
    public $timestamps = true;
    public $incrementing = false;
  
    protected $fillable = [
      'idComprobantePago',
      'idCliente',
      'igv',
      'deleted'     
    ];

    public function comprobantePago() {
      return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }
    public function personaJuridica() {
      return $this->belongsTo('App\Models\PersonaJuridica','idCliente','id');
    }
}
