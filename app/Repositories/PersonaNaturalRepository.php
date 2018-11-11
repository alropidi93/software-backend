<?php
namespace App\Repositories;
use App\Models\PersonaNatural;
	
class PersonaNaturalRepository extends BaseRepository {
    public function __construct(PersonaNatural $personaNatural)  {
        $this->model = $personaNatural;
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        $this->model = $this->model->create($dataArray);
        $this->model->save();
        return $this->model;
    }

    public function obtenerClientesNaturales(){
        $list = $this->model->where('deleted',false)->whereDoesntHave('usuario',function($q){
            $q->where('usuario.deleted',false);
        })->where('deleted',false)->get();
        return $list;
    }

    public function obtenerPersonaNaturalPorEmail($email){
        $personaNatural = $this->model->where('email',$email)->where('deleted',false)->first();
        if($personaNatural){
            return $personaNatural;
        }
        return null;
    }

    public function setPersonaNaturalModel($personaNatural){
        $this->model =  $personaNatural;
    }

    public function obtenerPersonaNaturalPorDni($dni){
        $personaNatural = $this->personaNatural->where('dni',$dni)->where('deleted',false)->first();
        if($personaNatural){
            $this->setPersonaNaturalModel($personaNatural);
            return $personaNatural;
        }
        return null;
    }
}