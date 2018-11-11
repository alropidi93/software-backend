<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Repositories\FacturaRepository;
use App\Repositories\PersonaJuridicaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\FacturaResource;
use App\Http\Resources\FacturasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class FacturaController extends Controller
{
    protected $facturaRepository;
   
    public function __construct(FacturaRepository $facturaRepository){
        FacturaResource::withoutWrapping();
        $this->facturaRepository = $facturaRepository;
        
    }
    
    public function index(){
        try{
            $facturas = $this->facturaRepository->obtenerTodos();
            
            foreach ($facturas as $key => $factura) {
                $this->facturaRepository->loadComprobantePagoRelationship($factura);
                $this->facturaRepository->loadPersonaJuridicaRelationship($factura);                
            }

            $facturasResource =  new FacturasResource($facturas);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de facturas');  
            $responseResource->body($facturasResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

   
    public function store(Request $facturaData){
        try{   
            $validator = \Validator::make($facturaData->all(), 
                               ['subtotal' => 'required'],
                               ['igv' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
          
            DB::beginTransaction();
            
            $factura = $this->facturaRepository->guarda($facturaData->all());
            

            DB::commit();
            $this->facturaRepository->setModel($factura);
            $this->facturaRepository->loadComprobantePagoRelationship($factura);
            $this->facturaRepository->loadPersonaJuridicaRelationship($factura);       
            $facturaResource =  new FacturaResource($factura);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Factura creada exitosamente');       
            $responseResource->body($facturaResource);       
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
    public function show($id){
        try{
            $factura = $this->facturaRepository->obtenerPorId($id);
            
            if (!$factura){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Factura no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->facturaRepository->loadComprobantePagoRelationship($factura);
            $this->facturaRepository->loadPersonaJuridicaRelationship($factura);      
            $facturaResource =  new FacturaResource($factura);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar factura');  
            $responseResource->body($facturaResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
   
    public function destroy($id){
        try{
            DB::beginTransaction();
            $factura = $this->facturaRepository->obtenerPorId($id);
            
            if (!$factura){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Factura no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->facturaRepository->setModel($factura);
            $this->facturaRepository->softDelete();

            $responseResource = new ResponseResource(null);
            $responseResource->title('Factura eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    } 
}
  
