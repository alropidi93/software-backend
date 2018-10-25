<?php
namespace App\Repositories;
use App\Models\LineaSolicitudCompra;
	
class LineaSolicitudCompraRepository extends BaseRepository{
    protected $proveedor;
    protected $proveedores;
    public function __construct(LineaSolicitudCompra $lineaSolicitudCompra){
        $this->model = $lineaSolicitudCompra;
    }

    public function loadProveedorRelationship($lineaSolicitudCompra=null){
        if (!$lineaSolicitudCompra){
            $this->model = $this->model->load([
                'proveedor'=>function($query){
                    $query->where('proveedor.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }else{
            $this->model =$lineaSolicitudCompra->load([
                'proveedor'=>function($query){
                    $query->where('proveedor.deleted', false)->wherePivot('deleted',false); 
                }
            ]);
        }
        if ($this->model->proveedor){
            $this->proveedor = $this->model->proveedor;
        }
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }
}