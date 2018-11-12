<?php
namespace App\Repositories;
use App\Models\ComprobantePago;
use App\Models\Usuario;
	
class ComprobantePagoRepository extends BaseRepository {
    protected $cajero;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(ComprobantePago $comprobantePago, Usuario $cajero)  
    {
        $this->model = $comprobantePago;
        $this->cajero = $cajero;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        $this->model = $this->model->create($dataArray);
        return $this->model;
    }

    public function attachCajero(){
        $this->model->usuario()->associate($this->cajero);
        $this->model->save();
    }

    public function loadCajeroRelationship($comprobantePago=null){
        if (!$comprobantePago){
            $this->model->load('usuario');
        }
        else{
            $tienda->load('usuario');
        }
    }

    public function setCajeroModel($usuario){
        $this->cajero = $usuario;       
    }

    // public function checkIfOwnModelTiendaHasJefeTienda(){
    //     return strval($this->model->jefeDeTienda()->where('usuario.deleted',false)
    //             ->whereHas('personaNatural', function ($query){
    //                 $query->where('personaNatural.deleted',false);
    //             })->exists());
    // }
}