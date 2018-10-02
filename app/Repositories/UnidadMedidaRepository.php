<?php
namespace App\Repositories;
use App\Models\UnidadMedida;
	
class UnidadMedidaRepository extends BaseRepository {
    
    /**
     * Create a new UnidadMedidaRepository instance.
     * @param  App\Models\UnidadMedida $unidadMedida
     * @return void
     */
    public function __construct(UnidadMedida $unidadMedida) 
    {
        $this->model = $unidadMedida;
        
    }

    
}