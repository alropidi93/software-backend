<?php
namespace App\Repositories;
use App\Models\Producto;
use App\Models\TipoProducto;
use App\Models\UnidadMedida;
	
class ProductoRepository extends BaseRepository {
    protected $tipoProducto;
    /**
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @return void
     */
    public function __construct(Producto $producto, TipoProducto $tipoProducto, UnidadMedida $unidadMedida) 
    {
        $this->model = $producto;
        $this->tipoProducto = $tipoProducto;
        $this->unidadMedida = $unidadMedida;
        
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

    public function loadUnidadMedidaModel($producto=null){
      


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

    public function loadTipoProductoModel($producto=null){
      


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
    
}