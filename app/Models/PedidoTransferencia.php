<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoTransferencia extends Model
{
    protected $table = 'pedidoTransferencia';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idUsuario',
      'idAlmacenO',
      'idAlmacenD',
      'descripcion',
      'deleted',
      
    ];
    public function lineaPedidoTransferencia() {
        return $this->hasMany('App\Models\LineaPedidoTransferencia','idLineaPedidoTransferencia','id');
    }
    public function almacen() {
        return $this->belongsToMany('App\Models\Almacen','idAlmacen','id');
    }
    
}