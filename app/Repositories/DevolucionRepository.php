<?php
namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\ComprobantePago;
use App\Models\Devolucion;
use App\Models\Usuario;
use App\Models\PersonaNatural;
use App\Models\PersonaJuridica;
use App\Models\LineaDeVenta;
	
class DevolucionRepository extends BaseRepository {
    protected $usuario;
    protected $personaNatural;
    protected $personaJuridica;
    protected $lineaDeVenta;
    protected $lineasDeVenta;
    protected $usuarioRepository;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Devolucion $devolucion, Usuario $usuario=null, PersonaNatural $personaNatural=null, PersonaJuridica $personaJuridica=null, LineaDeVenta $lineaDeVenta, UsuarioRepository $usuarioRepository)  
    {
        $this->model = $devolucion;
        $this->lineaDeVenta = $lineaDeVenta;
        $this->usuario = $usuario;
        $this->personaNatural = $personaNatural;
        $this->personaJuridica = $personaJuridica;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function attachUsuario(){
        $this->model->usuario()->associate($this->cajero);
        $this->model->save();
    }

    public function attachPersonaNatural(){
        $this->model->personaNatural()->associate($this->personaNatural);
        $this->model->save();
    }

    public function attachPersonaJuridica(){
        $this->model->personaJuridica()->associate($this->personaJuridica);
        $this->model->save();
    }
       
    public function loadUsuarioRelationship($devolucion=null){
        if (!$devolucion){
            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                }
            ]); 
        }else{   
            $this->model =$devolucion->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);   
        }
        if ($this->model->usuario && !$devolucion){
            $this->usuario = $this->model->usuario;
        }
    }

    public function loadPersonaNaturalRelationship($devolucion=null){
        if (!$devolucion){
            $this->model->load(['personaNatural' => function ($query) {
                $query->where('deleted', false);
            }]);
        }else{
            $devolucion->load(['personaNatural' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
    }

    public function loadPersonaJuridicaRelationship($devolucion=null){
        if (!$devolucion){
            $this->model->load(['personaJuridica' => function ($query) {
                $query->where('deleted', false);
            }]);
        }else{
            $devolucion->load(['personaJuridica' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
    }

    public function loadLineasDeVentaRelationship($devolucion=null){
        if (!$devolucion){
            $this->model = $this->model->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false);
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){ //esta parte no es tan necesaria para este request
                    $query->where('categoria.deleted', false);
                }
            ]);
        }else{
            $this->model =$devolucion->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false); 
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){//esta parte no es tan necesaria para este request
                    $query->where('categoria.deleted', false);
                }
            ]);   
        }
    }

    public function setLineaDeVentaData($dataLineaDeVenta){
        $this->lineaDeVenta =  new LineaDeVenta;
        $this->lineaDeVenta['idProducto'] =  $dataLineaDeVenta['idProducto'];
        $this->lineaDeVenta['cantidad'] = $dataLineaDeVenta['cantidad'];
        $this->lineaDeVenta['subtotalLinea'] = array_key_exists('subtotalLinea',$dataLineaDeVenta)? $dataLineaDeVenta['subtotalLinea']:0;
        $this->lineaDeVenta['deleted'] =  false; //default value
    }

    public function attachLineaDeVentaWithOwnModels(){
        $ans = $this->model->lineasDeVenta()->save($this->lineaDeVenta);
    }

    public function obtenerLineasDeVentaFromOwnModel(){
        return $this->lineasDeVenta;
    }

    public function setLineasDeVentaByOwnModel(){
        $this->lineasDeVenta = $this->model->lineasDeVenta;
        unset($this->model->lineasDeVenta);
     }

    public function setUsuarioModel($usuario){
        $this->usuario = $usuario;
    }

    public function setPersonaNaturalModel($personaNatural){
        $this->personaNatural = $personaNatural;
    }

    public function setPersonaJuridicaModel($personaJuridica){
        $this->personaJuridica = $personaJuridica;
    }

    public function setDevolucionModel($devolucion){
        $this->model = $devolucion;
    }

    public function getUsuarioById($idUsuario){
        return $this->usuario->where('idPersonaNatural',$idUsuario)->where('deleted',false)->first();
    }

    public function getClienteNaturalById($idClienteNatural){
        return $this->personaNatural->where('id',$idClienteNatural)->where('deleted',false)->first();
    }

    public function getClienteJuridicoById($idClienteJuridico){
        return $this->personaJuridica->where('id',$idClienteJuridico)->where('deleted',false)->first();
    }
}