<?php
namespace App\Services;
use App\Models\Usuario;
	
class UsuarioService {
    

    
    public function checkIdInUsuarioCollection($id, $userCollection)
    {
        $filtered = $userCollection->where('id', $id);
        $counter = count($filtered);
        return $counter > 0? true: false;
        
        
    }

    public function filterUsuarioCollectionByName($usuarios, $value){
        $usuarios = $usuarios->filter(function ($usuario, $key) use ($value) {
            return  strpos(strtolower($usuario->personaNatural->nombre), $value) !== false;
        });
        return $usuarios->values();
    }

    public function filterUsuarioCollectionByApellido($usuarios, $value){
        $usuarios = $usuarios->filter(function ($usuario, $key) use ($value) {
            return  strpos(strtolower($usuario->personaNatural->apellidos), $value) !== false;
        });
        return $usuarios->values();
    }
    
}