<?php
namespace App\Repositories;
use App\Models\PedidoTransferencia;
use App\Models\LineaPedidoTransferencia;
use App\Models\Transferencia;
use App\Models\Almacen;
use App\Models\Usuario;
use App\Models\TipoStock;
use App\Models\ProductoXAlmacen;
use Illuminate\Support\Facades\Log;
	
class PedidoTransferenciaRepository extends BaseRepository {
    protected $lineaPedidoTransferencia;
    protected $transferencia;
    protected $almacen;
    protected $almacenOrigen;
    protected $almacenDestino;
    protected $usuario;
    protected $tipoStock;
    protected $lineasPedidoTransferencia;
    //protected $aceptoJTO;
    //protected $aceptoJAD;
    //protected $aceptoJTD;

    /**
     * Create a new PedidoTransferencia instance.
     * @return void
     */
    public function __construct(PedidoTransferencia $pedidoTransferencia, LineaPedidoTransferencia $lineaPedidoTransferencia, Transferencia $transferencia=null,Almacen $almacen=null,Usuario $usuario=null, TipoStock $tipoStock, $aceptoJTO= false, $aceptoJAD= false, $aceptoJTD=false){
        $this->model = $pedidoTransferencia;
        $this->lineaPedidoTransferencia = $lineaPedidoTransferencia;
        $this->transferencia = $transferencia;
        $this->almacen = $almacen;
        $this->tipoStock = $tipoStock;
        $this->almacenOrigen = null;
        $this->almacenDestino = null;
        $this->usuario = $usuario;
        $this->aceptoJTO = $aceptoJTO;
        $this->aceptoJAD = $aceptoJAD;
        $this->aceptoJTD = $aceptoJTD;
    }

    public function getAlmacenCentral(){
        return $this->almacen->where('nombre','Central')->first();
    }

    

