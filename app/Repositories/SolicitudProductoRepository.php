<?php
namespace App\Repositories;
use App\Models\SolicitudProducto;

	
class SolicitudProductoRepository extends BaseRepository{
    

   
    public function __construct(SolicitudProducto $solicitudProducto)
    {
        $this->model = $solicitudProducto;
        
    }

   
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }
    
    
}