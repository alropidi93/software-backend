<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCompra;
use App\Repositories\SolicitudCompraRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudCompraResource;
use App\Http\Resources\SolicitudesCompraResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class SolicitudCompraController extends Controller
{
    public function __construct(SolicitudCompraRepository $solicitudCompraRepository){
        SolicitudCompraResource::withoutWrapping();
        $this->solicitudCompraRepository = $solicitudCompraRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try{
            $solicitudesCompra = $this->solicitudCompraRepository->obtenerTodos();
            foreach ($solicitudesCompra as $key => $solicitudCompra) {
                $this->solicitudCompraRepository->loadLineasSolicitudCompraRelationship($solicitudCompra);
            }
            $solicitudesCompraResource =  new SolicitudesCompraResource($solicitudesCompra);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de solicitudes de compra');  
            $responseResource->body($solicitudesCompraResource);
            return $responseResource;
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
    public function store(Request $solicitudCompraData){
        try{
            $validator = \Validator::make($solicitudCompraData->all(), 
                            ['fecha' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $solicitudCompra = $this->solicitudCompraRepository->guarda($solicitudCompraData->all());
            DB::commit();
            $solicitudCompraResource = new SolicitudCompraResource($solicitudCompra);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Solicitud de compra creada exitosamente');       
            $responseResource->body($solicitudCompraResource);       
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SolicitudCompra  $solicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $solicitudCompra = $this->solicitudCompraRepository->obtenerPorId($id);
            if (!$solicitudCompra){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud de compra no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->solicitudCompraRepository->loadLineasSolicitudCompraRelationship($solicitudCompra);
            $solicitudCompraResource =  new SolicitudCompraResource($solicitudCompra);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar solicitud de compra');  
            $responseResource->body($solicitudCompraResource);
            return $responseResource;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SolicitudCompra  $solicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $solicitudCompraData){
        try{
        
            $solicitudCompraDataArray= Algorithm::quitNullValuesFromArray($solicitudCompraData->all());
            $validator = \Validator::make($solicitudCompraData, 
                            ['idTienda' => 'exists:tienda,id']
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $solicitudCompra = $this->solicitudCompraRepository->obtenerPorId($id);
            
            if (!$solicitudCompra){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud de compra no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }

            $this->solicitudCompraRepository->setModel($solicitudCompra);
            $this->solicitudCompraRepository->actualiza($solicitudCompraData);
            $solicitudCompra = $this->solicitudCompraRepository->obtenerModelo();
            DB::commit();
            $solicitudCompraResource =  new SolicitudCompraResource($solicitudCompra);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Solicitud de compra actualizada exitosamente');       
            $responseResource->body($solicitudCompraResource);        
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SolicitudCompra  $solicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            DB::beginTransaction();
            $solicitudCompra = $this->solicitudCompraRepository->obtenerPorId($id);
            if (!$solicitudCompra){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Solicitud de compra no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->solicitudCompraRepository->setModel($solicitudCompra);
            $this->solicitudCompraRepository->softDelete();
            
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Usuario eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}
