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
use App\Models\ProductoXProveedor;
use App\Repositories\ProveedorRepository;
use App\Repositories\ProductoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class ProveedorController extends Controller
{
    protected $proveedorRepository;

    public function __construct(ProveedorRepository $proveedorRepository, ProductoRepository $productoRepository){
        ProveedorResource::withoutWrapping();
        $this->proveedorRepository = $proveedorRepository;
        $this->productoRepository = $productoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
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

    public function store(Request $proveedorData){
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

    public function show($id){
        try{
            $proveedor = $this->proveedorRepository->obtenerPorId($id);
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
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function busquedaPorFiltro(){
        try{
            $proveedor = $this->proveedorRepository->obtenerModelo();
            $filter = Input::get('filterBy');
            $value = strtolower(Input::get('value'));
            $responseResource = new ResponseResource(null);
            if (!$filter || !$value){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de búsqueda');
                $errorResource->message('Parámetros inválidos para la búsqueda');
                return $errorResource->response()->setStatusCode(400);
            }
            switch ($filter) {
                case 'ruc':      
                    $proveedores = $this->proveedorRepository->buscarPorFiltro($filter, $value);
                    $proveedoresResource =  new ProveedoresResource($proveedores);
                    $responseResource->title('Lista de proveedores filtrados por RUC');       
                    $responseResource->body($proveedoresResource);
                    break;
                case 'razonSocial':
                    $proveedores = $this->proveedorRepository->buscarPorFiltroRs($filter, $value);
                    $proveedoresResource =  new ProveedoresResource($proveedores);
                    $responseResource->title('Lista de proveedores filtrados por razón social');       
                    $responseResource->body($proveedoresResource);
                    break;
                case 'contacto':      
                    $proveedores = $this->proveedorRepository->buscarPorFiltro($filter, $value);
                    $proveedoresResource =  new ProveedoresResource($proveedores);
                    $responseResource->title('Lista de proveedores filtrados por contacto');       
                    $responseResource->body($proveedoresResource);
                    break;
                default:
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Valor de filtro inválido');
                    return $errorResource->response()->setStatusCode(400);
            }
            return $responseResource; 
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function listarProveedores(Request $productosData){
        //devuelve una lista de proveedores que ofrezcan los productos en $productosData
        // $productosData contiene una lista de ids de producto
        try{
            $validator = \Validator::make($productosData->all(), 
                            ['productos' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $listaProveedores = 40;
            $productos = $productosData['productos'];
            //verificar que los productos existan
            foreach($productos as $key => $producto){
                $producto = $this->productoRepository->obtenerPorId($producto['idProducto']);
                if(!$producto){
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Uno de los productos no existe');
                    return $errorResource->response()->setStatusCode(400);
                }
            }
            //buscar los proveedores
            return $this->proveedorRepository->listarProveedores($productos);
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
}
