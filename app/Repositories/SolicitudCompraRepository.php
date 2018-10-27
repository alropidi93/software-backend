<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Models\SolicitudCompra;
use App\Models\LineaSolicitudCompra; ///no poner esta linea de mierda me hizo perder 2 horas :')
	
class SolicitudCompraRepository extends BaseRepository {
    protected $lineaSolicitudCompra;
    protected $lineasSolicitudCompra;
    protected $tienda;
    /**
     * Create a new ProductoRepository instance.
     * @return void
     */
    public function __construct(SolicitudCompra $solicitudCompra, LineaSolicitudCompra $lineaSolicitudCompra, Tienda $tienda=null){
        $this->model = $solicitudCompra;
        $this->tienda = $tienda;
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function obtenerTiendaPorId($idTienda){
        return $this->tienda->where('id',$idTienda)->where('deleted',false)->first();
    }

    public function loadLineasSolicitudCompraRelationship($solicitudCompra=null){
        if (!$solicitudCompra){
            $this->model = $this->model->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                }
            ]);
        }
        else{
            $this->model =$solicitudCompra->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false); 
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                }
            ]);
        }
        if ($this->model->lineasSolicitudCompra){
            $this->lineasSolicitudCompra = $this->model->lineasSolicitudCompra;
        }
    }

    public function setLineaSolicitudCompraModel($lineaSolicitudCompra){
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;
    }

    public function attachLineaSolicitudCompra($lineaSolicitudCompra){
        $this->model->lineasSolicitudCompra()->save($lineaSolicitudCompra , ['deleted'=>false] );
        $this->model->save();
    }

    // public function checkProductoProveedorOwnModelsRelationship(){
    //     return $this->model->proveedores()->where('id',$this->proveedor->id)->where('proveedor.deleted' , false)->exists();
    // }
}