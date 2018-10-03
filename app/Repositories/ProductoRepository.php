<?php
namespace App\Repositories;
use App\Models\Producto;
	
class ProductoRepository extends BaseRepository {
    /**
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @return void
     */
    public function __construct(Producto $producto) 
    {
        $this->model = $producto;
        
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
    
}