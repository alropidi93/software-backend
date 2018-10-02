<?php
namespace App\Repositories;
use App\Models\Tienda;
	
class TiendaRepository extends BaseRepository {
    /**
     * The Usuario instance.
     *
     * @var App\Models\Usuario
     */
    protected $jefeDeTienda;
    /**
     * The Usuario instance.
     *
     * @var App\Models\Usuario
     */
    protected $jefeDeAmacen;
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Tienda $tienda) 
    {
        $this->model = $tienda;
        
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