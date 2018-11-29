<?php
namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Descuento;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Categoria;
use Carbon\Carbon;

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
    public function obtenerDescuentosVigentes(){        
        $lista = $this->model-> whereDate('fechaIni', '<=', Carbon::now())-> whereDate('fechaFin', '>=', Carbon::now())->where('deleted',false)->get();         
        return $lista;
    }

    public function obtenerProductosSinDescuentoDeTienda($id){
        //obtener descuentos
        $descuentos = DB::table('descuento')->where('deleted', false)->get();

        //obtener descuentos de la tienda indicada
        $descuentosTienda = array();
        foreach($descuentos as $key => $descuento){
            if($descuento->idTienda==$id){
                $descuentosTienda[]=$descuento;
            }
        }
        
        //ver que productos no estan en la lista de descuentos y agregarlos
        $productos = DB::table('producto')->where('deleted', false)->get();
        $listaProductos = array();
        foreach($productos as $key => $producto){
            $estaProducto = false;
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($producto->id == $descuentoTienda->idProducto){
                    $estaProducto = true;
                }
            }
            if(!$estaProducto){
                $listaProductos[] = $producto;
            }
        }
        return $listaProductos;
    }

    public function obtenerProductosConDescuentoDeTienda($id){
        //obtener descuentos
        $descuentos = DB::table('descuento')->where('deleted', false)->get();

        //obtener descuentos de la tienda indicada
        $descuentosTienda = array();
        foreach($descuentos as $key => $descuento){
            if($descuento->idTienda==$id){
                $descuentosTienda[]=$descuento;
            }
        }
        
        //ver que productos estan en la lista de descuentos y agregarlos
        $productos = DB::table('producto')->where('deleted', false)->get();
        $listaProductos = array();
        foreach($productos as $key => $producto){
            // $estaProducto = false;
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($producto->id == $descuentoTienda->idProducto){
                    $listaProductos[] = $producto;
                    break;
                }
            }
        }
        return $listaProductos;
    }
    public function obtenerCategoriasSinDescuentoDeTienda($id){
        //obtener descuentos
        $descuentos = DB::table('descuento')->where('deleted', false)->get();

        //obtener descuentos de la tienda indicada
        $descuentosTienda = array();
        foreach($descuentos as $key => $descuento){
            if($descuento->idTienda==$id){
                $descuentosTienda[]=$descuento;
            }
        }
        
        //encontrar productos con descuentos para luego retirar sus categorias de la lista 
        $productos = DB::table('producto')->where('deleted', false)->get();
        $listaProductosConDescuento = array();
        foreach($productos as $key => $producto){
            $estaProducto = false;
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($producto->id == $descuentoTienda->idProducto){
                    $estaProducto = true;
                }
            }
            if($estaProducto){
                $listaProductosConDescuento[] = $producto;
            }
        }
        $categorias= DB::table('categoria')->where('deleted', false)->get();
        $listaCategorias=array();
        foreach($categorias as $key => $categoria){
            $estaCategoria = false;
            $estaProductoDeEstaCategoria= false;
            foreach($listaProductosConDescuento as $key => $producto){
                if($categoria->id == $producto->idCategoria){
                    $estaProductoDeEstaCategoria = true;
                }
            }
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($categoria->id == $descuentoTienda->idCategoria){
                    $estaCategoria = true;
                }
            }
            if(!$estaCategoria and !$estaProductoDeEstaCategoria ){
                $listaCategorias[] = $categoria;
            }
        }
        return $listaCategorias;
    }
    public function obtenerProductosConAlgunDescuento($idTienda){
        //obtener descuentos
        $descuentos = DB::table('descuento')->where('deleted', false)->get();

        //obtener descuentos de la tienda indicada
        $descuentosTienda = array();
        foreach($descuentos as $key => $descuento){
            if($descuento->idTienda==$idTienda){
                $descuentosTienda[]=$descuento;
            }
        }
        
        //encontrar productos con descuentos para luego retirar sus categorias de la lista 
        $productos = DB::table('producto')->where('deleted', false)->get();
        $listaProductosConDescuento = array();
        foreach($productos as $key => $producto){
            $estaProducto = false;
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($producto->id == $descuentoTienda->idProducto){
                    $estaProducto = true;
                }
            }
            if($estaProducto){
                $listaProductosConDescuento[] = $producto;
            }
        }
        $categorias= DB::table('categoria')->where('deleted', false)->get();   
        foreach($categorias as $key => $categoria){
            $estaCategoria = false;
           //  $estaProductoDeEstaCategoria= false;
           //  foreach($listaProductosConDescuento as $key => $producto){
           //      if($categoria->id == $producto->idCategoria){
           //          $estaProductoDeEstaCategoria = true;
           //      }
           //  }
            foreach($descuentosTienda as $key => $descuentoTienda){
                if($categoria->id == $descuentoTienda->idCategoria){
                    $estaCategoria = true;
                }
            }
            if($estaCategoria){
               foreach($productos as $key => $producto){
                   if($categoria->id == $producto->idCategoria){
                       $listaProductosConDescuento[] = $producto;
                   }
               }
            }
        }
        return $listaProductosConDescuento;
   }
}