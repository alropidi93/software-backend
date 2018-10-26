<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Models\SolicitudCompra;
	
class SolicitudCompraRepository extends BaseRepository {
    protected $lineaSolicitudCompra;
    protected $lineasSolicitudCompra;
    protected $tienda;
    /**
     * Create a new ProductoRepository instance.
     * @return void
     */
    public function __construct(SolicitudCompra $solicitudCompra, Tienda $tienda=null){
        $this->model = $solicitudCompra;
        $this->tienda = $tienda;
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
                    $query->where('lineaSolicitudCompra.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        else{
            $this->model =$producto->load([
                'lineasSolicitudCompra'=>function($query){
                    $query->where('lineaSolicitudCompra.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        if ($this->model->lineasSolicitudCompra){
            $this->lineasSolicitudCompra = $this->model->lineasSolicitudCompra;
        }
    }

    public function setLineaSolicitudCompra($lineaSolicitudCompra){
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