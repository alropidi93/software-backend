<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::resource('tiendas', 'TiendaController');
Route::resource('productos', 'ProductoController');
Route::resource('unidades', 'UnidadMedidaController');
Route::resource('clientesNaturales', 'PersonaNaturalController');
Route::resource('usuarios', 'UsuarioController');
Route::resource('movimientos', 'MovimientoController');
Route::resource('proveedores', 'ProveedorController');
Route::resource('categorias', 'CategoriaController');
Route::resource('solicitudesCompra', 'SolicitudCompraController');
Route::resource('lineasSolicitudCompra', 'LineaSolicitudCompraController');
Route::resource('pedidosTransferencia', 'PedidoTransferenciaController');
Route::resource('almacen', 'AlmacenController');
Route::resource('clientesJuridicos', 'PersonaJuridicaController');
Route::resource('comprobanteDePago', 'ComprobantePagoController');
Route::resource('factura', 'FacturaController');
Route::resource('boleta', 'BoletaController');
Route::resource('cotizacion', 'CotizacionController');

Route::get('busqueda/proveedores','ProveedorController@busquedaPorFiltro');
Route::get('usuariosSinTipo', 'UsuarioController@listarUsuariosSinTipo');
Route::get('busqueda/productos', 'ProductoController@busquedaPorFiltro');
Route::get('busqueda/tiendas', 'TiendaController@busquedaPorFiltro');
Route::post('asignarTipoUsuario/{idUsuario}', 'UsuarioController@asignarRol');


Route::post('tienda/asignarJefeAlmacen/{idTienda}', 'TiendaController@asignarJefeDeAlmacen');
Route::post('tienda/desasignarJefeAlmacen/{idTienda}' , 'TiendaController@desasignarJefeDeAlmacen');
Route::post('tienda/asignarJefeTienda/{idTienda}' , 'TiendaController@asignarJefeDeTienda');
Route::post('tienda/desasignarJefeTienda/{idTienda}' , 'TiendaController@desasignarJefeDeTienda');
Route::post('tienda/asignarTrabajador/{idTienda}' , 'TiendaController@asignarTrabajador');
Route::post('tienda/desasignarTrabajador/{idTienda}' , 'TiendaController@desasignarTrabajador');
Route::get('tienda/listarTiendasFuncionales', 'TiendaController@obtenerTiendasFuncionales');

Route::post('producto/asignarProveedor/{idProducto}' , 'ProductoController@asignarProveedor');

Route::post('login', 'UsuarioController@login');

Route::resource('tipoUsuarios', 'TipoUsuarioController');
Route::resource('tipoProductos', 'TipoProductoController');



Route::get('usuario/jefesTiendaNoAsignados', 'UsuarioController@listarJefesDeTiendaSinTienda');
Route::get('usuario/jefesAlmacenNoAsignados', 'UsuarioController@listarJefesDeAlmacenSinTienda');
Route::get('usuario/cajeros', 'UsuarioController@listarCajeros');
Route::get('usuario/cajerosSinTiendaAsignada', 'UsuarioController@listarCajerosSinTiendaAsignada');
Route::get('usuario/listarPorRol', 'UsuarioController@listarPorRol');
Route::get('usuario/listarPorRolSinTienda', 'UsuarioController@listarPorRolSinTiendaAsignada');
Route::get('usuario/obtenerPorRolPorTienda/{idTienda}','UsuarioController@obtenerPorRolPorTienda');
Route::get('usuario/cajerosPorTienda/{idTienda}','UsuarioController@obtenerCajerosPorTienda');

Route::post('almacen/cargarProductosStock','AlmacenController@cargarProductosStock');
Route::post('almacen/asignarJefeDeAlmacenCentral','AlmacenController@asignarJefeAlmacenCentral');

Route::get('producto/listarConStock','ProductoController@listarConStock');
Route::get('producto/listarStockMin','ProductoController@listarConStockMinimo');
Route::get('producto/listarProductosDeAlmacen/{idAlmacen}','ProductoController@listarProductosDeAlmacen');


Route::get('transferencia/listarEstados','TransferenciaController@listarEstados');

Route::get('pedidoTransferencia/verPedidosTransferenciaRecibidos/{idAlmacenD}','PedidoTransferenciaController@verPedidosTransferenciaRecibidos');
Route::get('pedidoTransferencia/obtenerPedidoTransferenciaPorId/{idPedidoTransferencia}','PedidoTransferenciaController@obtenerPedidoTransferenciaPorId');
Route::get('pedidoTransferencia/verPedidosTransferenciaJTO/{idAlmacenO}','PedidoTransferenciaController@verPedidosTransferenciaJTO');// no esta en uso
Route::get('pedidoTransferencia/verPedidosTransferenciaJAD/{idAlmacenD}','PedidoTransferenciaController@verPedidosTransferenciaJAD');// el servicio se llama JA en Postman
Route::get('pedidoTransferencia/verPedidosTransferenciaJTD/{idAlmacenD}','PedidoTransferenciaController@verPedidosTransferenciaJTD');// no esta en uso
Route::get('pedidoTransferencia/verPedidosTransferenciaJT/{idAlmacen}','PedidoTransferenciaController@verPedidosTransferenciaJT');
Route::get('pedidoTransferencia/obtenerPedidosTransferenciaJefeTienda/{idAlmacen}','PedidoTransferenciaController@obtenerPedidosTransferenciaJefeTienda');
Route::post('pedidoTransferencia/aceptaPedidoJTO/{idPedidoTransferencia}','PedidoTransferenciaController@aceptaPedidoJTO');
Route::post('pedidoTransferencia/aceptaPedidoJAD/{idPedidoTransferencia}','PedidoTransferenciaController@aceptaPedidoJAD');// se usa como Aceptar TEST en Postman
Route::post('pedidoTransferencia/evaluar/{idPedidoTransferencia}','PedidoTransferenciaController@evaluarPedidoTransferencia');
Route::get('pedidoTransferencia/obtenerHistorialPedidosTransferencia/{idPedidoTransferencia}','PedidoTransferenciaController@obtenerHistorialPedidosTransferencia');


Route::put('producto/actualizarStock/{idProducto}' , 'ProductoController@modificarStock');


