<?php
namespace App\Repositories;
use App\Models\Cotizacion;
use App\Models\Usuario;
use App\Models\LineaDeVenta;
	
class CotizacionRepository extends BaseRepository {
    protected $cajero;
    protected $lineaDeVenta;
    protected $lineasDeVenta;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Cotizacion $cotizacion, Usuario $cajero=null, LineaDeVenta $lineaDeVenta)  
    {
        $this->model = $cotizacion;
        $this->lineaDeVenta = $lineaDeVenta;
        $this->cajero = $cajero;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function attachCajero(){
        $this->model->usuario()->associate($this->cajero);
        $this->model->save();
    }
       
    public function loadCajeroRelationship($cotizacion=null){
        if (!$cotizacion){
            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                }
            ]); 
        }else{   
            $this->model =$cotizacion->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);   
        }
        if ($this->model->cajero && !$cotizacion){
            $this->cajero = $this->model->cajero;
        }
    }

    public function loadLineasDeVentaRelationship($cotizacion=null){
        if (!$cotizacion){
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
            $this->model =$cotizacion->load([
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
        $this->cajero = $usuario;       
    }

    public function setCotizacionModel($cotizacion){
        //no tiene similar en pedido transferencia repo
        $this->model = $cotizacion;       
    }

    public function getUsuarioById($idUsuario){
        return $this->cajero->where('idPersonaNatural',$idUsuario)->where('deleted',false)->first();
    }
}