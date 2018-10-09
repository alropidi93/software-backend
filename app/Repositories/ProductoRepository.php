<?php
namespace App\Repositories;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\TipoProducto;
use App\Models\UnidadMedida;
	
class ProductoRepository extends BaseRepository {
    protected $tipoProducto;
    protected $unidadMedida;
    protected $proveedor;
    protected $proveedores;
    /**
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @param  App\Models\Proveedor $proveedor
     * @return void
     */
    public function __construct(Producto $producto, TipoProducto $tipoProducto,UnidadMedida $unidadMedida ,Proveedor $proveedor) 
    {
        $this->model = $producto;
        $this->tipoProducto = $tipoProducto;
        $this->unidadMedida = $unidadMedida;
        $this->proveedor = $proveedor;
        
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;

        return $this->model = $this->model->create($dataArray);
        
    }

    public function loadUnidadMedidaRelationship($producto=null){
      


        if (!$producto){
        
            

            $this->model = $this->model->load([
                'unidadMedida'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'unidadMedida'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->unidadMedida){
            $this->unidadMedida = $this->model->unidadMedida;
        }
    }

    public function loadTipoProductoRelationship($producto=null){
      


        if (!$producto){
        
            

            $this->model = $this->model->load([
                'tipoProducto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'tipoProducto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->tipoProducto){
            $this->tipoProducto = $this->model->tipoProducto;
        }
    }

    public function loadProveedoresRelationship($producto=null){
      


        if (!$producto){
                  

            $this->model = $this->model->load([
                'proveedores'=>function($query){
                    $query->where('proveedor.deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'proveedores'=>function($query){
                    $query->where('proveedor.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        if ($this->model->proveedores){
            $this->proveedores = $this->model->proveedores;
        }
    }

    public function buscarPorTipo($value){
        
               
        return $this->model->join('tipoProducto', 'tipoProducto.id', '=', 'producto.idTipoProducto')
            ->whereRaw("lower(tipo) like ? ",'%'.$value.'%')->where('tipoProducto.deleted','=',false)->get();
        }
    
}