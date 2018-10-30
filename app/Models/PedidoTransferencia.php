<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoTransferencia extends Model
{
    protected $table = 'pedidoDeTransferencia';
    public $timestamps = true;
    
  
    protected $fillable = [
      'id',
      'idUsuario',
      'idAlmacenO',
      'idAlmacenD',
      'descripcion',
      'deleted',
      
    ];

    public function usuario(){
        return $this->belongsTo('App\Models\Usuario','idUsuario','idPersonaNatural');
    }

    public function almacenOrigen() {
        return $this->belongsTo('App\Models\Almacen','idAlmacenO','id');
    }

    public function almacenDestino() {
        return $this->belongsTo('App\Models\Almacen','idAlmacenD','id');
    }

    public function transferencia() {
        return $this->hasOne('App\Models\Transferencia','id','id');
      }

    public function lineasPedidoTransferencia() {
        return $this->hasMany('App\Models\LineaPedidoTransferencia','idPedidoTransferencia','id');
    }
    
    
}