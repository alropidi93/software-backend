<?php
namespace App\Repositories;
use App\Models\Producto;
use App\Models\ProductoXAlmacen;
use App\Models\Proveedor;
use App\Models\TipoProducto;
use App\Models\UnidadMedida;
use App\Models\Categoria;
use App\Models\Almacen;
use App\Models\Tienda;
use App\Models\TipoStock;
use App\Models\Descuento;
	
class ProductoRepository extends BaseRepository {
    protected $tipoProducto;
    protected $unidadMedida;
    protected $proveedor;
    protected $categoria;
    protected $proveedores;
    protected $almacenes;
    protected $almacen;
    protected $tienda;
    protected $tipoStock;
    protected $descuento;
    /** 
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @param  App\Models\Proveedor $proveedor
     * @return void
     */
    public function __construct(Producto $producto, TipoProducto $tipoProducto,UnidadMedida $unidadMedida ,Proveedor $proveedor, Categoria $categoria, Almacen $almacen, Tienda $tienda,TipoStock $tipoStock, Descuento $descuento) 
    {
        $this->model = $producto;
        $this->tipoProducto = $tipoProducto;
        $this->unidadMedida = $unidadMedida;
        $this->proveedor = $proveedor;
        $this->categoria = $categoria;
        $this->almacen = $almacen;
        $this->tienda = $tienda;
        $this->tipoStock = $tipoStock;
        $this->descuento = $descuento;
        
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;

        return $this->model = $this->model->create($dataArray);
        
    }
    public function guardaPrecioPorAlmacen($productoData)
    {
        $productoData['deleted'] =false;

        return $this->model = $this->model->create($productoData);
        
    }

    public function obtenerProductosHabilitados(){
        $list = $this->model->where('deleted',false)->where('habilitado', true)->get();
        return $list;
    }

