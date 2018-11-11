<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BoletaResource;
use App\Http\Resources\BoletasResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Boleta;
use App\Repositories\BoletaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

class BoletaController extends Controller
{
    protected $boletaRepository;

    public function __construct(BoletaRepository $boletaRepository){
        BoletaResource::withoutWrapping();
        $this->boletaRepository = $boletaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try{
            $boletas = $this->boletaRepository->listarBoletas();
            foreach ($boletas as $key => $boleta) {
                $this->boletaRepository->loadComprobantePagoRelationship($boleta);
                $this->boletaRepository->loadPersonaNaturalRelationship($boleta);                
            }
            $boletasResource =  new BoletasResource($boletas); 
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de boletas');  
            $responseResourse->body($boletasResource);
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
    public function store(Request $boletaData){
        try{
            $validator = \Validator::make($boletaData->all(), 
                            ['subtotal' => 'required'],
                            ['igv' => 'required']);

            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }

            DB::beginTransaction();
            $this->boletaRepository->guarda($boletaData->all());
            $boletaCreated = $this->boletaRepository->obtenerModelo();
            DB::commit();   

            $this->boletaRepository->loadComprobantePagoRelationship($boletaCreated);
            $this->boletaRepository->loadPersonaNaturalRelationship($boletaCreated);
            
            $boletaResource =  new BoletaResource($boletaCreated);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Boleta creada exitosamente');       
            $responseResourse->body($boletaResource);       
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
    public function show($id){
        try{
            $boleta = $this->boletaRepository->obtenerBoletaPorId($id);
            
            if (!$boleta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Boleta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);
            }
            $this->boletaRepository->setModelboleta($boleta);
            $this->boletaRepository->loadComprobantePagoRelationship($boleta);
            $this->boletaRepository->loadPersonaNaturalRelationship($boleta);
            $boletaResource =  new BoletaResource($boleta);  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Mostrar boleta');  
            $responseResourse->body($boletaResource);
            return $responseResourse;
        }catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            DB::beginTransaction();
            $boleta = $this->boletaRepository->obtenerBoletaPorId($id);
            if (!$boleta){
                $notFoundResource = new NotFoundResource(null);
                $notFoundResource->title('Boleta no encontrada');
                $notFoundResource->notFound(['id'=>$id]);
                return $notFoundResource->response()->setStatusCode(404);;
            }
            $this->boletaRepository->setModelboleta($boleta);
            $this->boletaRepository->softDelete();
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Boleta eliminada');  
            $responseResourse->body(['id' => $id]);
            DB::commit();
            return $responseResourse;
        }catch(\Exception $e){
            DB::rollback();
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }
    }
}
