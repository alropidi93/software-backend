<?php
namespace App\Repositories;

use App\Models\LineaDeVenta;
use App\Models\Producto;
	
class LineaDeVentaRepository extends BaseRepository{
    protected $producto;
    protected $lineaDeVenta;

    public function __construct(LineaDeVenta $lineaDeVenta, Producto $producto){
        $this->model = $lineaDeVenta;
        $this->producto = $producto;
    }

    public function setProductoModel($producto){
        $this->producto = $producto;
    }

    public function attachProducto($producto){
        $this->model->producto()->save($producto , ['deleted'=>false] );
        $this->model->save();
    }

    public function loadProductoRelationship($lineaSolicitudCompra=null){
        if (!$lineaSolicitudCompra){
            $this->model = $this->model->load([
                'producto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{
            $this->model =$lineaSolicitudCompra->load([
                'producto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->producto){
            $this->producto = $this->model->producto;
        }
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }
}