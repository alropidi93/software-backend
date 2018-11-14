<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PersonaNaturalResource;
use App\Http\Resources\PersonasNaturalesResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Usuario;
use App\Repositories\PersonaNaturalRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class PersonaNaturalController extends Controller
{
    protected $personaNaturalRepository;
    public function __construct(PersonaNaturalRepository $personaNaturalRepository){
        PersonaNaturalResource::withoutWrapping();
        $this->personaNaturalRepository = $personaNaturalRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try{
            //obtener solo aquellos que no sean usuarios
            // $personasNaturales = $this->personaNaturalRepository->obtenerTodos();
            $personasNaturales = $this->personaNaturalRepository->obtenerClientesNaturales();
            $personasNaturalesResource =  new PersonasNaturalesResource($personasNaturales);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de personas naturales(clientes)');  
            $responseResource->body($personasNaturalesResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $data){
        try{
            $validator = \Validator::make($data->all(), 
                            ['nombre' => 'required',
                            'apellidos' => 'required',
                            'email' => 'required|email',
                            'dni' => 'required|min:8|max:12|unique:personaNatural,dni']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            $personaNatural= $this->personaNaturalRepository->obtenerPersonaNaturalPorEmail($data['email']);
            if ($personaNatural){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de validación del email');
                $errorResource->message('El email ya se encuentra en uso');
                return $errorResource->response()->setStatusCode(422);
            }

            DB::beginTransaction();
            $this->personaNaturalRepository->guarda($data->all());
            $personaNaturalCreated = $this->personaNaturalRepository->obtenerModelo();
            
            DB::commit();   
            
            $personaNaturalResource =  new PersonaNaturalResource($personaNaturalCreated);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Cliente natural creado exitosamente');       
            $responseResource->body($personaNaturalResource);       
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $personaNatural = $this->personaNaturalRepository->obtenerPorId($id);
            
            if (!$personaNatural){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cliente natural no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->personaNaturalRepository->setModel($personaNatural);
            $personaNaturalResource =  new PersonaNaturalResource($personaNatural);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar cliente natural');  
            $responseResourse->body($personaNaturalResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $personaNaturalData, $id){
        try{
            $personaNaturalDataArray= Algorithm::quitNullValuesFromArray($personaNaturalData->all());
            if (array_key_exists('dni',$personaNaturalData)){
                $personaNaturalAux= $this->personaNaturalRepository->obtenerPersonaNaturalPorDni($personaNaturalData['dni']);
                if ($id != $personaNaturalAux->id){
                    $validator = ['dni'=>'El dni ya está siendo usado por otro cliente natural'];
                    return (new ValidationResource($validator))->response()->setStatusCode(422);
                }
            }
         
            DB::beginTransaction();
            $personaNatural= $this->personaNaturalRepository->obtenerPorId($id);
            if (!$personaNatural){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cliente natural no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->personaNaturalRepository->setModel($personaNatural);
            $this->personaNaturalRepository->actualiza($personaNaturalDataArray);
            
            $personaNatural = $this->personaNaturalRepository->obtenerModelo();
            DB::commit();

            $personaNaturalResource =  new PersonaNaturalResource($personaNatural);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Cliente natural actualizado exitosamente');       
            $responseResourse->body($personaNaturalResource);
            return $responseResourse;   
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            DB::beginTransaction();
            $personaNatural = $this->personaNaturalRepository->obtenerPorId($id);
            
            if (!$personaNatural){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cliente natural no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->personaNaturalRepository->setModel($personaNatural);
            $this->personaNaturalRepository->softDelete();
            
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Cliente natural eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    public function busquedaPorDni(){
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
                case 'dni':      
                    $personasNaturales = $this->personaNaturalRepository->buscarPorFiltro($filter, $value); //busqueda generica en BaseRepository
                    $personasNaturalesResource =  new PersonasNaturalesResource($personasNaturales);
                    $responseResource->title('Clientes naturales encontrados por DNI');
                    $responseResource->body($personasNaturalesResource);
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
