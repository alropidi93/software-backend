<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $categorias = $this->categoriaRepository->obtenerTodos();
            
            
            $categoriaResource =  new CategoriasResource($categorias);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de categorias');  
            $responseResourse->body($movimientoResource);
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
    public function store(Request $request)
    {
        try{
            $validator = \Validator::make($categoriaData->all(), 
                            ['nombre' => 'required',
                            'descripcion' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $categoria = $this->categoriaRepository->guarda($categoriaData->all());
            DB::commit();
            $categoriaResource =  new CategoriaResource($categoria);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Categoria registrada exitosamente');       
            $responseResourse->body($categoriaResource);       
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
            $categoria = $this->categoriaRepository->obtenerPorId($id);
            if (!$categoria){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Categoria no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->categoriaRepository->setModel($categoria);
            $categoriaResource =  new CategoriaResource($categoria);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar categoria');  
            $responseResourse->body($categoriaResource);
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
    public function update(Request $request, $id)
    {
        try{
        
            $categoriaDataArray= Algorithm::quitNullValuesFromArray($categoriaData->all());
            $validator = \Validator::make($categoriaDataArray, 
                            ['id' => 'exists:categoria,id']
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $categoria = $this->categoriaRepository->obtenerPorId($id);
            
            if (!$categoria){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Categoria no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            

            
            
            $this->categoriaRepository->setModel($categoria);
            
            $this->categoriaRepository->actualiza($categoriaDataArray);
            $categoria = $this->categoriaRepository->obtenerModelo();
            
            DB::commit();
            $categoriaResource =  new ProductoResource($categoria);
            $responseResource = new ResponseResource(null);
            
            $responseResource->title('Categoria actualizada exitosamente');       
            $responseResource->body($categoriaResource);     
            
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
            $categoria = $this->categoriaRepository->obtenerPorId($id);
            
            if (!$categoria){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Categoria no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->categoriaRepository->setModel($categoria);
            $this->categoriaRepository->softDelete();
            

              
            $responseResource = new ResponseResource(null);
            $responseResource->title('Categoria eliminada');  
            $responseResource->body(['id' => $id]);
            DB::commit();
            return $responseResource;
        }
        catch(\Exception $e){
         
            
            
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }
}
