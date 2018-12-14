<?php
namespace App\Repositories;
use App\Models\Categoria;

	
class CategoriaRepository extends BaseRepository{
    
    protected $productoRepository;

   
    public function __construct(Categoria $categoria,ProductoRepository $productoRepository)
    {
        $this->model = $categoria;
        $this->productoRepository=$productoRepository;
    }

   
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }

    public function setProductosDeCategoriaEnProductoXDescuento($idCategoria, $descuento, $descuentoData){
        $productos = DB::table('producto')->where('deleted', false)->get();
        $listaProductosCategoria = array();
        foreach($productos as $key => $producto){
            if($idCategoria == $producto->idCategoria){
                $listaProductosCategoria[] = $producto;
            }
        }
        foreach($listaProductosCategoria as $key => $producto){
            $this->productoRepository->setModel($producto);
            $this->productoRepository->attachProductoXDescuento($descuento, $producto->id, $descuentoData['idTienda']);
        }



       
    }
    
    public function attachCategoriaXDescuento($descuento, $idCategoria, $idTienda){
        $this->model->descuentosTc()->save($descuento , ['idTienda'=>$idTienda, 'idCategoria'=> $idCategoria, 'deleted'=>false] );
        $this->model->save();
    }
    
}