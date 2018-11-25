<?php
namespace App\Repositories;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\ProductoXProveedor;
	
class ProveedorRepository extends BaseRepository{
    protected $productos;
    protected $producto;
    public function __construct(Proveedor $proveedor, Producto $producto) 
    {
        $this->model = $proveedor;
        $this->producto = $producto;
    }

    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function buscarPorFiltroRs($key, $value){
        return $this->model->whereRaw("\"{$key}\" like ? ",'%'.$value.'%')->where('deleted',false)->get();
    }

    // public function listarProveedores($productos){
    //     //$productos contiene solamente ids
    //     $proveedores =$this->model->where('deleted',false)->with(['productos' => function ($query) {
    //         $query->where('producto.deleted',false)->where('productoxproveedor.deleted',false)
    //         ->join('producto','producto.id', '=', 'productoxproveedor.idProducto');
    //     }])->get();
    //     return $proveedores;
    // }

    public function loadProductosRelationship($proveedor=null){
        if (!$proveedor){
            $this->model = $this->model->load([
                'productos'=>function($query){
                    $query->where('producto.deleted', false)
                    ->wherePivot('deleted',false)
                    ->orderBy('productoxproveedor.precio'); 
                }
            ]);
        }else{
            $this->model =$proveedor->load([
                'productos'=>function($query){
                    $query->where('producto.deleted', false)->wherePivot('deleted',false)
                    ->orderBy('productoxproveedor.precio'); 
                }
            ]);
        }
        if ($this->model->productos && !$proveedor){
            $this->productos = $this->model->productos;
        }
    }

    public function obtenerProveedoresPorIdProductoArray($product_id_array){

        // return $queryBase = $this->model->where('deleted',false)->with(['productos' => function ($query){
        //     $query->where('producto.id',4);
        //     }])
        //     ->with(['productos' => function ($query){
        //         $query->where('producto.id',5);
        //         }])
        //     ->get();
        

        // $proveedores = $this->model->where('deleted',false)->get();    
        // $proveedores->load([
        //     'productos'=>function($query)use($product_id_array){
        //         foreach ($product_id_array as $key => $product_id) {
        //             $query->where('producto.deleted', false)
        //             ->where('producto.id',$product_id);
        //         }
                 
        //     },
        // ]);  

        return $this->model->where('deleted',false)->whereHas('productos', $filter = function ($query) use ($product_id_array) {
                    $query->whereIn('producto.id', $product_id_array);
                }, '=', count($product_id_array) )
                ->with(['productos'=> $filter])
                ->get();

        
    

        //   $queryBase = $this->model->where('deleted',false);

        // foreach ($product_id_array as $key => $product_id) {
        //     $queryBase =  $queryBase->whereHas('productos',function ($query) use ($product_id){
        //         $query->where('producto.deleted',false)->where('productoxproveedor.deleted',false)
        //         ->where('producto.id',$product_id);
        //     });
        // }
        // return $queryBase->get();
       
    }
}