<?php
namespace App\Services;
use App\Models\Almacen;
use App\Models\Producto;
	
class AlmacenService {
    
    public function getProductosNoStockeadosEnAlmacenConTipoAlmacen($almacen, $tipo){
        

        // $consulta= $seccion->horarios()->whereHas('usuario',function($query){
        //     $query->where('idTipo',1);
        //   });

        // return $almacen->join('productoxalmacen', 'almacen.id', '=', 'productoxalmacen.idAlmacen')
        // ->whereRaw("lower(tipo) like ? ",'%'.$value.'%')->where('tipoProducto.deleted','=',false)->get();
        
        // $almacen->productos()->where('deleted',false)->get();

        // $productos = Producto::where('deleted',false)->whereHas('almacenes',function($query) use ($almacen,$tipo){
        //     $query->where('almacen.deleted',false)->where('id',$almacen->id)->wherePivot('deleted',false);
        //     // ->whereHas('tipoStocks', function ($query2)use ($tipo){
        //     //     $query2->where('tipoStock.deleted,',false)->where('key',$tipo)->wherePivot('deleted',false);
        //     // });
        // })->get();
        $productos =  Producto::whereDoesntHave('almacenes')->get();
        return $productos;

    }
    
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