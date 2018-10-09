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
Route::resource('usuarios', 'UsuarioController');
Route::resource('movimientos', 'MovimientoController');
Route::resource('proveedores', 'ProveedorController');

Route::get('busqueda/proveedores','ProveedorController@busquedaPorFiltro');
Route::get('usuariosSinTipo', 'UsuarioController@listarUsuariosSinTipo');
Route::get('busqueda/productos', 'ProductoController@busquedaPorFiltro');
Route::get('busqueda/tiendas', 'TiendaController@busquedaPorFiltro');
Route::post('asignarTipoUsuario/{idUsuario}', 'UsuarioController@asignarRol');

Route::post('tienda/asignarJefeAlmacen/{idTienda}', 'TiendaController@asignarJefeDeAlmacen');
Route::post('tienda/asignarJefeTienda/{idTienda}' , 'TiendaController@asignarJefeDeTienda');
Route::post('tienda/asignarTrabajador/{idTienda}' , 'TiendaController@asignarTrabajador');

Route::post('login', 'UsuarioController@login');

Route::resource('tipoUsuarios', 'TipoUsuarioController');
Route::resource('tipoProductos', 'TipoProductoController');



Route::get('usuario/jefesTiendaNoAsignados', 'UsuarioController@listarJefesDeTiendaSinTienda');
Route::get('usuario/jefesAlmacenNoAsignados', 'UsuarioController@listarJefesDeAlmacenSinTienda');
Route::get('usuario/cajeros', 'UsuarioController@listarCajeros');
Route::get('usuario/listarPorRol', 'UsuarioController@listarPorRol');
Route::get('usuario/listarPorRolSinTienda', 'UsuarioController@listarPorRolSinTiendaAsignada');

