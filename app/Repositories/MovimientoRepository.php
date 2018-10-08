<?php
namespace App\Repositories;
use App\Models\Movimiento;
	
class MovimientoRepository extends BaseRepository{
    /**
     * Create a new MovimientoRepository instance.
     * @param  App\Models\Movimiento $movimiento
     * @return void
     */
    public function __construct(Movimiento $movimiento) 
    {
        $this->model = $movimiento;
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Movimiento
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }
}