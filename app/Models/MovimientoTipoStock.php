<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoTipoStock extends Model
{
    protected $table = 'movimientoTipoStock';
    public $timestamps = true;
  
    protected $fillable = [
      'id',
      'idProducto',
      'idAlmacen',
      'idTipoStock',
      'idUsuario',
      'cantidad',
      'signo',
      'deleted',
      'tipo'
    ];

    public function producto(){
        return $this->belongsTo('App\Models\Producto', 'idProducto', 'id');
    }
    
    public function almacen(){
        return $this->belongsTo('App\Models\Almacen', 'idAlmacen', 'id');
    }

    public function tipoStock(){
        return $this->belongsTo('App\Models\TipoStock', 'idTipoStock', 'id');
    }

    public function usuario(){
        return $this->belongsTo('App\Models\Usuario', 'idUsuario', 'idPersonaNatural');
    }
}

/*
                   ▄              ▄
                  ▌▒█           ▄▀▒▌
                  ▌▒▒█        ▄▀▒▒▒▐
                 ▐▄▀▒▒▀▀▀▀▄▄▄▀▒▒▒▒▒▐
               ▄▄▀▒░▒▒▒▒▒▒▒▒▒█▒▒▄█▒▐
             ▄▀▒▒▒░░░▒▒▒░░░▒▒▒▀██▀▒▌
            ▐▒▒▒▄▄▒▒▒▒░░░▒▒▒▒▒▒▒▀▄▒▒▌
            ▌░░▌█▀▒▒▒▒▒▄▀█▄▒▒▒▒▒▒▒█▒▐
           ▐░░░▒▒▒▒▒▒▒▒▌██▀▒▒░░░▒▒▒▀▄▌
           ▌░▒▄██▄▒▒▒▒▒▒▒▒▒░░░░░░▒▒▒▒▌
          ▌▒▀▐▄█▄█▌▄░▀▒▒░░░░░░░░░░▒▒▒▐
          ▐▒▒▐▀▐▀▒░▄▄▒▄▒▒▒▒▒▒░▒░▒░▒▒▒▒▌
          ▐▒▒▒▀▀▄▄▒▒▒▄▒▒▒▒▒▒▒▒░▒░▒░▒▒▐
           ▌▒▒▒▒▒▒▀▀▀▒▒▒▒▒▒░▒░▒░▒░▒▒▒▌
           ▐▒▒▒▒▒▒▒▒▒▒▒▒▒▒░▒░▒░▒▒▄▒▒▐
            ▀▄▒▒▒▒▒▒▒▒▒▒▒░▒░▒░▒▄▒▒▒▒▌
              ▀▄▒▒▒▒▒▒▒▒▒▒▄▄▄▀▒▒▒▒▄▀
                ▀▄▄▄▄▄▄▀▀▀▒▒▒▒▒▄▄▀
                   ▒▒▒▒▒▒▒▒▒▒▀▀
 */