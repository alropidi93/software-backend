<?php
namespace App\Repositories;
use App\Models\TipoProducto;
	
class TipoProductoRepository extends BaseRepository {
    
    /**
     * Create a new UnidadMedidaRepository instance.
     * @param  App\Models\TipoProducto $tipoProducto
     * @return void
     */
    public function __construct(TipoProducto $tipoProducto) 
    {
        $this->model = $tipoProducto;
        
    }

    
}