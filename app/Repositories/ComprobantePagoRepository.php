<?php
namespace App\Repositories;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Models\LineaDeVenta;
	
class ComprobantePagoRepository extends BaseRepository {
    protected $cajero;
    protected $lineaDeVenta;
    protected $lineasDeVenta;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(ComprobantePago $comprobantePago, Usuario $cajero, LineaDeVenta $lineaDeVenta)  
    {
        $this->model = $comprobantePago;
        $this->lineaDeVenta = $lineaDeVenta;
        $this->cajero = $cajero;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        $this->model = $this->model->create($dataArray);
        return $this->model;
    }

    public function attachCajero(){
        $this->model->usuario()->associate($this->cajero);
        $this->model->save();
    }

    public function loadCajeroRelationship($comprobantePago=null){
        if (!$comprobantePago){
            $this->model->load('usuario');
        }
        else{
            $tienda->load('usuario');
        }
    }

    public function loadLineasDeVentaRelationship($comprobantePago=null){
        if (!$comprobantePago){
            $this->model = $this->model->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false);
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){
                    $query->where('categoria.deleted', false);
                }
            ]);
        }else{
            $this->model =$comprobantePago->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false); 
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){
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

    public function setLineasDeVentaByOwnModel(){
        $this->lineasDeVenta = $this->model->lineasDeVenta;
        unset($this->model->lineasDeVenta);
     }

    public function setCajeroModel($usuario){
        $this->cajero = $usuario;       
    }

    public function setComprobantePagoModel($comprobantePago){
        $this->model = $comprobantePago;       
    }

    // public function checkIfOwnModelTiendaHasJefeTienda(){
    //     return strval($this->model->jefeDeTienda()->where('usuario.deleted',false)
    //             ->whereHas('personaNatural', function ($query){
    //                 $query->where('personaNatural.deleted',false);
    //             })->exists());
    // }
}