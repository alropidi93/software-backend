<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Usuario;
use App\Repositories\DevolucionRepository;
use App\Repositories\LineaDeVentaRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\ComprobantePagoRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\DevolucionResource;
use App\Http\Resources\DevolucionesResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class DevolucionController extends Controller
{
    protected $devolucionRepository;
    protected $comprobantePagoRepository;
    protected $lineasDeVenta;
    protected $usuarioRepository;
    
    public function __construct(DevolucionRepository $devolucionRepository=null, ComprobantePagoRepository $comprobantePagoRepository=null, LineaDeVentaRepository $lineaDeVentaRepository=null, UsuarioRepository $usuarioRepository=null){
        DevolucionResource::withoutWrapping();
        $this->devolucionRepository = $devolucionRepository;
        $this->comprobantePagoRepository = $comprobantePagoRepository;
        $this->lineaDeVentaRepository = $lineaDeVentaRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $devoluciones = $this->devolucionRepository->obtenerTodos();
            foreach ($devoluciones as $key => $devolucion) {
                $this->devolucionRepository->loadUsuarioRelationship($devolucion);
                $this->devolucionRepository->loadPersonaNaturalRelationship();
                $this->devolucionRepository->loadPersonaJuridicaRelationship();
                $this->devolucionRepository->loadLineasDeVentaRelationship($devolucion);
            }
            $devolucionesResource = new DevolucionesResource($devoluciones);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de devoluciones');  
            $responseResourse->body($devolucionesResource);
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
    public function store(Request $request)
    {
        try{
            $validator = \Validator::make($request->all(),
                            ['descripcion' => 'required',
                            'monto' => 'required',
                            'idComprobantePago' => 'required',
                            'idCliente' => 'required', //natural o juridico
                            'lineasDeVenta'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            
            $idUsuario = array_key_exists('idUsuario', $request)? $request['idUsuario']:null;
            if($idUsuario){
                $usuario = $this->usuarioRepository->obtenerUsuarioPorId($idUsuario);
                if (!$usuario){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este usuario');
                    $notFoundResource->notFound(['id' => $idUsuario]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                $this->devolucionRepository->setUsuarioModel($usuario);
            }

            $idComprobantePago = $request['idComprobantePago'];
            $comprobantePago = $this->comprobantePagoRepository->obtenerPorId($idComprobantePago);
            if(!$comprobantePago){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('No existe el comprobante de pago');
                $notFoundResource->notFound(['id' => $idComprobantePago]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $idCliente = $request['idCliente'];
            //buscar al cliente natural o juridico
            $clienteNatural = $this->devolucionRepository->getClienteNaturalById($idCliente);
            if(!$clienteNatural){
                $clienteJuridico = $this->devolucionRepository->getClienteJuridicoById($idCliente);
                if(!$clienteJuridico){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este cliente');
                    $notFoundResource->notFound(['id' => $idCliente]);
                    return $notFoundResource->response()->setStatusCode(404);
                }else{
                    $this->devolucionRepository->setPersonaJuridicaModel($clienteJuridico);
                }
            }else{
                $this->devolucionRepository->setPersonaNaturalModel($clienteNatural);
            }
            
            DB::beginTransaction();
            $this->devolucionRepository->guarda($request->all());
            $list = $request['lineasDeVenta'];
            $list_collection = new Collection($list);
            foreach ($list_collection as $key => $elem) {
                $this->devolucionRepository->setLineaDeVentaData($elem);
                $this->devolucionRepository->attachLineaDeVentaWithOwnModels();
            }
            $this->devolucionRepository->loadLineasDeVentaRelationship();
            
            $devolucionCreada = $this->devolucionRepository->obtenerModelo();
            $this->devolucionRepository->setModel($devolucionCreada);
            $this->devolucionRepository->loadUsuarioRelationship();
            if($clienteNatural){
                $this->devolucionRepository->setPersonaNaturalModel($clienteNatural);
                $this->devolucionRepository->loadPersonaNaturalRelationship();
                $this->devolucionRepository->attachPersonaNatural();
            }else if($clienteJuridico){
                $this->devolucionRepository->setPersonaJuridicaModel($clienteJuridico);
                $this->devolucionRepository->loadPersonaJuridicaRelationship();
                $this->devolucionRepository->attachPersonaJuridica();
            }
            DB::commit();

            $devolucionResource =  new DevolucionResource($devolucionCreada);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Devolucion creada exitosamente');       
            $responseResourse->body($devolucionResource);       
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
        //
    }
}
