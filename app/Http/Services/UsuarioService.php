<?php
namespace App\Services;
use App\Models\Usuario;
	
class UsuarioService extends BaseRepository {
    

    
    public function checkIdInUsuarioCollection($id, $userCollection)
    {
        $filtered = $userCollection->where('id', $id);
        $counter = count($filtered);
        return $counter > 0? true: false;
        
        
    }
    
}