    public function loadTransferenciaRelationship($pedidoTransferencia=null){
        if (!$pedidoTransferencia){
                  

            $this->model = $this->model->load([
                'transferencia'=>function($query){
                    $query->where('transferencia.deleted', false);
                }
            ]);
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'transferencia'=>function($query){
                    $query->where('transferencia.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->transferencia && !$pedidoTransferencia){
            $this->transferencia = $this->model->transferencia;
        }
    }

    public function loadAlmacenOrigenRelationship($pedidoTransferencia=null){
        
        if (!$pedidoTransferencia){
                  
            
            $this->model = $this->model->load([
                'almacenOrigen'=>function($query){
                    $query->where('almacen.deleted', false);
                }
            ]);
                
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'almacenOrigen'=>function($query){
                    $query->where('almacen.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->almacenOrigen && !$pedidoTransferencia){
            $this->almacenOrigen = $this->model->almacenOrigen;
        }
    }

    public function loadUsuarioRelationship($pedidoTransferencia=null){
        
        if (!$pedidoTransferencia){
                  
            
            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                }
            ]);
                
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->usuario && !$pedidoTransferencia){
            $this->usuario = $this->model->usuario;
        }
    }

    
    public function loadAlmacenDestinoRelationship($pedidoTransferencia=null){
        if (!$pedidoTransferencia){
                  

            $this->model = $this->model->load([
                'almacenDestino'=>function($query){
                    $query->where('almacen.deleted', false);
                }
            ]);
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'almacenDestino'=>function($query){
                    $query->where('almacen.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->almacenDestino && !$pedidoTransferencia){
            $this->almacenDestino = $this->model->almacenDestino;
        }
    }
    public function loadAlmacenDestino2Relationship($pedidoTransferencia=null){
        if (!$pedidoTransferencia){
                  

            $this->model = $this->model->load([
                'almacenDestino2'=>function($query){
                    $query->where('almacen.deleted', false);
                }
            ]);
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'almacenDestino2'=>function($query){
                    $query->where('almacen.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->almacenDestino2 && !$pedidoTransferencia){
            $this->almacenDestino2 = $this->model->almacenDestino2;
        }
    }


    public function loadLineasPedidoTransferenciaRelationship($pedidoTransferencia=null){
        if (!$pedidoTransferencia){
                  

            $this->model = $this->model->load([
                'lineasPedidoTransferencia'=>function($query){
                    $query->where('lineaPedidoDeTransferencia.deleted', false);
                },
                'lineasPedidoTransferencia.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasPedidoTransferencia.producto.categoria'=>function($query){
                    $query->where('categoria.deleted', false);
                }
            ]);
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'lineasPedidoTransferencia'=>function($query){
                    $query->where('lineaPedidoDeTransferencia.deleted', false); 
                },
                'lineasPedidoTransferencia.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasPedidoTransferencia.producto.categoria'=>function($query){
                    $query->where('categoria.deleted', false);
                }
            ]);
            
        }
        
    }

    public function buscarPorFiltroPorTransferencia($key, $value){
        return $this->model->whereHas('transferencia',function ($q) use($key,$value){
            $q->whereRaw("lower({$key}) like ? ",'%'.$value.'%')->where('deleted',false);
        })->where('deleted',false)->get();

    }
    public function buscarPorFiltroPorTransferenciaPorAlmacen($idAlmacen,$key, $value){
        $listaEmitidos= $this->model->whereHas('transferencia',function ($q) use($key,$value){
            $q->whereRaw("lower({$key}) like ? ",'%'.$value.'%')->where('deleted',false);
        })->where('idAlmacenO',$idAlmacen)->where('deleted',false)->get();
        $listaRecibidos= $this->model->whereHas('transferencia',function ($q) use($key,$value){
            $q->whereRaw("lower({$key}) like ? ",'%'.$value.'%')->where('deleted',false);
        })->where('idAlmacenD',$idAlmacen)->where('deleted',false)->get();
        $lista = $listaRecibidos->merge($listaEmitidos);
        return $lista;
    }
    public function obtenerTodosPorAlmacen($idAlmacen){
        $listaRecibidos = $this->model->where('idAlmacenD',$idAlmacen)->where('deleted',false)->get();         
        $listaEmitidos = $this->model->where('idAlmacenO',$idAlmacen)->where('deleted',false)->get();
        $lista = $listaRecibidos->merge($listaEmitidos);
        return $lista;
    }

    public function getAlmacenById($idAlmacen){
        
        $almacenCentral = $this->getAlmacenCentral();
        if ($idAlmacen == $almacenCentral->id){
            return $almacenCentral;
        }
        else{
            
            return $this->almacen->where('id',$idAlmacen)->where('deleted',false)
                ->whereHas('tienda',function($q){
                    $q->where('tienda.deleted',false);
                })->first();

        }
      
      
    }

    

    public function getUsuarioById($idUsuario){
        
        return $this->usuario->where('idPersonaNatural',$idUsuario)->where('deleted',false)->first();
    }


    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }

    public function attachTransferenciaWithOwnModels()
    {
         
        $this->model->transferencia()->save($this->transferencia);
        
    }

    public function setLineaPedidoTransferenciaData($dataLineaPedidoTransferencia)
    {
        $this->lineaPedidoTransferencia =  new LineaPedidoTransferencia;
        $this->lineaPedidoTransferencia['idProducto'] =  $dataLineaPedidoTransferencia['idProducto'];
        $this->lineaPedidoTransferencia['cantidad'] = $dataLineaPedidoTransferencia['cantidad'];
        
        $this->lineaPedidoTransferencia['deleted'] =  false; //default value
        
    }

    public function setUsuarioModel($usuario)
    {
        $this->usuario =  $usuario;
           
    }
    public function setAlmacenModel($almacen)
    {
        $this->almacen =  $almacen;
       
    }

    public function getTiendaDeAlmacenOrigen(){
        
        return $this->model->almacenOrigen->tienda;
    }

    public function getTiendaDeAlmacenDestino(){
        //return $this->model;//->almacenDestino;
        return $this->model->almacenDestino->tienda;
    }

    public function getAlmacenDestino(){
        return $this->model->almacenDestino;
    }

    public function getTiendaDeAlmacenOwnModel(){

        return $this->almacen->tienda;
    }

    public function setTransferenciaData($dataTransferencia)
    {
        $this->transferencia =  new Transferencia($dataTransferencia);
     
        
    }
    
    public function attachLineaPedidoTransferenciaWithOwnModels(){
        
    
        $ans = $this->model->lineasPedidoTransferencia()->save($this->lineaPedidoTransferencia);
       
    }
    public function obtenerPedidosTransferenciaPorAlmacenD($idAlmacenD){
       // return $this->pedidosTransferencia->where('idAlmacenD',$idAlmacenD)->where('deleted',false);

        $lista = $this->model->where('idAlmacenD',$idAlmacenD)->where('deleted',false)->get();
        
        return $lista;
    }
    public function obtenerPedidosTransferenciaJTO($idAlmacenO){ // Jefe Tienda Origen
        $lista = $this->model->where('idAlmacenO',$idAlmacenO)->where('aceptoJTO',false)->where('aceptoJAD',false)->where('aceptoJTD',false)->where('deleted',false)->get();         
        return $lista;
     }
     public function obtenerPedidosTransferenciaJAD($idAlmacenD){ //Jefe Almacen Destino
        $lista = $this->model->where('idAlmacenD',$idAlmacenD)->where('aceptoJTO',true)->where('aceptoJAD',false)->where('aceptoJTD',false)->where('deleted',false)->get();         
        return $lista;
     }
     public function obtenerPedidosTransferenciaJTD($idAlmacenD){// Jefe Tienda Destino
        $lista = $this->model->where('idAlmacenD',$idAlmacenD)->where('aceptoJTO',true)->where('aceptoJAD',true)->where('aceptoJTD',false)->where('deleted',false)->get();         
        return $lista;
     }
     public function obtenerPedidosTransferenciaJT($idAlmacen){ //Jefe Tienda, puede ver pedidos emitidos y recibidos
        $listaRecibidos = $this->model->where('idAlmacenD',$idAlmacen)->where('aceptoJTO',true)->where('aceptoJAD',true)->where('aceptoJTD',false)->whereDoesntHave('transferencia',function($q){
            $q->where('transferencia.deleted',false);
        })->where('deleted',false)->get();         
        $listaEmitidos = $this->model->where('idAlmacenO',$idAlmacen)->where('aceptoJTO',false)->where('aceptoJAD',false)->where('aceptoJTD',false)->whereDoesntHave('transferencia',function($q){
            $q->where('transferencia.deleted',false);
        })->where('deleted',false)->get();
        $lista = $listaRecibidos->merge($listaEmitidos);
        // $lista=array();
        // foreach ($lista2 as $key => $pedidoTransferencia){
        //     if(!$pedidoTransferencia->fueEvaluado()) {
        //         array_push($lista, $pedidoTransferencia);
        //     }
        // }
        
        // foreach ($lista as $key => $pedidoTransferencia){
        //     //$pedidoTransferencia->load('transferencia');
        //     if($pedidoTransferencia->transferencia){
        //         Log::info("Esta descartando");
        //         unset($lista[$key]);
        //         Log::info(json_encode("lista"));
        //     }
        
        // }
        
        return $lista;
    }
     public function obtenerPedidosTransferenciaJefeTienda($idAlmacen){
        $listaRecibidos = $this->model->where('idAlmacenD',$idAlmacen)->where('deleted',false)->get();         
        $listaEmitidos = $this->model->where('idAlmacenO',$idAlmacen)->where('deleted',false)->get();
        $lista = $listaRecibidos->merge($listaEmitidos);
        return $lista;
     }
    public function obtenerPedidoTransferenciaConTransferenciaPorId($idPedidoTransferencia){
       return $this->model->where('id',$idPedidoTransferencia)->where('deleted',false)->first();
    }

    public function obtenerLineasPedidoTransferenciaFromOwnModel(){
        return $this->lineasPedidoTransferencia;
    }

    public function obtenerTiendaDeAlmacenOrigenFromOwnModel(){
        if ($this->model){
            return $this->model->almacenOrigen->tienda;

        }
        return null;        
    }
    public function setLineasPedidoTransferenciaByOwnModel(){
       
       $this->lineasPedidoTransferencia = $this->model->lineasPedidoTransferencia;
       unset($this->model->lineasPedidoTransferencia);
    }

    public function usuarioEsJefeDeTiendaDe($tienda){
        if(!$this->usuario   || !$tienda){
            return false;
        }
        $usuario = $this->usuario;
       
        $tiendaDeLaQueEsJefe = $usuario->tiendaCargoJefeTienda()->where('deleted',false)->first();
        if($tiendaDeLaQueEsJefe){
            return $tiendaDeLaQueEsJefe->id == $tienda->id;
        }
        else{
            return false;
        }
        
    }

    public function usuarioEsJefeDeAlmacenDe($tienda){
        if(!$this->usuario  || !$tienda){
            return false;
        }

        $usuario = $this->usuario;
   
        Log::info(json_encode($usuario));
        Log::info(json_encode($tienda));
        Log::info(json_encode($usuario->tiendasCargoJefeAlmacen));
        
        
        $tiendaDeLaQueEsJefe = $usuario->tiendaCargoJefeAlmacen()->where('deleted',false)->first();;
        Log::info(json_encode($tiendaDeLaQueEsJefe));
        if($tiendaDeLaQueEsJefe){
            return $tiendaDeLaQueEsJefe->id == $tienda->id;
        }
        else{
            return false;
        }
        return $tiendaDeLaQueEsJefe->id == $tienda->id;
    }

    public function usuarioEsJefeDeAlmacenCentralDe($almacen){
   
        if(!$this->usuario){
            return false;
        }
        $usuario = $this->usuario;
        $almacenDelQueEsJefe = $usuario->almacenCentral()->where('deleted',false)->first();;
        return $almacenDelQueEsJefe->id == $almacen->id;
    }

    public function actualizaSumaRestaStocks($almacenOrigen, $almacenDestino, $lineasPedidoTransferencia){
        $tipoStock = $this->tipoStock->where('key',1)->where('deleted',false)->first();//obtenemos el tipo principal de stock
        foreach ($lineasPedidoTransferencia as $key => $lt) {
            $cantidad =  $lt->cantidad;
            $productoxalmacenOrigen =  ProductoXAlmacen::where('idAlmacen',$almacenOrigen->id)
                            ->where('idProducto',$lt->idProducto)
                            ->where('idTipoStock',$tipoStock->id)
                            ->where('deleted',false)->first();
            Log::info(json_encode($productoxalmacenOrigen));
            Log::info($almacenDestino->id);
            Log::info("ID producto: ".$lt->idProducto);
      
            Log::info($tipoStock->id);
            $productoxalmacenDestino =  ProductoXAlmacen::where('idAlmacen',$almacenDestino->id)
                ->where('idProducto',$lt->idProducto)
                ->where('idTipoStock',$tipoStock->id)
                ->where('deleted',false)->first();
            Log::info(json_encode($productoxalmacenDestino));

            $nuevaCantidadOrigen = $productoxalmacenOrigen->cantidad + $cantidad;
            $nuevaCantidadDestino = $productoxalmacenDestino->cantidad - $cantidad;    

            Log::info("Vieja cantidad origen: ".strval($productoxalmacenOrigen->cantidad));
            Log::info("Cantidad a sumar: ". strval($cantidad));
            Log::info("Nueva cantidad origen: ". strval($nuevaCantidadOrigen));
            
            Log::info("Vieja cantidad destino: ".strval($productoxalmacenDestino->cantidad));
            Log::info("Cantidad a restar: ".strval($cantidad));
            Log::info("Nueva cantidad destino: ".strval($nuevaCantidadDestino));
            
                            
            $this->actualizarStock($lt->idProducto,$almacenOrigen->id,$tipoStock->id,$nuevaCantidadOrigen);
            $this->actualizarStock($lt->idProducto,$almacenDestino->id,$tipoStock->id,$nuevaCantidadDestino);
        }
    }

    public function actualizarStock($idProducto, $idAlmacen,$idTipoStock, $cantidad){
        Log::info("ID producto: ".$idProducto);
        Log::info($idAlmacen);
        Log::info($idTipoStock);
        Log::info($cantidad);
        $productoxalmacen =  ProductoXAlmacen::where('idAlmacen',$idAlmacen)
                            ->where('idProducto',$idProducto)
                            ->where('idTipoStock',$idTipoStock)
                            ->where('deleted',false)
                            ->update(['cantidad'=>$cantidad]);
        Log::info(json_encode($productoxalmacen));
        Log::info("actualizado");
    }
}