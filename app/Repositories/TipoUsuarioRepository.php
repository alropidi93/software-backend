<?php
namespace App\Repositories;
use App\Models\TipoUsuario;
	
class TipoUsuarioRepository extends BaseRepository {
    
    /**
     * Create a new UnidadMedidaRepository instance.
     * @param  App\Models\UnidadMedida $unidadMedida
     * @return void
     */
    public function __construct(TipoUsuario $tipoUsuario) 
    {
        $this->model = $tipoUsuario;
        
    }

    
}