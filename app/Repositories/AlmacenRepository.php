<?php
namespace App\Repositories;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\TipoStock;
use App\Models\Usuario;
use App\Http\Helpers\Algorithm;

	
class AlmacenRepository extends BaseRepository{
    
 
    protected $producto;
    protected $tipoStock;
    protected $productos;
    protected $jefeAlmacenCentral;
    // protected $productoRepository;
   
    public function __construct(Almacen $almacen,Producto $producto, TipoStock $tipoStock ,Usuario $jefeAlmacenCentral, ProductoRepository $productoRepository=null)
    {
        $this->model = $almacen;
        $this->producto = $producto;
        $this->tipoStock = $tipoStock;
        $this->jefeAlmacenCentral = $jefeAlmacenCentral;
        $this->productoRepository = $productoRepository;

    }
    public function setJefeAlmacenCentralModel($usuario){
        $this->jefeAlmacenCentral = $usuario;       
    }

    public function loadProductosRelationship($almacen=null){
        
        if (!$almacen){
                  

            $this->model = $this->model->load([
                'productos'=>function($query){
                    $query->where('producto.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'productos'=>function($query){
                    $query->where('producto.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        if ($this->model->productos){
            $this->productos = $this->model->productos;
        }
        
    }

    public function getProductosNoStockedosByOwnModelAndKeyTipoStock($keyTipoStock){
        /*$productos =  Producto::whereHas('almacenes')->get();*/

        /*
        $productos =  Producto::where('deleted',false)->whereHas('almacenes',function($query) use ($almacen){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false);
        })->get();
        */        
        
        /*
        $productos =  Producto::where('deleted',false)->whereHas('almacenes',function($query) use ($almacen){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false);
            
        })->whereHas('tipoStocks',function ($query2) use ($tipo){
            $query2->where('tipoStock.deleted',false)->where('tipoStock.key',$tipo);
        })->get();
        */

        /*
        $productos =  Producto::where('deleted',false)->whereHas('almacenes',function($query) use ($almacen){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false)->where('productoxalmacen.deleted', false);
            
        })
        ->whereHas('tipoStocks',function ($query2) use ($keyTipoStock){
            $query2->where('tipoStock.deleted',false)->where('tipoStock.key',$keyTipoStock)->where('productoxalmacen.deleted', false);
        })->get();
        */

        

       /* 
       $productos =  $this->producto->where('deleted',false)->whereHas('almacenes',function($query) use ($almacen, $keyTipoStock){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false)
            ->where('productoxalmacen.deleted', false)->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                                                    ->where('tipoStock.key',$keyTipoStock)->where('tipoStock.deleted',false);
            
        })->get();

       */
        
        $almacen = $this->model;
        $tipoStock = $this->tipoStock->where('key',$keyTipoStock)->where('deleted',false)->first();
        if (!$tipoStock){
            return [];
        }


        $productos =  $this->producto->where('deleted',false)->whereDoesntHave('almacenes',function($query) use ($almacen, $keyTipoStock){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false)
            ->where('productoxalmacen.deleted', false)->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                                                    ->where('tipoStock.key',$keyTipoStock)->where('tipoStock.deleted',false);
            
        })->get();

        return $productos;
     
    

    }
    
   

    public function getAlmacenCentral(){
        return $this->model->where('nombre','Central')->first();
    }

    public function attachProductoStockRndByTipoStock($producto,$keyTipoStock){
        $tipoStock = $this->tipoStock->where('key',$keyTipoStock)->where('deleted',false)->first();
        if (!$tipoStock){
            throw new \Exception('No se encontro Tipo Stock');
        }
        $cantidadRnd = Algorithm::getRndIntegerNumber(100);

        $this->model->productos()->save($producto , ['idTipoStock'=>$tipoStock->id,'cantidad'=>$cantidadRnd,'deleted'=>false] );
        $this->model->save();
    }

    public function attachProductoStockNuevoByTipoStock($producto,$keyTipoStock){
        $tipoStock = $this->tipoStock->where('key',$keyTipoStock)->where('deleted',false)->first();
        if (!$tipoStock){
            throw new \Exception('No se encontro Tipo Stock');
        }
        
        $cantidad = 0;

        $this->model->productos()->save($producto , ['idTipoStock'=>$tipoStock->id,'cantidad'=>$cantidad,'deleted'=>false] );
        $this->model->save();
    }
    
    public function loadJefeDeAlmacenCentralRelationship($tienda=null){
        if (!$tienda){
            $this->model->load('jefeDeAlmacenCentral');
        }
        else{
            $tienda->load('jefeDeAlmacenCentral');
        }
        
    }

    public function attachJefeAlmacenCentral(){
           
        $this->model->jefeDeAlmacenCentral()->associate($this->jefeAlmacenCentral);
        $this->model->save();

      
    }

    public function obtenerIdAlmacenConIdTienda($idTienda){
        return $this->model->where('idTienda',$idTienda)->where('deleted',false)->first();
    }

    public function listarConStockMinimoDeAlmacen($almacen){
        $productos = $almacen->productos()
        ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
        ->where('tipoStock.key',1)
        ->where('tipoStock.deleted',false)
        ->where('producto.habilitado',true)
        ->where('producto.deleted',false)
        ->wherePivot('deleted',false)
        ->whereRaw('producto."stockMin" >= productoxalmacen.cantidad')
        ->get();

        foreach($productos as $key => $producto){
            $this->productoRepository->loadTipoProductoRelationship($producto);
            $this->productoRepository->loadUnidadMedidaRelationship($producto);
            $this->productoRepository->loadCategoriaRelationship($producto);
        }
      
        return $productos;
    }

    public function listarConStockDeAlmacen($almacen){
        $productos = $almacen->productos()
        ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
        ->where('tipoStock.key',1)
        ->where('tipoStock.deleted',false)
        ->where('producto.deleted',false)
        ->where('producto.habilitado',true)
        ->wherePivot('deleted',false)
        ->get();

        foreach($productos as $key => $producto){
            $this->productoRepository->loadTipoProductoRelationship($producto);
            $this->productoRepository->loadUnidadMedidaRelationship($producto);
            $this->productoRepository->loadCategoriaRelationship($producto);
        }
      
        return $productos;
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

