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
            $proveedor = $this->categoriaRepository->obtenerPorId($id);
            if (!$proveedor){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Proveedor no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->proveedorRepository->setModel($proveedor);
            $proveedorResource =  new ProveedorResource($proveedor);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar proveedor');  
            $responseResourse->body($usuarioResource);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
