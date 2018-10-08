<?php
namespace App\Repositories;
use App\Models\Movimiento;
	
class MovimientoRepository extends BaseRepository{
    /**
     * Create a new MovimientoRepository instance.
     * @param  App\Models\Movimiento $movimiento
     * @return void
     */
    public function __construct(Tienda $movimiento) 
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

    public function loadMovimientoRelationship($movimento=null){
        if (!$movimiento){
        
            

            $this->model->load(['usuario' => function ($query) {
                $query->where('deleted', false); // hacemos referencia al metodo usuario() en el modelo Movimiento y solo cargamos los que tengan el campo 'deleted' igual a false
            }]);
        }
        else{
            
            $movimiento->load(['usuario' => function ($query) {
                $query->where('deleted', false); // hacemos referencia al metodo usuario() en el modelo Movimiento y solo cargamos los que tengan el campo 'deleted' igual a false
            }]);
        }
    }
}