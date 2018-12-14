<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\SolicitudCompra;
use App\Repositories\SolicitudCompraRepository;
use App\Repositories\MovimientoTipoStockRepository;
use App\Repositories\LineaSolicitudCompraRepository;
use App\Repositories\LineaPedidoTransferenciaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudCompraResource;
use App\Http\Resources\SolicitudesCompraResource;
use App\Http\Resources\LineasSolicitudCompraResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Services\ProductoService;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class SolicitudCompraController extends Controller
{
    protected $solicitudCompraRepository;
    protected $movimientoTipoStockRepository;
    protected $lineaSolicitudCompraRepository;
    protected $lineaPedidoTransferenciRepository;
    protected $usuarioRepository;
    public function __construct(SolicitudCompraRepository $solicitudCompraRepository,
                                MovimientoTipoStockRepository $movimientoTipoStockRepository,
                                LineaSolicitudCompraRepository $lineaSolicitudCompraRepository,
                                LineaPedidoTransferenciaRepository $lineaPedidoTransferenciRepository,
                                UsuarioRepository $usuarioRepository){
        SolicitudCompraResource::withoutWrapping();
        $this->solicitudCompraRepository = $solicitudCompraRepository;
        $this->movimientoTipoStockRepository = $movimientoTipoStockRepository;
        $this->lineaSolicitudCompraRepository = $lineaSolicitudCompraRepository;
        $this->lineaPedidoTransferenciaRepository = $lineaPedidoTransferenciRepository;
        $this->usuarioRepository = $usuarioRepository;
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
                $this->solicitudCompraRepository->loadLineasSolicitudCompraRelationshipWithExtraRelationships($solicitudCompra);
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

    public function efectuarCompra(Request $data){
        
        try{
            $validator = \Validator::make($data->all(), 
                            [
                                'idUsuario'=>'required',
                                'idProveedor' => 'required',
                                'producto_id_cantidad_list' => 'required'
                            ]
                        );
            
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            $usuario = $this->usuarioRepository->obtenerUsuarioPorId($data['idUsuario']);
            if (!$usuario){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Usuario no encontrado');
                $notFoundResource->notFound(['idUsuario'=>$data['idUsuario']]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            
            $proveedor = $this->solicitudCompraRepository->obtenerProveedorPorId($data['idProveedor']);
            if (!$proveedor){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Proveedor no encontrado');
                $notFoundResource->notFound(['idProveedor'=>$data['idProveedor']]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $prod_id_cantidad_collection =  new Collection($data['producto_id_cantidad_list']);
            $productoService =  new ProductoService();
            $product_id_array = $productoService->obtenerIdArray($prod_id_cantidad_collection);
            
            if(!($proveedor->tieneExactamenteProductos($product_id_array))){
                $errorResource = new ErrorResource(null);
                $errorResource->title('Error de petición');
                $errorResource->message('El proveedor no vende exactamente los productos solicitados');
                return $errorResource->response()->setStatusCode(400);

            }
            /* Datos de prueba
            $product_id_array=[1,5];
            $prod_id_cantidad_collection = array(array('id'=>1,'cantidad'=>20),array('id'=>5,'cantidad'=>29));
            */
            DB::beginTransaction();
            $solicitudCompra = $this->solicitudCompraRepository->obtenerSolicitudDisponible();
            
            $this->solicitudCompraRepository->setModel($solicitudCompra);
            Log::info(count($prod_id_cantidad_collection));
            $array_id_success =[];
            foreach ($prod_id_cantidad_collection as $key => $productoObj) {
                Log::info("key: ".$key);
                Log::info("id Producto: ".$productoObj['id']);
                
                $lineaSC = $this->solicitudCompraRepository->obtenerLineaPorProductoIdDisponible($productoObj['id']);
                Log::info(json_encode($lineaSC));
                if(!$lineaSC) continue; //verificamos que la linea de solicitud de compra del producto este presente
                Log::info(json_encode($lineaSC->cantidad));
                Log::info(json_encode($productoObj['cantidad']));
                if (!$lineaSC->cantidad == $productoObj['cantidad']) continue; // verificamos que las cantidades enviadas en el post sean las mismas de la linea de solicitud de compra
                //return json_encode($this->lineaSolicitudCompraRepository);
                $this->lineaSolicitudCompraRepository->setModel($lineaSC);
                $this->lineaSolicitudCompraRepository->actualiza(['idProveedor'=>$proveedor->id]);
                Log::info("sacaremos lineas de pedido de transeferencia asociada a la linea de solicitud de compra");
                $lineasPT = $this->lineaSolicitudCompraRepository->obtenerLineasPedidoTransferencia();
                Log::info(json_encode($lineasPT));

                $tipoStockPrincipal = $this->solicitudCompraRepository->obtenerStockPrincipal();
                Log::info(json_encode($tipoStockPrincipal));
                foreach ($lineasPT as $lineaPT ){
                    
                    Log::info(json_encode($lineaPT));
                    $almacenOrigen = $this->lineaPedidoTransferenciaRepository->obtenerAlmacenOrigen($lineaPT);
                    if(!$almacenOrigen) continue;// si no encuentra almacen de origen no actualiza
                    Log::info("Va a guardar el movimiento");
                    $this->movimientoTipoStockRepository->crear(['idAlmacen' => $almacenOrigen->id,
                                                                         'idProducto'=>$productoObj['id'],
                                                                         'idTipoStock'=> $tipoStockPrincipal->id,
                                                                         'idUsuario'=>$usuario->idPersonaNatural,
                                                                         'cantidad'=>$lineaPT->cantidad,
                                                                         'signo'=> '+',
                                                                         'tipo'=> 'compra',
                                                                         'deleted'=>false]);
                    
                }
                array_push($array_id_success,$productoObj['id']);
                
            }
            
           
           
            $this->solicitudCompraRepository->loadSpecifLineasRelationship($array_id_success);
            
            DB::commit();
            $solicitudCompraResource =  new SolicitudCompraResource($solicitudCompra);  
            $responseResource = new ResponseResource(null);
            $responseResource->title('Solicitud de compra con lineas con compras efectuadas');  
            $responseResource->body($solicitudCompraResource);
            return $responseResource;
        }
        catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }

    }

    public function listarLineasComprasEfectuadas(){
        try{
            
            $filter = Input::get('filterBy');
            $value = strtolower(Input::get('value'));
            if (count(Input::get())>0){
                if (!$filter || !$value){
                    $errorResource = new ErrorResource(null);
                    $errorResource->title('Error de búsqueda');
                    $errorResource->message('Parámetros inválidos para la búsqueda');
                    return $errorResource->response()->setStatusCode(400);
                }
            }
            
            
            $responseResource = new ResponseResource(null);
            
            
            if ($filter && $value){
                
                switch ($filter) {
                    case 'idProducto':
                                     
                        $lineasSolicitudCompra = $this->solicitudCompraRepository->obtenerLineasConProveedorConFiltro($filter, $value);
                        foreach ($lineasSolicitudCompra as $key => $pt) {
                            $this->lineaSolicitudCompraRepository->loadProveedorRelationship($pt);
                            $this->lineaSolicitudCompraRepository->loadProductoRelationship($pt);
                            
                        }
                        $lineasSolicitudCompraResource =  new LineasSolicitudCompraResource($lineasSolicitudCompra);
                        $responseResource->title('Lista de lineas de solicitud de compras efectuadas historicas filtradas por id de producto');       
                        $responseResource->body($lineasSolicitudCompraResource);
                        break;
    
                    case 'nombreProducto':
                      
                        $lineasSolicitudCompra = $this->solicitudCompraRepository->obtenerLineasConProveedorConFiltro($filter, $value);
                        foreach ($lineasSolicitudCompra as $key => $pt) {
                            $this->lineaSolicitudCompraRepository->loadProveedorRelationship($pt);
                            $this->lineaSolicitudCompraRepository->loadProductoRelationship($pt);
                            
                        }
                        $lineasSolicitudCompraResource =  new LineasSolicitudCompraResource($lineasSolicitudCompra);
                        $responseResource->title('Lista de lineas de solicitud de compras efectuadas filtrados por nombre de producto');       
                        $responseResource->body($lineasSolicitudCompraResource);
                        break;
                    
    
                    case 'nombreProveedor':
                        $lineasSolicitudCompra = $this->solicitudCompraRepository->obtenerLineasConProveedorConFiltro($filter, $value);
                        foreach ($lineasSolicitudCompra as $key => $pt) {
                            $this->lineaSolicitudCompraRepository->loadProveedorRelationship($pt);
                            $this->lineaSolicitudCompraRepository->loadProductoRelationship($pt);
                            
                        }
                        $lineasSolicitudCompraResource =  new LineasSolicitudCompraResource($lineasSolicitudCompra);
                        $responseResource->title('Lista de lineas de solicitud de compras efectuadas filtrados por nombre de proveedor');       
                        $responseResource->body($lineasSolicitudCompraResource);
                        break;
                   
                    
    
                    default:
                        $errorResource = new ErrorResource(null);
                        $errorResource->title('Error de búsqueda');
                        $errorResource->message('Valor de filtro inválido');
                        return $errorResource->response()->setStatusCode(400);
                }
                return $responseResource;
            
            }

            
            $lineasSolicitudCompra = $this->solicitudCompraRepository->obtenerLineasConProveedor();
            foreach ($lineasSolicitudCompra as $key => $pt) {
                $this->lineaSolicitudCompraRepository->loadProveedorRelationship($pt);
                $this->lineaSolicitudCompraRepository->loadProductoRelationship($pt);
             
                
            }
            $lineasSolicitudCompraResource =  new LineasSolicitudCompraResource($lineasSolicitudCompra);  
            
            
            $responseResource->title('Lista de lineas de solicitudes de compras efectuadas historicas');  
            $responseResource->body($lineasSolicitudCompraResource);
            return $responseResource;
        }
        catch(\Exception $e){
                   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
            
        }
    }
}
