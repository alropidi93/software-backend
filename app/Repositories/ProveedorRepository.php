<?php
namespace App\Repositories;
use App\Models\Proveedor;
use App\Models\ProductoXProveedor;
	
class ProveedorRepository extends BaseRepository{
    public function __construct(Proveedor $proveedor) 
    {
        $this->model = $proveedor;
    }

    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function buscarPorFiltroRs($key, $value){
        return $this->model->whereRaw("\"{$key}\" like ? ",'%'.$value.'%')->where('deleted',false)->get();
    }

    public function listarProveedores($productos){
        //$productos contiene solamente ids
        $proveedores =$this->model->where('deleted',false)->with(['productos' => function ($query) {
            $query->where('producto.deleted',false)->where('productoxproveedor.deleted',false)
            ->join('producto','producto.id', '=', 'productoxproveedor.idProducto');
        }])->get();
        return $proveedores;
    }
}