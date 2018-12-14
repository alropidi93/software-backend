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
        $this->lineaDeVenta['subtotalConIgv'] = array_key_exists('subtotalConIgv',$dataLineaDeVenta)? $dataLineaDeVenta['subtotalConIgv']:0;
        $this->lineaDeVenta['precioUnitarioConIgv'] = array_key_exists('precioUnitarioConIgv',$dataLineaDeVenta)? $dataLineaDeVenta['precioUnitarioConIgv']:0;
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
        $lista = DB::select(DB::raw('select pN.id "ID Cajero",  pN.nombre || \' \' || pN.apellidos "Nombre", cP."created_at" "Fecha de emision", cP.subtotal AS "Subtotal"
        from "comprobantePago" cP, "personaNatural" pN
        where cp."idCajero" = pn."id"
        order by pN."id"'));
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
        $lista = DB::select(DB::raw('select b."idCliente" "ID Cliente", pN.nombre|| \' \' ||pN.apellidos "Nombre", cP."subtotal" "Subtotal", b."igv" "IGV" , b."igv" + cP.subtotal "Total", cP."created_at" "Fecha de Emision"
        from "boleta" b, "comprobantePago" cP, "personaNatural" pN
        where "idCliente" is not null and b."idComprobantePago" = cP."id" and 
        b."idCliente" = pN."id" and pN.id not in (select "idPersonaNatural" from usuario)
        order by b."idCliente"'));
        return $lista;
    }

    public function reporteTotalesClientesPorFacturas(){
        $lista = DB::select(DB::raw('select f."idCliente" "ID Cliente", pN.nombre|| \' \' ||pN.apellidos "Nombre", cP."subtotal" "Subtotal", f."igv" "IGV" , f."igv" + cP.subtotal "Total", cP."created_at" "Fecha de Emision"
        from "factura" f, "comprobantePago" cP, "personaNatural" pN
        where "idCliente" is not null and f."idComprobantePago" = cP."id" and 
        f."idCliente" = pN."id" and pN.id not in (select "idPersonaNatural" from usuario)
        order by f."idCliente"'));
        return $lista;
    }

    public function reporteMovimientos(){
        $lista = DB::select(DB::raw('select mts.id "Id Movimiento", p."nombre" "Producto", ts."tipo" "Tipo", mts."cantidad" "Cantidad", mts."signo" "Signo", mts."created_at" "Fecha Movimiento"
        from "movimientoTipoStock" mts, "tipoStock" ts, "producto" p
        where mts."idTipoStock" = ts."id" and mts."idProducto" = p."id"'));
        return $lista;
    }

    public function reporteCompras(){
        $lista = DB::select(DB::raw('select sc.id "N° solicitud", t."nombre", p."nombre", lsc."cantidad", prov."razonSocial", sc."created_at" "Fecha Solicitud"
        from "lineaSolicitudDeCompra" lsc, "solicitudDeCompra" sc, "producto" p, "proveedor" prov, "tienda" t
        where lsc."idSolicitudDeCompra" = sc."id" and lsc."idProducto" = p."id" and lsc."idProveedor" = prov."id" and sc."idTienda" = t."id"
        order by "N° solicitud"'));
        return $lista;
    }
}