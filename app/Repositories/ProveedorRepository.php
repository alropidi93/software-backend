<?php
namespace App\Repositories;
use App\Models\Proveedor;
	
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
}