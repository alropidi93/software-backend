<?php
namespace App\Repositories;

use App\Models\LineaSolicitudCompra;
use App\Models\Proveedor;
use App\Models\Producto;
	
class LineaSolicitudCompraRepository extends BaseRepository{
    protected $proveedor;
    protected $producto;

    public function __construct(LineaSolicitudCompra $lineaSolicitudCompra, Proveedor $proveedor, Producto $producto){
        $this->model = $lineaSolicitudCompra;
        $this->proveedor = $proveedor;
        $this->producto = $producto;
    }

    public function setProveedorModel($proveedor){
        $this->proveedor = $proveedor;
    }

    public function setProductoModel($producto){
        $this->producto = $producto;
    }

    public function attachProveedor($proveedor){
        $this->model->proveedor()->save($proveedor , ['deleted'=>false] );
        $this->model->save();
    }

    public function attachProducto($producto){
        $this->model->producto()->save($producto , ['deleted'=>false] );
        $this->model->save();
    }

    public function loadProveedorRelationship($lineaSolicitudCompra=null){
        if (!$lineaSolicitudCompra){
            $this->model = $this->model->load([
                'proveedor'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{
            $this->model =$lineaSolicitudCompra->load([
                'proveedor'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->proveedor){
            $this->proveedor = $this->model->proveedor;
        }
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