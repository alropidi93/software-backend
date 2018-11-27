<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Models\SolicitudCompra;
use App\Models\Proveedor;
use App\Models\TipoStock;
use App\Models\LineaSolicitudCompra; ///no poner esta linea de mierda me hizo perder 2 horas :')
	
class SolicitudCompraRepository extends BaseRepository {
    protected $lineaSolicitudCompra;
    protected $lineasSolicitudCompra;
    protected $tienda;
    protected $proveedor;
    protected $tipoStock;
    /**
     * Create a new ProductoRepository instance.
     * @return void
     */
    public function __construct(SolicitudCompra $solicitudCompra, LineaSolicitudCompra $lineaSolicitudCompra, Tienda $tienda=null, Proveedor $proveedor,TipoStock $tipoStock){
        $this->model = $solicitudCompra;
        $this->tienda = $tienda;
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;
        $this->proveedor = $proveedor;
        $this->tipoStock = $tipoStock;
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function obtenerTiendaPorId($idTienda){
        return $this->tienda->where('id',$idTienda)->where('deleted',false)->first();
    }

    public function loadLineasSolicitudCompraRelationship($solicitudCompra=null){
        if (!$solicitudCompra){
            $this->model = $this->model->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                },
                'lineasSolicitudCompra.producto'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                    $query->where('producto.deleted', false); 
                }
            ]);
        }
        else{
            $this->model =$solicitudCompra->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false); 
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                },
                'lineasSolicitudCompra.producto'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                    $query->where('producto.deleted', false); 
                }
            ]);
        }
        if ($this->model->lineasSolicitudCompra){
            $this->lineasSolicitudCompra = $this->model->lineasSolicitudCompra;
        }
    }

    public function loadLineasSolicitudCompraRelationshipWithExtraRelationships($solicitudCompra=null){
        if (!$solicitudCompra){
            $this->model = $this->model->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                },
                'lineasSolicitudCompra.producto'=>function($query){
                    
                    $query->where('producto.deleted', false); 
                },
                'lineasSolicitudCompra.proveedor'=>function($query){
                    
                    $query->where('proveedor.deleted', false); 
                }
            ]);
        }
        else{
            $this->model =$solicitudCompra->load([
                'lineasSolicitudCompra'=>function($query){
                    // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false); 
                    $query->where('lineaSolicitudDeCompra.deleted', false); 
                },
                'lineasSolicitudCompra.producto'=>function($query){
                    
                    $query->where('producto.deleted', false); 
                },
                'lineasSolicitudCompra.proveedor'=>function($query){
                    
                    $query->where('proveedor.deleted', false); 
                }
            ]);
        }
        if ($this->model->lineasSolicitudCompra){
            $this->lineasSolicitudCompra = $this->model->lineasSolicitudCompra;
        }
    }

    public function setLineaSolicitudCompraModel($lineaSolicitudCompra){
        $this->lineaSolicitudCompra = $lineaSolicitudCompra;
    }

    

    public function setTiendaModel($tienda){
        $this->tienda = $tienda;
    }

    public function attachLineaSolicitudCompra($lineaSolicitudCompra){
        $this->model->lineasSolicitudCompra()->save($lineaSolicitudCompra , ['deleted'=>false] );
        $this->model->save();
    }

    protected function obtenerSolictudesQueContieneProducto($producto){
        
        
        return $this->model->where('enviado',false)->where('idTienda',$this->tienda->id)->where('deleted',false)->whereHas('lineasSolicitudCompra',function($q) use ($producto){
            $q->whereHas('producto',function ($q2)use ($producto){
                $q->where('producto.id',$producto->id)->where('producto.deleted',false);
            });
        });
    }
    public function obtenerLineaActual($producto){
        return $this->lineaSolicitudCompra->where('idProducto',$producto->id)->where('deleted',false)
        ->whereHas('solicitudCompra',function ($q) {
            $q->where('solicitudCompra.idTienda',$this->tienda->id)->where('solicitudCompra.enviado',false)->where('solicitudCompra.deleted',false);
        })->first();
    }

    public function acumulaPeoductoOrCreaSolicitud($producto,$cantidad){
        $n_elements = $this->obtenerSolicitudQueContienenProducto($producto);
        if ($n_elements==0){
            //creo
            $this->guarda(['idTienda'=>$this->tienda->id,'enviado'=>false]);
            
        }
        else{
            //actualizo
            $lineaActual = $this->obtenerLineaActual($producto);
            $nuevaCantidad = $cantidad +$lineaActual['cantidad'];
            $lineaActual->update(['cantidad'=>$nuevaCantidad]);
        }
    }

    public function obtenerSolicitudDisponible(){
        return $this->model->where('deleted',false)->where('enviado',false)->first();
    }

    public function crearNueva(){
        return $this->model->create(['deleted'=>false,'enviado'=>false]);
    }

    public function obtenerProveedorPorId($idProveedor){
        return $this->proveedor->where('id', $idProveedor)->first();
    }

    public function obtenerStockPrincipal(){
        return $this->tipoStock->where('key',1)->where('deleted',false)->first();
    }

    public function obtenerLineaPorProductoIdDisponible($idProducto){
        return $this->model->lineasSolicitudCompra()->where('idProducto',$idProducto)->whereNull('idProveedor')->where('deleted',false)->first();
    }

 

    public function loadSpecifLineasRelationship($id_array){
        $this->model = $this->model->load([
            'lineasSolicitudCompra'=>function($query) use ($id_array){
                // $query->where('lineaSolicitudDeCompra.deleted', false)->wherePivot('deleted',false);
                $query->where('lineaSolicitudDeCompra.deleted', false)->whereIn('idProducto',$id_array); 
            },
            'lineasSolicitudCompra.producto'=>function($query){
                
                $query->where('producto.deleted', false); 
            },
            'lineasSolicitudCompra.proveedor'=>function($query){
                
                $query->where('proveedor.deleted', false); 
            }
        ]);
    }

    protected function obtenerLineasConProveedorQuery(){
        $solicitudCompraDisponible = $this->obtenerSolicitudDisponible();
        return $solicitudCompraDisponible->lineasSolicitudCompra()->where('deleted',false)->whereNotNull('idProveedor');
    }
    public function obtenerLineasConProveedor(){
        return $this->obtenerLineasConProveedorQuery()->get();
    }


    public function obtenerLineasConProveedorConFiltro($filter, $value){
        if($filter=='idProducto'){
            return $this->obtenerLineasConProveedorQuery()->where($filter,$value)->get();
        }
        else if ($filter=='nombreProducto'){
            
            return $this->obtenerLineasConProveedorQuery()->whereHas('producto',function($query) use ($value){
                $query->whereRaw("lower(nombre) like ? ",'%'.$value.'%')->where('deleted',false);
            })->get();

        }
            
        else if ($filter=='nombreProveedor'){
            return $this->obtenerLineasConProveedorQuery()->whereHas('proveedor',function($query) use ($value){
                $query->whereRaw("lower(contacto) like ? ",'%'.$value.'%')->where('deleted',false);
            })->get();
        }
     
        return null;

    }

    
}