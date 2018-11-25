<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devolucion';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'descripcion',
        'monto',
        'idComprobantePago',
        'idUsuario', //cajero de devoluciones
        'idPersonaNatural', //cliente natural
        'idPersonaJuridica', //cliente juridico
        'deleted'   
    ];

    public function comprobantePago() {
        return $this->belongsTo('App\Models\ComprobantePago','idComprobantePago','id');
    }

    public function usuario() {
        return $this->belongsTo('App\Models\Usuario','idUsuario','idPersonaNatural');
    }

    public function personaNatural() {
        return $this->belongsTo('App\Models\PersonaNatural','idPersonaNatural','id');
    }

    public function personaJuridica() {
        return $this->belongsTo('App\Models\PersonaJuridica','idPersonaJuridica','id');
    }

    public function lineasDeVenta() {
        return $this->hasMany('App\Models\LineaDeVenta','idDevolucion','id');
    }
}
