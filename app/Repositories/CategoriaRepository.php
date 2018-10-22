<?php
namespace App\Repositories;
use App\Models\Categoria;

	
class CategoriaRepository extends BaseRepository{
    

   
    public function __construct(Categoria $categoria)
    {
        $this->model = $categoria;
        
    }

   
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }
    
}