<?php
namespace App\Repositories;
use App\Models\Boleta;
use App\Models\PersonaNatural;
use App\Models\ComprobantePago;
use App\Http\Helpers\DateFormat;
use Illuminate\Support\Collection;
    
//CHECKED AGAINST USUARIO REPOSITORY
class BoletaRepository extends BaseRepository {
    protected $comprobantePago;
    protected $personaNatural; //cliente
    protected $comprobantePagoRepository;
    protected $lineaDeVentaRepository;
    protected $lineasDeVenta;

    public function __construct(Boleta $boleta=null, ComprobantePago $comprobantePago=null, PersonaNatural $personaNatural=null, ComprobantePagoRepository $comprobantePagoRepository=null, LineaDeVentaRepository $lineaDeVentaRepository=null){
        $this->model = $boleta;
        $this->comprobantePago = $comprobantePago;
        $this->personaNatural = $personaNatural;
        $this->comprobantePagoRepository = $comprobantePagoRepository;
        $this->lineaDeVentaRepository = $lineaDeVentaRepository;
    }

    public function setLineaDeVentaData($dataLineaDeVenta){
        $this->comprobantePagoRepository->setLineaDeVentaData($dataLineaDeVenta);
    }

    public function attachLineaDeVentaWithOwnModels(){
        $this->comprobantePagoRepository->attachLineaDeVentaWithOwnModels();
    }

    public function loadLineasDeVentaRelationship(){
        $this->comprobantePagoRepository->loadLineasDeVentaRelationship();
    }

    protected function setComprobantePagoData($dataComprobantePago){
        $this->comprobantePago['idCajero'] = array_key_exists('idCajero',$dataComprobantePago)? $dataComprobantePago['idCajero']:null;
        $this->comprobantePago['subtotal'] =  $dataComprobantePago['subtotal'];
        $this->comprobantePago['deleted'] =  false; //default value
       
    }

    protected function setBoletaData($dataBoleta){
        $this->model['idCliente'] = array_key_exists('idCliente',$dataBoleta)? $dataBoleta['idCliente']:null;
        $this->model['igv'] = $dataBoleta['igv'];
        $this->model['deleted'] =  false; //default value
    }

    protected function saveComprobantePago(){
        $this->comprobantePago->save();    
    }

    protected function attachBoletaToComprobantePago($comprobantePago, $boleta){
        return $comprobantePago->boleta()->save($boleta);
    }

    public function getUsuarioById($idUsuario){
        return $this->personaNatural->where('id',$idUsuario)->where('deleted',false)->first();
    }

    /**
     * guarda los datos del comprobante de pago, pero queda pendiente guardar las lineas de venta que pertenecen
     * al COMPROBANTE DE PAGO
     */
    public function guarda($dataArray){
        // $dataArray['deleted'] =false;
        // return $this->model = $this->model->create($dataArray);
        $this->setComprobantePagoData($dataArray); //set data only in its ComprobantePago model
        $this->saveComprobantePago(); //saving in database
        // $this->comprobantePagoRepository->setLineasDeVentaByOwnModel();
        $this->setBoletaData($dataArray);// set data only in its boleta model
        $this->attachBoletaToComprobantePago($this->comprobantePago,$this->model);
        $this->model->comprobantePago;//loading comprobantePago
        $this->comprobantePagoRepository->setComprobantePagoModel($this->model->comprobantePago);
        $list = $dataArray['lineasDeVenta'];
        //$this->$lineasDeVenta = $list;
        //must save lineas de venta
        $list_collection = new Collection($list);
        foreach ($list_collection as $key => $elem) {
             $this->comprobantePagoRepository->setLineaDeVentaData($elem);
             $this->comprobantePagoRepository->attachLineaDeVentaWithOwnModels();
        }
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

    public function loadPersonaNaturalRelationship($personaNatural=null){
        if (!$personaNatural){
            $this->model->load(['personaNatural' => function ($query) {
                $query->where('deleted', false);
            }]);
        }else{
            $personaNatural->load(['personaNatural' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
    }

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

    public function getComprobantePagoModel(){
        return $this->comprobantePago;
    }

    public function setModelBoleta($boleta){
        $this->model = $boleta;
        $comprobantePago = $boleta->comprobantePago;
        if($boleta->comprobantePago)
            $this->comprobantePago =  $comprobantePago;
    }

    public function setPersonaNaturalModel($personaNatural){
        $this->personaNatural =  $personaNatural;
    }

    public function softDelete(){
        $this->comprobatePago->deleted = true;
        $this->comprobatePago->save();
        $this->model->deleted = true;
        $this->model->save();
    }
    /*Alvaro's change*/
    public function obtenerComprobantePago($boleta = null){
        if ($boleta){
            return $boleta->comprobantePago;
        }
        else{
            return $this->model->comprobantePago;
        }

    }
    /*Alvaro's change END*/
}