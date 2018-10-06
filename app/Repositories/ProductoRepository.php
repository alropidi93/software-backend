<?php
namespace App\Repositories;
use App\Models\Producto;
use App\Models\TipoProducto;
	
class ProductoRepository extends BaseRepository {
    protected $tipoProducto;
    /**
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @return void
     */
    public function __construct(Producto $producto, TipoProducto $tipoProducto) 
    {
        $this->model = $producto;
        $this->tipoProducto = $tipoProducto;
        
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

    public function loadTipoProductoModel($producto=null){
        if ($producto){
            $producto->load('tipoProducto');
        }
        else{
            $this->model->load('tipoProducto');
        }
    }
    
}