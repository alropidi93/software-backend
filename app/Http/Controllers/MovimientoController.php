<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MovimientoResource;
use App\Http\Resources\MovimientosResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Http\Resources\ResponseResource;
use App\Models\Usuario;
use App\Repositories\MovimientoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\Algorithm;
use Illuminate\Support\Facades\Input;

/*
Clase usada para la trazabilidad, solo se puede registrar un movimiento
y listar los movimientos. No se puede editar ni eliminar.
*/
class MovimientoController extends Controller
{
    protected $movimientoRepository;

    public function __construct(MovimientoRepository $movimientoRepository){
        MovimientoResource::withoutWrapping();
        $this->movimientoRepository = $movimientoRepository;
        //falta crear el repository
    }

    public function index() 
    {
        try{
            $movimientoResource =  new MovimientoResource($this->movimientoRepository->obtenerTodos());  
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Lista de movimientos');  
            $responseResourse->body($movimientoResource);
            return $responseResourse;
        }
        catch(\Exception $e){
            return (new ExceptionResource($e))->response()->setStatusCode(500);   
        }  
    }

    public function store(Request $movimientoData)
    {
        try{
            $validator = \Validator::make($movimientoData->all(), 
                            ['descripcion' => 'required',
                            'fecha' => 'required',
                            'idUsuario' => 'required']);
            if ($validator->fails()) {
                return (new ValidationResource($validator))->response()->setStatusCode(422);
            }
            DB::beginTransaction();
            $movimiento = $this->movimientoRepository->guarda($movimientoData->all());
            DB::commit();
            $movimientoResource =  new MovimientoResource($movimiento);
            $responseResourse = new ResponseResource(null);
            $responseResourse->title('Movimiento registrado exitosamente');       
            $responseResourse->body($movimientoResource);       
            return $responseResourse;
        }
        catch(\Exception $e){
            DB::rollback();   
            return (new ExceptionResource($e))->response()->setStatusCode(500);
        }
    }

    public function update($id,Request $movimientoData) {
        //no se debe editar un movimiento
    }

    public function destroy($id) {
        //no se debe eliminar un movimiento
    }
}
