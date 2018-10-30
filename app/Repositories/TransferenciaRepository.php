<?php
namespace App\Repositories;
use App\Models\Transferencia;

class TransferenciaRepository extends BaseRepository {


    /**
     * Create a new TransferenciaRepository instance.
     * @param  App\Models\Transferencia $transferencia
     * @return void
     */
    public function __construct(Transferencia $transferencia) 
    {
        $this->model = $transferencia;

        
    }

   


}