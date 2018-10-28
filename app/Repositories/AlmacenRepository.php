<?php
namespace App\Repositories;
use App\Models\Almacen;
use App\Models\Producto;

	
class AlmacenRepository extends BaseRepository{
    
 
    protected $producto;
    protected $productos;
   
    public function __construct(Almacen $almacen,Producto $producto )
    {
        $this->model = $almacen;
        $this->producto = $producto;
        
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
        ->whereHas('tipoStocks',function ($query2) use ($tipo){
            $query2->where('tipoStock.deleted',false)->where('tipoStock.key',$tipo)->where('productoxalmacen.deleted', false);
        })->get();
        */
        $almacen = $this->model;
        $productos =  $this->producto->where('deleted',false)->whereDoesntHave('almacenes',function($query) use ($almacen){
            $query->where('almacen.id',$almacen->id)->where('almacen.deleted',false)->where('productoxalmacen.deleted', false);
            
        })
        ->whereDoesntHave('tipoStocks',function ($query2) use ($keyTipoStock){
            $query2->where('tipoStock.deleted',false)->where('tipoStock.key',$keyTipoStock)->where('productoxalmacen.deleted', false);
        })->get();
        

        return $productos;

    }
    
   

    public function getAlmacenCentral(){
        return $this->model->where('nombre','Central')->first();
    }

   
    
    
}