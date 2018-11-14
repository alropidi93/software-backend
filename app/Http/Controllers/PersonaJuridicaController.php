<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonaJuridica;
use App\Repositories\PersonaJuridicaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonaJuridicaResource;
use App\Http\Resources\PersonasJuridicasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class PersonaJuridicaController extends Controller
{
    protected $personaJuridicaRepository;
   
    public function __construct(PersonaJuridicaRepository $personaJuridicaRepository){
        PersonaJuridicaResource::withoutWrapping();
        $this->personaJuridicaRepository = $personaJuridicaRepository;
        
    }
    
    public function index(){
        try{
            $personasJuridicas = $this->personaJuridicaRepository->obtenerTodos();
            
            // foreach ($personasJuridicas as $key => $personaJuridica) {
            //     $this->personaJuridicaRepository->loadComprobantePagoRelationship($personaJuridica);
            //     $this->personaJuridicaRepository->loadPersonaJuridicaRelationship($personaJuridica);                
            // }

            $personasJuridicasResource =  new PersonasJuridicasResource($personasJuridicas);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de personas juridicas');  
            $responseResource->body($personasJuridicasResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function update(Request $personaJuridicaData, $id){
        try{
            $personaJuridicaDataArray= Algorithm::quitNullValuesFromArray($personaJuridicaData->all());
            if (array_key_exists('ruc',$personaJuridicaData)){
                $personaJuridicaAux= $this->personaJuridicaRepository->obtenerPersonaJuridicaPorRuc($personaJuridicaData['ruc']);
                if ($id != $personaJuridicaAux->id){
                    $validator = ['ruc'=>'El ruc ya está siendo usado por otro cliente juridico'];
                    return (new ValidationResource($validator))->response()->setStatusCode(422);
                }
            }
         
            DB::beginTransaction();
            $personaJuridica= $this->personaJuridicaRepository->obtenerPorId($id);
            if (!$personaJuridica){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cliente juridico no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->personaJuridicaRepository->setModel($personaJuridica);
            $this->personaJuridicaRepository->actualiza($personaJuridicaDataArray);
            
            $personaJuridica = $this->personaJuridicaRepository->obtenerModelo();
            DB::commit();

            $personaJuridicaResource =  new PersonaJuridicaResource($personaJuridica);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Cliente juridico actualizado exitosamente');       
            $responseResourse->body($personaJuridicaResource);
            return $responseResourse;   
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
    public function store(Request $personaJuridicaData){
        try{   
            // $validator = \Validator::make($personaJuridicaData->all(), 
            //                 ['idCliente' => 'required' ]);

            // if ($validator->fails()) {
            //     return (new ValidationResource($validator))->response()->setStatusCode(422);
            // }
          
            DB::beginTransaction();
            
            $personaJuridica = $this->personaJuridicaRepository->guarda($personaJuridicaData->all());
            

            DB::commit();
            $this->personaJuridicaRepository->setModel($personaJuridica);
            // $this->personaJuridicaRepository->loadComprobantePagoRelationship($factura);
            // $this->personaJuridicaRepository->loadPersonaJuridicaRelationship($factura);       
            $personaJuridicaResource =  new personaJuridicaResource($personaJuridica);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Persona juridica registrada exitosamente');       
            $responseResource->body($personaJuridicaResource);       
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
    public function show($id){
        try{
            $personaJuridica = $this->personaJuridicaRepository->obtenerPorId($id);
            
            if (!$personaJuridica){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Persona juridica no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            // $this->facturaRepository->loadComprobantePagoRelationship($factura);
            // $this->facturaRepository->loadPersonaJuridicaRelationship($factura);      
            $personaJuridicaResource =  new PersonaJuridicaResource($personaJuridica);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar persona juridica');  
            $responseResource->body($personaJuridicaResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
   
    public function destroy($id){
        try{
            DB::beginTransaction();
            $personaJuridica = $this->personaJuridicaRepository->obtenerPorId($id);
            
            if (!$personaJuridica){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Persona juridica no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->personaJuridicaRepository->setModel($personaJuridica);
            $this->personaJuridicaRepository->softDelete();

            $responseResource = new ResponseResource(null);
            $responseResource->title('Persona Juridica eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function busquedaPorRuc(){
        try{
            $filter = Input::get('filterBy');
            $value = strtolower(Input::get('value'));
            $responseResource = new ResponseResource(null);
            if (!$filter || !$value){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de búsqueda');
                $errorResource->message('Parámetros inválidos para la búsqueda');
                return $errorResource->response()->setStatusCode(400);
            }
            switch ($filter) {
                case 'ruc':
                    $personasJuridicas = $this->personaJuridicaRepository->buscarPorFiltro($filter, $value); //busqueda generica en BaseRepository
                    $personasJuridicasResource =  new PersonasJuridicasResource($personasJuridicas);
                    $responseResource->title('Clientes jurídicos encontrados por RUC');
                    $responseResource->body($personasJuridicasResource);
                    break;
                default:
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Valor de filtro inválido');
                    return $errorResource->response()->setStatusCode(400);
            }
            return $responseResource; 
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

} 
