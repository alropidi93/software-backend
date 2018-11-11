<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    protected $table = 'boleta';
    public $timestamps = true;

    protected $fillable = [
      'idComprobantePago',
      'idCliente',
      'igv',
      'deleted'
    ];

    public function comprobantePago() {
      return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }
    public function personaNatural() {
      return $this->belongsTo('App\Models\PersonaNatural','idCliente','id');
    }
}
