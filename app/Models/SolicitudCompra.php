<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCompra extends Model
{
    protected $table = 'solicitudDeCompra';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'fecha',
        'idTienda',
        'deleted'
    ];

    public function lineasSolicitudCompra(){
        return $this->hasMany('App\Models\LineaSolicitudCompra');
    }

    public function tienda() {
        return $this->belongsTo('App\Models\Tienda','idTienda','id');   
    }
}
