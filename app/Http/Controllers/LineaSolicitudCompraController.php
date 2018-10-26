<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LineaSolicitudCompraRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\LineaSolicitudCompraResource;
use App\Http\Resources\LineasSolicitudCompraResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class LineaSolicitudCompraController extends Controller
{
    public function __construct(LineaSolicitudCompraRepository $lineaSolicitudCompraRepository){
        LineaSolicitudCompraResource::withoutWrapping();
        $this->lineaSolicitudCompraRepository = $lineaSolicitudCompraRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $lineasSolicitudCompra = $this->lineaSolicitudCompraRepository->obtenerTodos();
            foreach ($lineasSolicitudCompra as $key => $lineaSolicitudCompra) {
                $this->lineaSolicitudCompraRepository->loadProductoRelationship($lineaSolicitudCompra);
                $this->lineaSolicitudCompraRepository->loadProveedorRelationship($lineaSolicitudCompra);
            }
            $LineasSolicitudCompraResource =  new LineasSolicitudCompraResource($lineasSolicitudCompra);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Lista de lineas de solicitud de compra');  
            $responseResource->body($LineasSolicitudCompraResource);
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

    public function store(Request $lineaSolicitudCompraData){
        try{
            $validator = \Validator::make($lineaSolicitudCompraData->all(), 
                            ['cantidad' => 'required',
                            'idProducto' => 'required',
                            'idSolicitudDeCompra' => 'required',
                            'idProveedor' => 'required'
                            ]);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            
            $lineaSolicitudCompra = $this->lineaSolicitudCompraRepository->guarda($lineaSolicitudCompraData->all());
            DB::commit();
            $lineaSolicitudCompraResource =  new LineaSolicitudCompraResource($lineaSolicitudCompra);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Linea de solicitud de compra creada exitosamente');       
            $responseResource->body($lineaSolicitudCompraResource);       
            return $responseResource;
        }catch(\Exception $e){
            DB::rollback();            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LineaSolicitudCompra  $lineaSolicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $lineaSolicitudCompra = $this->lineaSolicitudCompraRepository->obtenerPorId($id);   
            if (!$lineaSolicitudCompra){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de solicitud de compra no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->lineaSolicitudCompraRepository->loadProveedoresRelationship($lineaSolicitudCompra);
            $lineaSolicitudCompraResource =  new LineaSolicitudCompraResource($lineaSolicitudCompra);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Mostrar linea de solicitud de compra');  
            $responseResource->body($lineaSolicitudCompraResource);
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LineaSolicitudCompra  $lineaSolicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LineaSolicitudCompra $lineaSolicitudCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LineaSolicitudCompra  $lineaSolicitudCompra
     * @return \Illuminate\Http\Response
     */
    public function destroy(LineaSolicitudCompra $lineaSolicitudCompra)
    {
        try{
            DB::beginTransaction();
            $lineaSolicitudCompra = $this->lineaSolicitudCompraRepository->obtenerPorId($id);
            if (!$lineaSolicitudCompra){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de solicitud de compra no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->lineaSolicitudCompraRepository->setModel($lineaSolicitudCompra);
            $this->lineaSolicitudCompraRepository->softDelete();     
            $responseResource = new ResponseResource(null);
            $responseResource->title('Linea de solicitud de compra eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}
