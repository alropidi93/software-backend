<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Repositories\ComprobantePagoRepository;
use App\Repositories\LineaDeVentaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComprobantePagoResource;
use App\Http\Resources\ComprobantesPagoResource;
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
    
    public function __construct(ComprobantePagoRepository $cotizacionRepository, LineaDeVentaRepository $lineaDeVentaRepository){
        CotizacionResource::withoutWrapping();
        // LineaDeVentaResource::withoutWrapping(); // no tiene similar en pedido trans contro
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
            }
            $cotizacionesResource =  new CotizacionesResource($cotizaciones);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de cotizaciones');  
            $responseResourse->body($comprobantesPagoResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
    public function store(Request $request)
    {
        try{
            $validator = \Validator::make($cotizacionData->all(), 
                            ['subtotal' => 'required',
                            'lineasDeVenta'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $idUsuario =  $cotizacionData['idUsuario']; //id del cajero en caso tenga
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
            $this->cotizacionRepository->loadLineasDeVentaRelationship($comprobantePagoCreado);
                      
            $cotizacionResource =  new CotizacionResource($cotizacionCreada);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Cotizacion creada exitosamente');       
            $responseResourse->body($comprobantePagoResource);       
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
            $this->cotizacionRepository->loadLineasDeVentaRelationship(); //no tiene similar en pedido trans contr

            $cotizacionResource =  new ComprobantePagoResource($cotizacion);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar cotizacion');  
            $responseResourse->body($cotizacionResource);
            return $responseResourse;
        }catch(\Exception $e){
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
}
