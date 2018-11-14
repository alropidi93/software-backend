<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Repositories\ComprobantePagoRepository;
use App\Repositories\LineaDeVentaRepository;
use App\Repositories\UsuarioRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComprobantePagoResource;
use App\Http\Resources\ComprobantesPagoResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

class ComprobantePagoController extends Controller
{
    protected $comprobantePagoRepository;
    protected $lineasDeVenta;
    
    public function __construct(ComprobantePagoRepository $comprobantePagoRepository, LineaDeVentaRepository $lineaDeVentaRepository){
        ComprobantePagoResource::withoutWrapping();
        // LineaDeVentaResource::withoutWrapping(); // no tiene similar en pedido trans contro
        $this->comprobantePagoRepository = $comprobantePagoRepository;
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
            $comprobantesPago = $this->comprobantePagoRepository->obtenerTodos();
            foreach ($comprobantesPago as $key => $comprobantePago) {
                $this->comprobantePagoRepository->loadCajeroRelationship($comprobantePago);
                $this->comprobantePagoRepository->loadLineasDeVentaRelationship($comprobantePago);
            }
            $comprobantesPagoResource =  new ComprobantesPagoResource($comprobantesPago);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de comprobantes de pago');  
            $responseResourse->body($comprobantesPagoResource);
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
    public function store(Request $comprobantePagoData)
    {
        try{
            $validator = \Validator::make($comprobantePagoData->all(), 
                            ['subtotal' => 'required',
                            'lineasDeVenta'=>  'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            $idUsuario =  $comprobantePagoData['idUsuario']; //id del cajero en caso tenga
            if($idUsuario){
                $usuario = $this->comprobantePagoRepository->getUsuarioById($idUsuario);
                if (!$usuario){
                    $notFoundResource = new NotFoundResource(null);
                    $notFoundResource->title('No existe este usuario');
                    $notFoundResource->notFound(['id' => $idUsuario]);
                    return $notFoundResource->response()->setStatusCode(404);
                }
                $this->comprobantePagoRepository->setUsuarioModel($usuario);
            }
            
            DB::beginTransaction();
            $this->comprobantePagoRepository->guarda($comprobantePagoData->all());
            $comprobantePago = $this->comprobantePagoRepository->obtenerModelo();

            $list = $comprobantePagoData['lineasDeVenta'];
            $list_collection = new Collection($list);

            foreach ($list_collection as $key => $elem) {
                $this->comprobantePagoRepository->setLineaDeVentaData($elem);
                $this->comprobantePagoRepository->attachLineaDeVentaWithOwnModels();
            }
            DB::commit();
            
            $comprobantePagoCreado = $this->comprobantePagoRepository->obtenerModelo();
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship($comprobantePagoCreado);

            $this->comprobantePagoRepository->loadCajeroRelationship($comprobantePagoCreado); //agregado por Luis
            
            $comprobantePagoResource =  new ComprobantePagoResource($comprobantePagoCreado);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Comprobante de pago creado exitosamente');       
            $responseResourse->body($comprobantePagoResource);       
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
            $comprobantePago = $this->comprobantePagoRepository->obtenerPorId($id);
            if (!$comprobantePago){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Comprobante de pago no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }

            $this->comprobantePagoRepository->setModel($comprobantePago);
            $this->comprobantePagoRepository->loadCajeroRelationship();
            $this->comprobantePagoRepository->loadLineasDeVentaRelationship(); //no tiene similar en pedido trans contr

            $comprobantePagoResource =  new ComprobantePagoResource($comprobantePago);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar comprobante de pago');  
            $responseResourse->body($comprobantePagoResource);
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
    public function update(Request $comprobantePagoData, $id)
    {
        try{
            DB::beginTransaction();
            $comprobantePago = $this->comprobantePagoRepository->obtenerPorId($id);
            if (!$comprobantePago){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Comprobante de pago no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            
            $this->comprobantePagoRepository->setModel($comprobantePago);
            $comprobantePagoDataArray= Algorithm::quitNullValuesFromArray($comprobantePagoData->all());
            $this->comprobantePagoRepository->actualiza($comprobantePagoDataArray);
            $comprobantePago = $this->comprobantePagoRepository->obtenerModelo();
            DB::commit();

            $comprobantePagoResource =  new ComprobantePagoResource($comprobantePago);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Comprobante de pago actualizado exitosamente');       
            $responseResourse->body($comprobantePagoResource);   
            return $responseResourse;
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
            $comprobantePago = $this->comprobantePagoRepository->obtenerPorId($id);
            if (!$comprobantePago){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Comprobante de pago no encontrado');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->comprobantePagoRepository->setModel($comprobantePago);
            $this->comprobantePagoRepository->softDelete();
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Comprobante de pago eliminado');  
            $responseResourse->body(['id' => $id]);
            DB::commit();

            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }
}
