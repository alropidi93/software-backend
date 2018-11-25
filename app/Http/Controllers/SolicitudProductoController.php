<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudProducto;
use App\Models\Producto;
use App\Repositories\SolicitudProductoRepository;
use App\Repositories\ProductoRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudProductoResource;
use App\Http\Resources\SolicitudesProductoResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class SolicitudProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $solicitudProductoRepository;

    public function __construct(SolicitudProductoRepository $solicitudProductoRepository){
        SolicitudProductoResource::withoutWrapping();
        $this->solicitudProductoRepository = $solicitudProductoRepository;
    }
    public function index()
    {
        try{
            $solicitudesProducto = $this->solicitudProductoRepository->obtenerTodos();
                      
            $solicitudesProductoResource =  new SolicitudesProductoResource($solicitudesProducto);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de solicitudes de producto');  
            $responseResourse->body($solicitudesProductoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }  
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $solicitudProductoData)
    {
        try{
            $validator = \Validator::make($solicitudProductoData->all(), 
                            ['descripcion' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $solicitudProducto = $this->solicitudProductoRepository->guarda($solicitudProductoData->all());
            DB::commit();
            $solicitudProductoResource =  new SolicitudProductoResource($solicitudProducto);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Categoria registrada exitosamente');       
            $responseResourse->body($solicitudProductoResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
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
            $solicitudProducto = $this->solicitudProductoRepository->obtenerPorId($id);
            if (!$solicitudProducto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud de Producto no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->solicitudProductoRepository->setModel($solicitudProducto);
            $solicitudProductoResource =  new SolicitudProductoResource($solicitudProducto);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar solicitud de producto');  
            $responseResourse->body($solicitudProductoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
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
    public function update(Request $solicitudProductoData, $id)
    {
        try{
        
            $solicitudProductoDataArray= Algorithm::quitNullValuesFromArray($solicitudProductoData->all());
            $validator = \Validator::make($solicitudProductoDataArray, 
                            ['id' => 'exists:solicitudProducto,id']
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $solicitudProducto = $this->solicitudProductoRepository->obtenerPorId($id);
            
            if (!$solicitudProducto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud Producto no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->solicitudProductoRepository->setModel($solicitudProducto);
            
            $this->solicitudProductoRepository->actualiza($solicitudProductoDataArray);
            $solicitudProducto = $this->solicitudProductoRepository->obtenerModelo();
            
            DB::commit();
            $solicitudProductoResource =  new SolicitudProductoResource($solicitudProducto);
            $responseResource = new ResponseResource(null);
            
            $responseResource->title('Solicitud de Producto actualizada exitosamente');       
            $responseResource->body($solicitudProductoResource);     
            
            return $responseResource;
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
            $solicitudProducto = $this->solicitudProductoRepository->obtenerPorId($id);
            
            if (!$solicitudProducto){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud de Producto no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->solicitudProductoRepository->setModel($solicitudProducto);
            $this->solicitudProductoRepository->softDelete();
            

              
            $responseResource = new ResponseResource(null);
            $responseResource->title('Solicitud de Producto eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }
}