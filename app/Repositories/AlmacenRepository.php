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

    
    
   

    public function getAlmacenCentral(){
        return $this->model->where('nombre','Central')->first();
    }

   
    
    
}