<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LineaDeVenta;
use App\Repositories\LineaDeVentaRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\LineaDeVentaResource;
use App\Http\Resources\LineasDeVentaResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class LineaDeVentaController extends Controller
{
    // protected $lineaDeVentaRepository;

    public function __construct(LineaDeVentaRepository $lineaDeVentaRepository){
        LineaDeVentaResource::withoutWrapping();
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
            $lineasDeVenta = $this->lineaDeVentaRepository->obtenerTodos();
            $lineasDeVentaResource =  new LineasDeVentaResource($lineasDeVenta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de lineas de venta');  
            $responseResourse->body($lineasDeVentaResource);
            return $responseResourse;
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
    public function store(Request $lineaDeVentaData)
    {
        try{
            $validator = \Validator::make($lineaDeVentaData->all(), 
                            ['idProducto' => 'required',
                            // 'idComprobantePago' => 'required',
                            'cantidad' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            DB::beginTransaction();
            $lineaDeVenta = $this->lineaDeVentaRepository->guarda($lineaDeVentaData->all());
            DB::commit();

            $this->lineaDeVentaRepository->loadProductoRelationship($lineaDeVenta);
            $lineaDeVentaResource =  new LineaDeVentaResource($lineaDeVenta);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Linea de venta creada exitosamente');       
            $responseResourse->body($lineaDeVentaResource);       
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
            $lineaDeVenta = $this->lineaDeVentaRepository->obtenerPorId($id);
            if (!$lineaDeVenta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de venta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->lineaDeVentaRepository->loadProductoRelationship($lineaDeVenta);
            // $this->lineaDeVentaRepository->setModel($lineaDeVenta);
           
            $lineaDeVentaResource =  new LineaDeVentaResource($lineaDeVenta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar linea de venta');  
            $responseResourse->body($lineaDeVentaResource);
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
    public function update(Request $lineaDeVentaData, $id)
    {
        try{
            DB::beginTransaction();
            $lineaDeVenta = $this->lineaDeVentaRepository->obtenerPorId($id);
            if (!$lineaDeVenta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de venta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->lineaDeVentaRepository->setModel($lineaDeVenta);
            $lineaDeVentaDataArray= Algorithm::quitNullValuesFromArray($lineaDeVentaData->all());
            $this->lineaDeVentaRepository->actualiza($lineaDeVentaDataArray);
            $lineaDeVenta = $this->lineaDeVentaRepository->obtenerModelo();
            DB::commit();

            $lineaDeVentaResource =  new LineaDeVentaResource($lineaDeVenta);
            $responseResource = new ResponseResource(null);
            $responseResource->title('Linea de venta actualizada exitosamente');       
            $responseResource->body($lineaDeVentaResource);        
            return $responseResource;
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
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $lineaDeVenta = $this->lineaDeVentaRepository->obtenerPorId($id);
            if (!$lineaDeVenta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Linea de venta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->lineaDeVentaRepository->setModel($lineaDeVenta);
            $this->lineaDeVentaRepository->softDelete();  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Linea de venta eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();

            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}
