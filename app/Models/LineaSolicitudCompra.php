<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaSolicitudCompra extends Model
{
    protected $table = 'lineaSolicitudDeCompra';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'cantidad',
        'idSolicitudDeCompra',
        'idProducto',
        'idProveedor',
        'deleted'
    ];

    public function solicitudCompra(){
        return $this->belongsTo('App\Models\SolicitudCompra', 'idSolicitudDeCompra', 'id');
    }

    public function proveedor() {
        return $this->belongsTo('App\Models\Proveedor','idProveedor','id');
    }

    public function producto(){
        return $this->belongsTo('App\Models\Producto', 'idProducto', 'id');
    }

    public function lineasPedidoTransferencia(){
    
        return $this->hasMany('App\Models\LineaPedidoTransferencia','idLineaSolicitudCompra','id');
       
    }
}
