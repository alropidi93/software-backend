<?php
namespace App\Repositories;
use App\Models\Descuento;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Categoria;

class DescuentoRepository extends BaseRepository{
    protected $tienda;
    protected $producto;
    protected $categoria;

    public function __construct(Descuento $descuento=null, Tienda $tienda=null, Producto $producto=null, Categoria $categoria=null)
    {
        $this->model = $descuento;
        $this->tienda = $tienda;
        $this->producto = $producto;
        $this->categoria = $categoria;
    }

   
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);    
    }

    public function setTiendaModel($tienda){
        $this->tienda = $tienda;
    }

    public function setProductoModel($producto){
        $this->producto = $producto;
    }

    public function setCategoriaModel($categoria){
        $this->categoria = $categoria;
    }

    public function loadTiendaRelationship($descuento=null){
        if (!$descuento){
            $this->model = $this->model->load([
                'tienda'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{
            $this->model =$descuento->load([
                'tienda'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->tienda){
            $this->tienda = $this->model->tienda;
        }
    }

    public function loadProductoRelationship($descuento=null){
        if (!$descuento){
            $this->model = $this->model->load([
                'producto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{
            $this->model =$descuento->load([
                'producto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->producto){
            $this->producto = $this->model->producto;
        }
    }

    public function loadCategoriaRelationship($descuento=null){
        if (!$descuento){
            $this->model = $this->model->load([
                'categoria'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{
            $this->model =$descuento->load([
                'categoria'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->categoria){
            $this->categoria = $this->model->categoria;
        }
    }
}