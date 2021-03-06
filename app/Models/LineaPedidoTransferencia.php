<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaPedidoTransferencia extends Model
{
    protected $table = 'lineaPedidoDeTransferencia';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idProducto',
      'cantidad',
      'deleted',
      'idPedidoTransferencia',
      'idLineaSolicitudCompra'
      
    ];
    public function pedidoTransferencia() {
        return $this->belongsTo('App\Models\PedidoTransferencia','idPedidoTransferencia','id');
    }

    public function producto() {
        return $this->belongsTo('App\Models\Producto','idProducto','id');
    }

    public function lineaSolicitudCompra() {
        return $this->belongsTo('App\Models\LineaSolicitudCompra','idLineaSolicitudCompra','id');
    }
    
}