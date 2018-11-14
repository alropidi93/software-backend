<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Usuario;
use App\Repositories\CotizacionRepository;
use App\Repositories\LineaDeVentaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\CotizacionResource;
use App\Http\Resources\CotizacionesResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class CotizacionController extends Controller
{   
    protected $cotizacionRepository;
    protected $lineasDeVenta;
    
    public function __construct(CotizacionRepository $cotizacionRepository, LineaDeVentaRepository $lineaDeVentaRepository){
        CotizacionResource::withoutWrapping();
        $this->cotizacionRepository = $cotizacionRepository;
        $this->lineaDeVentaRepository = $lineaDeVentaRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $cotizaciones = $this->cotizacionRepository->obtenerTodos();
            foreach ($cotizaciones as $key => $cotizacion) {
              $this->cotizacionRepository->loadLineasDeVentaRelationship($cotizacion);
              $this->cotizacionRepository->loadCajeroRelationship($cotizacion);
            }
            $cotizacionesResource =  new CotizacionesResource($cotizaciones);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de cotizaciones');  
            $responseResourse->body($cotizacionesResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
    public function store(Request $cotizacionData)
    {
        try{
            $validator = \Validator::make($cotizacionData->all(), 
                            ['subtotal' => 'required',
                            'lineasDeVenta'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            
            $idUsuario = array_key_exists('idCajero', $cotizacionData)? $cotizacionData['idCajero']:null;
            if($idUsuario){
                $usuario = $this->cotizacionRepository->getUsuarioById($idUsuario);
                if (!$usuario){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este usuario');
                    $notFoundResource->notFound(['id' => $idUsuario]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                $this->cotizacionRepository->setUsuarioModel($usuario);
            }
            
            DB::beginTransaction();
            $this->cotizacionRepository->guarda($cotizacionData->all());
            $cotizacion = $this->cotizacionRepository->obtenerModelo();

            $list = $cotizacionData['lineasDeVenta'];
            $list_collection = new Collection($list);

            foreach ($list_collection as $key => $elem) {
                $this->cotizacionRepository->setLineaDeVentaData($elem);
                $this->cotizacionRepository->attachLineaDeVentaWithOwnModels();
            }
            DB::commit();
            
            $cotizacionCreada = $this->cotizacionRepository->obtenerModelo();
            $this->cotizacionRepository->loadLineasDeVentaRelationship($cotizacionCreada);
            
            $this->cotizacionRepository->loadCajeroRelationship($cotizacionCreada);
                      
            $cotizacionResource =  new CotizacionResource($cotizacionCreada);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Cotizacion creada exitosamente');       
            $responseResourse->body($cotizacionResource);       
            return $responseResourse;
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
    public function show($id)
    {
        try{
            $cotizacion = $this->cotizacionRepository->obtenerPorId($id);
            if (!$cotizacion){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cotizacion no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->cotizacionRepository->setModel($cotizacion);
            $this->cotizacionRepository->loadLineasDeVentaRelationship();
            $this->cotizacionRepository->loadCajeroRelationship($cotizacion);

            $cotizacionResource =  new CotizacionResource($cotizacion);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar cotizacion');  
            $responseResourse->body($cotizacionResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    
    public function update($id,Request $cotizacionData)
    {
        try{
            $cotizacionDataArray= Algorithm::quitNullValuesFromArray($cotizacionData->all());
            if (array_key_exists('dni',$cotizacionData)){
                $usuarioAux= $this->usuarioRepository->obtenerUsuarioPorDni($usuarioData['dni']);
                if ($id != $usuarioDataAux->id){
                    $validator = ['dni'=>'El dni ya está siendo usado por otro usuario'];
                    return (new ValidationResource($validator))->response()->setStatusCode(422);
                }
            }
    
         
            DB::beginTransaction();
            $usuario= $this->usuarioRepository->obtenerUsuarioPorId($id);
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $this->usuarioRepository->setModelUsuario($usuario);
            
            $this->usuarioRepository->actualiza($usuarioDataArray);
            
            $usuario = $this->usuarioRepository->obtenerModelo();
            DB::commit();
            $this->usuarioRepository->loadTipoUsuarioRelationship();

            $usuarioResource =  new UsuarioResource($usuario);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Usuario actualizado exitosamente');       
            $responseResourse->body($usuarioResource);     
            
            return $responseResourse;


            
        }
        catch(\Exception $e){
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
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $cotizacion = $this->cotizacionRepository->obtenerPorId($id);
            if (!$cotizacion){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Cotizacion no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->cotizacionRepository->setModel($cotizacion);
            $this->cotizacionRepository->softDelete();
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Cotizacion eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();

            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    
}
