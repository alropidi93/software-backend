<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProveedorResource;
use App\Http\Resources\ProveedoresResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Proveedor;
use App\Repositories\ProveedorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class ProveedorController extends Controller
{
    protected $proveedorRepository;

    public function __construct(ProveedorRepository $proveedorRepository){
        ProveedorResource::withoutWrapping();
        $this->proveedorRepository = $proveedorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        try{
            $proveedorResource =  new ProveedoresResource($this->proveedorRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de proveedores');  
            $responseResourse->body($proveedorResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function store(Request $proveedorData)
    {
        try{
            $validator = \Validator::make($proveedorData->all(), 
                            ['ruc' => 'required',
                            'contacto' => 'required',
                            'razonSocial' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $proveedor = $this->proveedorRepository->guarda($proveedorData->all());
            DB::commit();
            $proveedorResource =  new ProveedorResource($proveedor);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Proveedor registrado exitosamente');       
            $responseResourse->body($proveedorResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function update($id,Request $proveedorData){
        try{
            DB::beginTransaction();
            $proveedor = $this->proveedorRepository->obtenerPorId($id);
            if (!$proveedor){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Proveedor no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->proveedorRepository->setModel($proveedor);
            $proveedorDataArray= Algorithm::quitNullValuesFromArray($proveedorData->all());
            $this->proveedorRepository->actualiza($proveedorDataArray);
            $proveedor = $this->proveedorRepository->obtenerModelo();
            
            DB::commit();
            $proveedorResource =  new ProveedorResource($proveedor);
            $responseResourse = new ResponseResource(null);
            
            $responseResourse->title('Proveedor actualizado exitosamente');       
            $responseResourse->body($proveedorResource);     
            
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
}
