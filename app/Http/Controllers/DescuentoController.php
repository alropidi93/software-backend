<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Descuento;
use App\Models\Usuario;
use App\Repositories\DescuentoRepository;
use App\Repositories\CategoriaRepository;
use App\Repositories\ProductoRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\DescuentoResource;
use App\Http\Resources\DescuentosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class DescuentoController extends Controller
{   
    protected $descuentoRepository;
  
    public function __construct(DescuentoRepository $descuentoRepository){
        DescuentoResource::withoutWrapping();
        $this->descuentoRepository = $descuentoRepository;
        }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $descuentos = $this->descuentoRepository->obtenerTodos();
            foreach ($descuentos as $key => $descuento) {
              $this->descuentoRepository->loadProductoRelationship($descuento);
              $this->descuentoRepository->loadCategoriaRelationship($descuento);
            }
            $descuentosResource =  new DescuentosResource($descuentos);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de descuentos');  
            $responseResourse->body($descuentosResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    
    public function store(Request $descuentoData)
    {
        try{
            $validator = \Validator::make($descuentoData->all(), 
                            ['fechaIni' => 'required',
                            'fechaFin'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }   
                      
            DB::beginTransaction();
            $descuento=$this->descuentoRepository->guarda($descuentoData->all()); 

            DB::commit();
            $this->descuentoRepository->loadProductoRelationship($descuento);            
            $this->descuentoRepository->loadCategoriaRelationship($descuento);            
                                 
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento creado exitosamente');       
            $responseResourse->body($descuentoResource);       
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
            $descuento = $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadProductoRelationship();
            $this->descuentoRepository->loadCategoriaRelationship();

            $descuentoResource =  new DescuentoResource($descuento);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar descuento');  
            $responseResourse->body($descuentoResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    
    public function update($id,Request $descuentoData)
    {
        try{
            $descuentoDataArray= Algorithm::quitNullValuesFromArray($descuentoData->all());             
            DB::beginTransaction();
            $descuento= $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }            
            $this->descuentoRepository->setDescuentoModel($descuento);            
            $this->descuentoRepository->actualiza($descuentoDataArray);            
            $descuento = $this->descuentoRepository->obtenerModelo();
           
            DB::commit();
            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->loadProductoRelationship();
            $this->descuentoRepository->loadCategoriaRelationship();
            $descuentoResource =  new DescuentoResource($descuento);
            $responseResourse = new ResponseResource(null);            
            $responseResourse->title('Descuento actualizado exitosamente');       
            $responseResourse->body($descuentoResource);     
            
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
            $descuento = $this->descuentoRepository->obtenerPorId($id);
            if (!$descuento){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Descuento no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->descuentoRepository->setModel($descuento);
            $this->descuentoRepository->softDelete();
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Descuento eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();

            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
    
}
