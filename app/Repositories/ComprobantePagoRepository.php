<?php
namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Models\LineaDeVenta;
	
class ComprobantePagoRepository extends BaseRepository {
    protected $cajero;
    protected $lineaDeVenta;
    protected $lineasDeVenta;
    protected $usuarioRepository;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(ComprobantePago $comprobantePago, Usuario $cajero=null, LineaDeVenta $lineaDeVenta, UsuarioRepository $usuarioRepository)  
    {
        $this->model = $comprobantePago;
        $this->lineaDeVenta = $lineaDeVenta;
        $this->cajero = $cajero;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function guarda($dataArray){
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
    }

    public function attachCajero(){
        $this->model->usuario()->associate($this->cajero);
        $this->model->save();
    }
       
    public function loadCajeroRelationship($comprobantePago=null){
        if (!$comprobantePago){
            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false);
                }
            ]); 
        }else{   
            $this->model =$comprobantePago->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);   
        }
        if ($this->model->cajero && !$comprobantePago){
            $this->cajero = $this->model->cajero;
        }
    }

    public function loadLineasDeVentaRelationship($comprobantePago=null){
        if (!$comprobantePago){
            $this->model = $this->model->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false);
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){ //esta parte no es tan necesaria para este request
                    $query->where('categoria.deleted', false);
                }
            ]);
        }else{
            $this->model =$comprobantePago->load([
                'lineasDeVenta'=>function($query){
                    $query->where('lineaDeVenta.deleted', false); 
                },
                'lineasDeVenta.producto'=>function($query){
                    $query->where('producto.deleted', false);
                },
                'lineasDeVenta.producto.categoria'=>function($query){//esta parte no es tan necesaria para este request
                    $query->where('categoria.deleted', false);
                }
            ]);   
        }
    }

    public function setLineaDeVentaData($dataLineaDeVenta){
        $this->lineaDeVenta =  new LineaDeVenta;
        $this->lineaDeVenta['idProducto'] =  $dataLineaDeVenta['idProducto'];
        $this->lineaDeVenta['cantidad'] = $dataLineaDeVenta['cantidad'];
        $this->lineaDeVenta['deleted'] =  false; //default value
    }

    public function attachLineaDeVentaWithOwnModels(){
        
        $ans = $this->model->lineasDeVenta()->save($this->lineaDeVenta);
    }

    public function obtenerLineasDeVentaFromOwnModel(){
        return $this->lineasDeVenta;
    }

    public function setLineasDeVentaByOwnModel(){
        $this->lineasDeVenta = $this->model->lineasDeVenta;
        unset($this->model->lineasDeVenta);
     }

    public function setUsuarioModel($usuario){
        $this->cajero = $usuario;       
    }

    public function setComprobantePagoModel($comprobantePago){
        //no tiene similar en pedido transferencia repo
        $this->model = $comprobantePago;       
    }

    public function getUsuarioById($idUsuario){
        return $this->cajero->where('idPersonaNatural',$idUsuario)->where('deleted',false)->first();
    }

    public function reporteVentasCajeros(){
        $lista = DB::table('comprobantePago')
        ->select('idCajero', DB::raw('sum(subtotal) as subtotal'))
        // ->join('personaNatural', 'personaNatural.id', '=', 'comprobantePago.idCajero')
        ->groupBy('idCajero')
        ->get();

        // foreach($lista as $key => $cajero){
        //     $this->usuarioRepository->loadPersonaNaturalRelationship($cajero);
        // }
        return $lista;
    }

    public function reporteVentasProductos(){
        $lista = DB::select(DB::raw('select p.id AS "idProducto", p.nombre AS "producto", SUM(lv.cantidad * p.precio) AS "totalVendido"
        from "producto" p, "lineaDeVenta" lv
        where p."id" = lv."idProducto" and lv."idCotizacion" is null
        group by p.id, p.nombre
        order by "totalVendido" DESC'));
        return $lista;
    }
}