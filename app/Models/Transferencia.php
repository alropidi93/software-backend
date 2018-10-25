<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $table = 'transferencia';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idPedidoTransferencia',
      'fecha',
      'estado',
      'observacion',
      'deleted',
      
    ];
    public function pedidoTransferencia() {
        return $this->belongsTo('App\Models\PedidoTransferencia','idPedidoTransferencia','id');
    }
}