<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaSolicitudCompra extends Model
{
    protected $table = 'lineaSolicitudDeCompra';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'idProducto',
        'cantidad',
        'idSolicitudDeCompra',
        'idProveedor',
        'deleted'
    ];

    public function solicitudCompra(){
        return $this->belongsTo('App\Model\SolicitudCompra', 'idSolicitudDeCompra', 'id');
    }

    public function proveedor(){
        return $this->hasOne('App\Model\Proveedor', 'idProveedor', 'id');
    }

    public function producto(){
        return $this->hasOne('App\Model\Producto', 'idProducto', 'id');
    }
}
