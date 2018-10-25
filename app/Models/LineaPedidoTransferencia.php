<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaPedidoTransferencia extends Model
{
    protected $table = 'lineaPedidoTransferencia';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idProducto',
      'cantidad',
      'deleted',
      
    ];
    public function pedidoTransferencia() {
        return $this->belongsTo('App\Models\PedidoTransferencia','idPedidoTransferencia','id');
    }
    
}