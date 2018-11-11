<?php
namespace App\Repositories;
use App\Models\Boleta;
use App\Models\PersonaNatural;
use App\Http\Helpers\DateFormat;
	
class BoletaRepository extends BaseRepository {
    protected $comprobantePago;

    public function __construct(Boleta $boleta=null, ComprobantePago $comprobantePago=null){
        $this->model = $boleta;
        $this->comprobantePago = $comprobantePago;
    }

    protected function setComprobantePagoData($dataComprobantePago){
        $this->comprobantePago['subtotal'] =  $dataComprobantePago['subtotal'];
        $this->comprobantePago['deleted'] =  false; //default value
    }

    protected function setBoletaData($dataBoleta){
        $this->model['igv'] = $dataBoleta['igv'];
        $this->model['deleted'] =  false; //default value
    }

    protected function saveComprobantePago(){
        $this->comprobantePago->save();    
    }

    protected function attachBoletaToComprobantePago($comprobantePago, $boleta){
        return $comprobantePago->boleta()->save($boleta);
    }

    public function guarda($dataArray){
        $this->setComprobantePagoData($dataArray); //set data only in its ComprobantePago model
        $this->saveComprobantePago(); //saving in database
        $this->setBoletaData($dataArray);// set data only in its Usuario model
        $this->attachBoletaToComprobantePago($this->comprobantePago,$this->model);
        $this->model->comprobantePago;//loading comprobantePago
    }

    public function actualiza($dataArray){
        //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
        //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
        $this->comprobantePago->update($dataArray);
        $this->model->update($dataArray); //set data only in its ComprobantePago model
    }

    // public function actualizaSoloBoleta($dataArray){
    //     //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
    //     //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
    //     if (array_key_exists('igv',$dataArray))
    //         $this->model->update($dataArray); //set data only in its PersonaNatural model
    // }

    public function listarBoletas(){
        $lista = $this->model->where('deleted',false)->get();
        foreach ($lista as $key => $boleta) {
            $boleta->comprobantePago;
        }
        return $lista;
    }

    public function obtenerBoletaPorId($id){
        $boleta = $this->model->where('idComprobantePago',$id)->where('deleted',false)->first();
        if($boleta) $boleta->comprobantePago;
        return $boleta;
    }

    protected function setComprobantePagoModel($comprobantePago){
        $this->comprobantePago = $comprobantePago;
    }

    public function setModelBoleta($boleta){
        $this->model = $boleta;
        $comprobantePago = $boleta->comprobantePago;
        if($boleta->comprobantePago)
            $this->comprobantePago =  $comprobantePago;
    }

    public function softDelete(){
        $this->comprobatePago->deleted = true;
        $this->comprobatePago->save();
        $this->model->deleted = true;
        $this->model->save();
    }

    public function loadPersonaNaturalRelationship($boleta=null){
        if (!$boleta){
            $this->model = $this->model->load([
                'personaNatural'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }else{       
            $this->model =$boleta->load([
                'personaNatural'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->personaNatural){
            $this->personaNatural = $this->model->personaNatural;
        }
    }
}