    public function loadUnidadMedidaRelationship($producto=null){
    
        if (!$producto){
            $this->model = $this->model->load([
                'unidadMedida'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'unidadMedida'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->unidadMedida){
            $this->unidadMedida = $this->model->unidadMedida;
        }
    }
    public function obtenerStockPrincipal(){
        return $this->tipoStock->where('key',1)->where('deleted',false)->first();
    }

    public function loadCategoriaRelationship($producto=null){
    
        if (!$producto){
            $this->model = $this->model->load([
                'categoria'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'categoria'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->categoria){
            $this->categoria = $this->model->categoria;
        }
    }
    public function loadDescuentoRelationship($producto=null){
    
        if (!$producto){
            $this->model = $this->model->load([
                'descuento'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'descuento'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->descuento){
            $this->descuento = $this->model->descuento;
        }
    }

    public function loadTipoProductoRelationship($producto=null){
      
        if (!$producto){
               

            $this->model = $this->model->load([
                'tipoProducto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$producto->load([
                'tipoProducto'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->tipoProducto){
            $this->tipoProducto = $this->model->tipoProducto;
        }
    }

    public function loadAlmacenesRelationship($producto=null){
        if (!$producto){
            $this->model = $this->model->load([
                'almacenes'=>function($query){
                    $query->where('almacen.deleted', false); 
                },
                'almacenes.tienda'=>function($query){
                    $query->where('tienda.deleted', false); 
                },

            ]);
            foreach ($this->model->almacenes as $key => $almacen) {
                $almacen->pivot->load([
                    'tipoStock'=>function($query){
                        $query->where('tipoStock.deleted', false); 
                    }
                ]);
            }    
        }else{
            $this->model =$producto->load([
                'almacenes'=>function($query){
                    $query->where('almacen.deleted', false); 
                },
                'almacenes.tienda'=>function($query){
                    $query->where('tienda.deleted', false); 
                },
            ]);
            foreach ($this->model->almacenes as $key => $almacen) {
                $almacen->pivot->load([
                    'tipoStock'=>function($query){
                        $query->where('tipoStock.deleted', false); 
                    }
                ]);
            }
        }
        if ($this->model->almacenes && !$producto ){
            $this->almacenes = $this->model->almacenes;
        }
    }

    public function loadProveedoresRelationship($producto=null){
        if (!$producto){
            $this->model = $this->model->load([
                'proveedores'=>function($query){
                    $query->where('proveedor.deleted', false)
                    ->wherePivot('deleted',false)
                    ->orderBy('productoxproveedor.precio'); 
                }
            ]);
        }else{   
            $this->model =$producto->load([
                'proveedores'=>function($query){
                    $query->where('proveedor.deleted', false)->wherePivot('deleted',false)
                    ->orderBy('productoxproveedor.precio'); 
                    
                }
            ]);
        }
        if ($this->model->proveedores && !$producto){
            $this->proveedores = $this->model->proveedores;
        }
    }

    public function buscarPorTipo($value){
        return $this->model->whereHas('tipoProducto',function($q) use ($value) {
            $q->whereRaw("lower(tipo) like ? ",'%'.$value.'%')->where('tipoProducto.deleted',false);          
        })->get();     

        // return $this->model->join('tipoProducto', 'tipoProducto.id', '=', 'producto.idTipoProducto')
        //     ->whereRaw("lower(tipo) like ? ",'%'.$value.'%')->where('tipoProducto.deleted','=',false)->get();
    }

    public function buscarPorCategoria($value){
        return $this->model->whereHas('categoria',function($q) use ($value) {
            $q->where('categoria.id',$value)->where('categoria.deleted',false);          
        })->get();
    }
    public function obtenerProveedorPorId($idProveedor){
        return $this->proveedor->where('id',$idProveedor)->where('deleted',false)->first();
    }

    public function setProveedorModel($proveedor){
        $this->proveedor = $proveedor;
    }

    public function attachProveedor($proveedor,$pivotData){
       
        $this->model->proveedores()->save($proveedor , $pivotData);
        $this->model->save();
    }

    public function updateStock($idTipoStock, $idAlmacen, $cantidad){
        $productoxalmacen =  ProductoXAlmacen::where('idAlmacen',$idAlmacen)
                            ->where('idProducto',$this->model->id)
                            ->where('idTipoStock',$idTipoStock)
                            ->where('deleted',false)
                            ->update(['cantidad'=>$cantidad]);
    }

    public function actualizarPrecio($idTipoStock, $idAlmacen, $precio){
        $productoxalmacen =  ProductoXAlmacen::where('idAlmacen',$idAlmacen)
                            ->where('idProducto',$this->model->id)
                            ->where('idTipoStock',$idTipoStock)
                            ->where('deleted',false)
                            ->update(['precio'=>$precio]);
    }

    public function setPrecioxAlmacen($idProducto){
        $producto = $this->productoRepository->obtenerPorId($idProducto);
        $precio = $producto['precio'];
        DB::table('productoxalmacen')
            ->where('idProducto', $idProducto)
            ->where('idTipoStock', 1)
            ->update(['precio' => $precio]);
    }

    public function checkProductoProveedorOwnModelsRelationship(){
        return $this->model->proveedores()->where('id',$this->proveedor->id)->where('proveedor.deleted' , false)->exists();
    }

    public function listarConStock(){
        $productos = $this->model->where('habilitado',true)->where('deleted',false)->get();
        foreach ($productos as $key => $producto) {
            $this->loadAlmacenesRelationship($producto);
        }
        return $productos;
    }

    public function listarConStockPorTienda($idTienda){
        $productos = $this->model->where('deleted',false)->get();
        foreach ($productos as $key => $producto) {
            $this->loadAlmacenesRelationship($producto)->where;
        }
        return $productos;
    }
    public function listarProductosDeAlmacenTest($idAlmacen){
        $productos = $this->model->where('deleted',false)->get();
        foreach ($productos as $key => $producto) {
            $this->loadAlmacenesRelationship($producto);
        }
        return $productos;
    }

    public function listarProductosDeAlmacenTestNuevo($idAlmacen){
        $almacen = $this->almacen->where('id',$idAlmacen)->where('deleted',false)->first();
        $productos = $almacen->productos()->where('productoxalmacen.idTipoStock',1)
            ->where('productoxalmacen.deleted',false)
            ->where('producto.deleted',false)->get();
        return $productos;
        // $productos = $this->model->where('deleted',false)->get();
        // foreach ($productos as $key => $producto) {
        //     $this->loadAlmacenesRelationship($producto);
        // }
        // return $productos;

    }

    public function listarProductosDeAlmacen($idAlmacen){
        /* Muestra los productos que se ofrecen en el almacen indicado */
        $productos = $this->model->where('deleted',false)->get();
        foreach ($productos as $key => $producto) {
            $this->loadAlmacenesRelationship($producto);
        }
        return $productos;
        // $idTipoStock = 1;
        // $producto = $this->productoRepository->obtenerPorId($idProducto);
        // $this->productoRepository->setModel($producto);
        // $lista = ProductoXAlmacen::where('idAlmacen',$idAlmacen)
        //                     ->where('idProducto',$this->model->id)
        //                     ->where('idTipoStock',$idTipoStock)
        //                     ->where('deleted',false)->get();
        // return $lista;
    }

    public function listarConStockMinimoDeAlmacen($idAlmacen){
        $productos=null;
        switch($idAlmacen){
            case 2:
                $productos =$this->model->where('habilitado', true)->where('deleted',false)->with(['almacenes' => function ($query) {
                    $query->where('almacen.deleted',false)->where('almacen.id',2)->where('productoxalmacen.deleted',false)
                    ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                    ->join('producto','producto.id', '=', 'productoxalmacen.idProducto')
                    ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                    ->whereRaw('productoxalmacen.cantidad <= producto."stockMin"');
                }])->get();
                $productos->each(function ($producto, $key) {
                    $producto->almacenes->each(function ($almacen,$key){
                        $this->loadTipoStockRelationShipFromPivotProducto_Almacen($almacen);
                    });
                });
                break;
            case 3:
                $productos =$this->model->where('deleted',false)->with(['almacenes' => function ($query) {
                    $query->where('almacen.deleted',false)->where('almacen.id',3)->where('productoxalmacen.deleted',false)
                    ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                    ->join('producto','producto.id', '=', 'productoxalmacen.idProducto')
                    ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                    ->whereRaw('productoxalmacen.cantidad <= producto."stockMin"');
                }])->get();
                $productos->each(function ($producto, $key) {
                    $producto->almacenes->each(function ($almacen,$key){
                        $this->loadTipoStockRelationShipFromPivotProducto_Almacen($almacen);
                    });
                });
                break;
            case 4:
                $productos =$this->model->where('deleted',false)->with(['almacenes' => function ($query) {
                    $query->where('almacen.deleted',false)->where('almacen.id',4)->where('productoxalmacen.deleted',false)
                    ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
                    ->join('producto','producto.id', '=', 'productoxalmacen.idProducto')
                    ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
                    ->whereRaw('productoxalmacen.cantidad <= producto."stockMin"');
                }])->get();
                $productos->each(function ($producto, $key) {
                    $producto->almacenes->each(function ($almacen,$key){
                        $this->loadTipoStockRelationShipFromPivotProducto_Almacen($almacen);
                    });
                });
                break;
        }
        
        
        return $productos;
    }

    public function loadTipoStockRelationShipFromPivotProducto_Almacen($almacen){
        $almacen->pivot->load([
            'tipoStock'=>function($query){
                $query->where('tipoStock.deleted', false)->where('deleted',false); 
            }
        ]);
    }

    public function listarConStockMinimo(){
        $productos =$this->model->where('deleted',false)->with(['almacenes' => function ($query) {
            $query->where('almacen.deleted',false)->where('productoxalmacen.deleted',false)
            ->join('tipoStock', 'tipoStock.id', '=', 'productoxalmacen.idTipoStock')
            ->join('producto','producto.id', '=', 'productoxalmacen.idProducto')
            ->where('tipoStock.key',1)->where('tipoStock.deleted',false)
            ->whereRaw('productoxalmacen.cantidad <= producto."stockMin"');
        }])->get();
        $productos->each(function ($producto, $key) {
            $producto->almacenes->each(function ($almacen,$key){
                $this->loadTipoStockRelationShipFromPivotProducto_Almacen($almacen);
            });
        });
        return $productos;
    }
    
    public function consultarStock($idProducto, $idAlmacen, $idTipoStock){
        //begin
        $productos = $this->model->where('deleted',false)->get(); //obtener lista de productos
        foreach ($productos as $key => $producto){
            $this->loadAlmacenesRelationship($producto); //obtener sus almacenes
        }

        foreach($productos as $key => $prod){
            if($prod['id'] == $idProducto){
                $almacenes = $prod['almacenes'];
                foreach($almacenes as $key => $almacen){
                    if($almacen['id'] == $idAlmacen){
                        $pivot = $almacen['pivot'];
                        if($pivot['idTipoStock'] == $idTipoStock){
                            return $pivot['cantidad'];
                        }
                    }
                }
            }
        }
    }

    public function obtenerTiendaPorId($id){
        return $this->tienda->where('id',$id)->where('deleted',false)->first();
    }

    public function actualizarDataPorTienda( $tienda,$data){
        
        $idProducto = $this->model->id;
        
        $idAlmacen = $tienda->almacen->id;
        $idTipoStock = $this->obtenerStockPrincipal()->id;
        

        
        $productoxalmacen =  ProductoXAlmacen::where('idAlmacen',$idAlmacen)
                            ->where('idProducto',$idProducto)
                            ->where('idTipoStock',$idTipoStock)
                            ->where('deleted',false)
                            ->update($data);
        
    }

    public function attachProductoXDescuento($descuento, $idProducto, $idTienda){
        
        $this->model->descuentosTc()->save($descuento , ['idTienda'=>$idTienda, 'idProducto'=> $idProducto, 'deleted'=>false] );
        $this->model->save();
    }
}