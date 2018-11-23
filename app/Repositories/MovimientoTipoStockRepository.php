<?php
namespace App\Repositories;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Usuario;
use App\Models\TipoStock;
use App\Models\MovimientoTipoStock;
	
class MovimientoTipoStockRepository extends BaseRepository {
    protected $usuario;
    protected $producto;
    protected $almacen;
    protected $tipoStock;

    public function __construct(MovimientoTipoStock $movimientoTipoStock, Usuario $usuario=null, Producto $producto=null, Almacen $almacen=null, TipoStock $tipoStock=null){
        $this->model = $movimientoTipoStock;
        $this->usuario = $usuario;
        $this->producto = $producto;
        $this->almacen = $almacen;
        $this->tipoStock = $tipoStock;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function getUsuarioById($idUsuario){   
        return $this->usuario->where('idPersonaNatural',$idUsuario)->where('deleted',false)->first();
    }

    public function loadUsuarioRelationship($movimiento=null){
        if (!$movimiento){
            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                },
                'usuario.personaNatural' => function ($query) {
                    $query->where('personaNatural.deleted', false);
                }
            ]);
        }else{   
            $this->model =$movimiento->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                },
                'usuario.personaNatural' => function ($query) {
                    $query->where('personaNatural.deleted', false);
                }
            ]);
        }
        if ($this->model->usuario){
            $this->usuario = $this->model->usuario;
        }   
    }

    public function loadProductoRelationship($movimiento=null){
        if (!$movimiento){
            $this->model->load('producto');
        }else{
            $movimiento->load('producto');
        }
    }

    public function loadAlmacenRelationship($movimiento=null){
        if (!$movimiento){
            $this->model->load('almacen');
        }else{
            $movimiento->load('almacen');
        }
    }

    public function loadTipoStockRelationship($movimiento=null){
        if (!$movimiento){
            $this->model->load('tipoStock');
        }else{
            $movimiento->load('tipoStock');
        }
    }

    public function setUsuarioModel($usuario){
        $this->usuario = $usuario;
    }

    public function setProductoModel($producto){
        $this->producto = $producto;
    }

    public function setAlmacenModel($almacen){
        $this->almacen = $almacen;
    }

    public function setTipoStockModel($tipoStock){
        $this->tipoStock = $tipoStock;
    }

    public function obtenerUsuarioModel(){
        return $this->usuario;
    }
}