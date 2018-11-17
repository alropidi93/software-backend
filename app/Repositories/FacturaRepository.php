<?php
namespace App\Repositories;
use App\Models\Factura;
use App\Models\PersonaJuridica;
use App\Models\ComprobantePago;
use App\Http\Helpers\DateFormat;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Collection;
    
//CHECKED AGAINST USUARIO REPOSITORY
class FacturaRepository extends BaseRepository {
    protected $comprobantePago;
    protected $personaJuridica; //cliente
    protected $comprobantePagoRepository;
    protected $lineaDeVentaRepository;
    protected $lineasDeVenta;

    public function __construct(Factura $factura=null, ComprobantePago $comprobantePago=null, PersonaJuridica $personaJuridica=null, ComprobantePagoRepository $comprobantePagoRepository=null, LineaDeVentaRepository $lineaDeVentaRepository=null){
        $this->model = $factura;
        $this->comprobantePago = $comprobantePago;
        $this->personaJuridica = $personaJuridica;
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
        $dataComprobantePago= Algorithm::quitNullValuesFromArray($dataComprobantePago);
        $this->comprobantePago['idCajero'] = array_key_exists('idCajero',$dataComprobantePago)? $dataComprobantePago['idCajero']:null;
        $this->comprobantePago['entrega'] = array_key_exists('entrega',$dataComprobantePago)? $dataComprobantePago['entrega']:true;
        $this->comprobantePago['fechaEnt'] = array_key_exists('fechaEnt',$dataComprobantePago)? $dataComprobantePago['fechaEnt']:null;
        $this->comprobantePago['subtotal'] =  $dataComprobantePago['subtotal'];
        $this->comprobantePago['deleted'] =  false; //default value
       
    }

    protected function setFacturaData($dataFactura){
        $this->model['idCliente'] = array_key_exists('idCliente',$dataFactura)? $dataFactura['idCliente']:null;
        $this->model['igv'] = array_key_exists('igv', $dataFactura)? $dataFactura['igv']:null;
        $this->model['deleted'] =  false; //default value
    }

    protected function saveComprobantePago(){
        $this->comprobantePago->save();    
    }

    protected function attachFacturaToComprobantePago($comprobantePago, $factura){
        return $comprobantePago->factura()->save($factura);
    }

    public function getUsuarioById($idUsuario){
        return $this->personaJuridica->where('id',$idUsuario)->where('deleted',false)->first();
    }

   
    public function guarda($dataArray){
        $this->setComprobantePagoData($dataArray);
        $this->saveComprobantePago(); //saving in database        
        $this->setFacturaData($dataArray);
        $this->attachFacturaToComprobantePago($this->comprobantePago,$this->model);
        $this->model->comprobantePago;//loading comprobantePago
        return $this->comprobantePagoRepository->setComprobantePagoModel($this->model->comprobantePago);
        
        
    }

    public function actualiza($dataArray){
        $this->setFacturaData($dataArray);
        $this->comprobantePago->update($dataArray);
        $this->model->update($dataArray); //set data only in its ComprobantePago model
    }

    public function attachPersonaJuridica(){
        $this->model->personaJuridica()->associate($this->personaJuridica);
        $this->model->save();
    }

    public function loadPersonaJuridicaRelationship($personaJuridica=null){
        if (!$personaJuridica){
            $this->model->load(['personaJuridica' => function ($query) {
                $query->where('deleted', false);
            }]);
        }else{
            $personaJuridica->load(['personaJuridica' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
    }

    public function listarFacturas(){
        $lista = $this->model->where('deleted',false)->get();
        foreach ($lista as $key => $factura) {
            $factura->comprobantePago;
        }
        return $lista;
    }

    public function obtenerFacturaPorId($id){
        $factura = $this->model->where('idComprobantePago',$id)->where('deleted',false)->first();
        if($factura) $factura->comprobantePago;
        return $factura;
    }

    protected function setComprobantePagoModel($comprobantePago){
        $this->comprobantePago = $comprobantePago;
    }

    public function getComprobantePagoModel(){
        return $this->comprobantePago;
    }

    public function setModelFactura($factura){
        $this->model = $factura;
        $comprobantePago = $factura->comprobantePago;
        if($factura->comprobantePago)
            $this->comprobantePago =  $comprobantePago;
    }

    public function setPersonaJuridicaModel($personaJuridica){
        $this->personaJuridica =  $personaJuridica;
    }

    public function softDelete(){
        $this->comprobatePago->deleted = true;
        $this->comprobatePago->save();
        $this->model->deleted = true;
        $this->model->save();
    }
    /*Alvaro's change*/
    public function obtenerComprobantePago($factura = null){
        if ($factura){
            return $factura->comprobantePago;
        }
        else{
            return $this->model->comprobantePago;
        }

    }
    /*Alvaro's change END*/
}