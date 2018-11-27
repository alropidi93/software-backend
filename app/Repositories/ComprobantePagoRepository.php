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
        $lista = DB::select(DB::raw('select pN.id AS "idCajero",  pN.nombre || \' \' || pN.apellidos AS "nombre", SUM(cP.subtotal) AS "totalVendido"
        from "comprobantePago" cP, "personaNatural" pN
        where cp."idCajero" = pn."id"
        group by pN.id, pN.nombre
        order by pN."id" DESC'));
        
        return $lista;
    }

    public function reporteVentasProductos(){
        $lista = DB::select(DB::raw('select p.id AS "Id Producto", p.nombre AS "Nombre del Producto", lv."created_at" "Fecha de Emision", lv.cantidad "Cantidad", lv.cantidad * p.precio AS "Subtotal"
        from "producto" p, "lineaDeVenta" lv
        where p."id" = lv."idProducto" and lv."idCotizacion" is null
        order by "Id Producto" DESC'));
        return $lista;
    }

    public function reporteTotalesClientesPorBoletas(){
        $lista = DB::select(DB::raw('select b."idCliente", cP."subtotal" "Subtotal", b."igv" "IGV" , b."igv" + cP.subtotal "Total"
        from "boleta" b, "comprobantePago" cP
        where "idCliente" is not null and b."idComprobantePago" = cP."id"
        order by b."idCliente"'));
        return $lista;
    }

    public function reporteTotalesClientesPorFacturas(){
        $lista = DB::select(DB::raw('select f."idCliente", cP."subtotal" "Subtotal", f."igv" "IGV" , f."igv" + cP.subtotal "Total"
        from "factura" f, "comprobantePago" cP
        where "idCliente" is not null and f."idComprobantePago" = cP."id"
        order by f."idCliente"'));
        return $lista;
    }
}