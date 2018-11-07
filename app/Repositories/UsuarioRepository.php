<?php
namespace App\Repositories;
use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\PersonaNatural;
use App\Models\Tienda;
use App\Http\Helpers\DateFormat;
	
class UsuarioRepository extends BaseRepository {
    /**
     * The PersonaNatural instance.
     *
     * @var App\Models\PersonaNatural
     */
    protected $personaNatural;
    /**
     * The PersonaNatural instance.
     *
     * @var App\Models\PersonaNatural
     */
    protected $tipoUsuario;

    protected $tienda;

  
    /**
     * Create a new UsuarioRepository instance.
     * @param  App\Models\Usuario $usuario
     * @param  App\Models\PersonaNatural $personaNatural
     * @param  App\Models\TipoUsuario $tipoUsuario
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Usuario $usuario=null, PersonaNatural $personaNatural=null, TipoUsuario $tipoUsuario=null,Tienda $tienda=null) 
    {
        $this->model = $usuario;
        $this->personaNatural = $personaNatural;
        $this->tipoUsuario = $tipoUsuario;
        $this->tienda = $tienda;
        
    }

    protected function setPersonaNaturalData($dataPersona)
    {
        $this->personaNatural['nombre'] =  $dataPersona['nombre'];
        $this->personaNatural['apellidos'] =  $dataPersona['apellidos'];
        $this->personaNatural['genero'] =  $dataPersona['genero'];
        $this->personaNatural['dni'] =  $dataPersona['dni'];
        $this->personaNatural['email'] =  $dataPersona['email'];
        $this->personaNatural['fechaNac'] =  DateFormat::spanishDateToEnglishDate($dataPersona['fechaNac']);
        $this->personaNatural['direccion'] =  $dataPersona['direccion'];
        $this->personaNatural['deleted'] =  false; //default value
        
    }

    protected function setUsuarioData($dataUsuario)
    {
        $this->model['password'] =  bcrypt($dataUsuario['password']);
       
        /*nullable fields*/
        $this->model['idTipoUsuario'] = array_key_exists('idTipoUsuario',$dataUsuario)? $dataUsuario['idTipoUsuario']:null;
        $this->model['idTienda'] = array_key_exists('idTienda',$dataUsuario)? $dataUsuario['idTienda']:null;
        /* end nullable fields*/
        $this->model['deleted'] =  false; //default value
        
    }

    protected function savePersonaNatural(){
        $this->personaNatural->save();
        
    }

    protected function attachUsuarioToPersonaNatural($personaNatural, $usuario){
        return $personaNatural->usuario()->save($usuario);
    }

   

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        
        $this->setPersonaNaturalData($dataArray); //set data only in its PersonaNatural model
        $this->savePersonaNatural(); //saving in database
        $this->setUsuarioData($dataArray);// set data only in its Usuario model
        $this->attachUsuarioToPersonaNatural($this->personaNatural,$this->model);
        $this->model->personaNatural;//loading personaNatural
       
        
    }

    public function actualiza($dataArray)
    {
        
        //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
        //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
        if (array_key_exists('fechaNac',$dataArray))
            $dataArray['fechaNac'] = DateFormat::spanishDateToEnglishDate($dataArray['fechaNac']);
        if (array_key_exists('password',$dataArray))
            $dataArray['password'] = bcrypt($dataArray['password']);
        $this->personaNatural->update($dataArray);
        $this->model->update($dataArray); //set data only in its PersonaNatural model
       
        
       
        
    }

    public function actualizaSoloUsuario($dataArray)
    {
        
        //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
        //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
        
        if (array_key_exists('password',$dataArray))
            $dataArray['password'] = bcrypt($dataArray['password']);
        
        $this->model->update($dataArray); //set data only in its PersonaNatural model
       
        
       
        
    }

    public function listarUsuariosSinTipo(){
        $lista = $this->model->whereNull('idTipoUsuario')->where('deleted',false)->get();
        foreach ($lista as $key => $usuario) {
            $usuario->personaNatural;
            $usuario->tipoUsuario;
        }
        return $lista;
    }

    public function listarUsuarios(){
        $lista = $this->model->where('deleted',false)->get();
        foreach ($lista as $key => $usuario) {
            $usuario->personaNatural;
            
        }
        return $lista;
    }

    protected function attachRol($personaNatural, $usuario){
        return $personaNatural->usuario()->save($usuario);
    }

    public function attachRolWithOwnModels(){
          
        $this->tipoUsuario->usuarios()->save($this->model);
        //$this->model->tipoUsuario()->save($this->tipoUsuario);
    }

    public function obtenerRolPorId($tipoUsuarioId){
        return $this->tipoUsuario->where('id',$tipoUsuarioId)->where('deleted',0)->first();
    }

    public function obtenerRolPorKey($key){
        return $this->tipoUsuario->where('key',$key)->where('deleted',0)->first();
    }

    public function loadTipoUsuarioRelationship($usuario=null){
        if (!$usuario){
            $this->model->load('tipoUsuario');
        }
        else{
            $usuario->load('tipoUsuario');
        }
        
    }

    public function loadTiendasCargoJefeTiendaRelationship($usuario=null){
        if (!$usuario){
            //$this->model->load('tiendasCargoJefeTienda');
            

            $this->model->load(['tiendasCargoJefeTienda' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
        else{
            //$usuario->load('tiendasCargoJefeTienda');
            $usuario->load(['tiendasCargoJefeTienda' => function ($query) {
                $query->where('deleted', false);
            }]);
        }
        
    }

    public function loadTiendaCargoJefeTiendaRelationship($usuario=null){
        if (!$usuario){
            

            $this->model->load([
                'tiendaCargoJefeTienda' => function ($query) {
                    $query->where('deleted', false);
                },
                'tiendaCargoJefeTienda.almacen' => function ($query) {
                    $query->where('almacen.deleted', false);
                },
            
            
            ]);
        }
        else{
            
            $usuario->load([
                'tiendaCargoJefeTienda' => function ($query) {
                    $query->where('deleted', false);
                },
                'tiendaCargoJefeTienda.almacen' => function ($query) {
                    $query->where('almacen.deleted', false);
                },
            ]);
        }
        
    }

    public function loadTiendasCargoTrabajadorRelationship($usuario=null){
        if (!$usuario){
            //$this->model->load('tiendasCargoJefeTienda');
            

            $this->model->load(['tiendas' => function ($query) {
                $query->where('tienda.deleted', false);
            }]);
        }
        else{
            //$usuario->load('tiendasCargoJefeTienda');
            $usuario->load(['tiendas' => function ($query) {
                $query->where('tienda.deleted', false);
            }]);
        }
        
    }

    public function loadTiendasCargoJefeAlmacenRelationship($usuario=null){
        
        if (!$usuario){
            

            $this->model->load(['tiendasCargoJefeAlmacen' => function ($query) {
                $query->where('tienda.deleted', false);
            }])->get();
        }
        else{
            
            $usuario->load(['tiendasCargoJefeAlmacen' => function ($query) {
                $query->where('tienda.deleted', false);
            }])->get();
        }
        
    }

    public function loadTiendaCargoJefeAlmacenRelationship($usuario=null){
        
        if (!$usuario){
            

            $this->model->load([
                'tiendaCargoJefeAlmacen' => function ($query) {
                    $query->where('tienda.deleted', false);
                },
                'tiendaCargoJefeAlmacen.almacen' => function ($query) {
                    $query->where('almacen.deleted', false);
                },
            ])->get();
        }
        else{
            
            $usuario->load([
                'tiendaCargoJefeAlmacen' => function ($query) {
                    $query->where('tienda.deleted', false);
                },
                'tiendaCargoJefeAlmacen.almacen' => function ($query) {
                    $query->where('almacen.deleted', false);
                },
            ])->get();
        }
        
    }

    

    public function obtenerUsuarioPorId($id)
    {
        $user = $this->model->where('idPersonaNatural',$id)->where('deleted',false)->first();
        if($user) $user->personaNatural;
        return $user;
    }

    public function obtenerUsuarioPorEmail($email)
    {
        $personaNatural = $this->personaNatural->where('email',$email)->where('deleted',false)->first();
        if($personaNatural){
            $this->setPersonaNaturalModel($personaNatural);
            $usuario = $personaNatural->usuario;
            $usuario->personaNatural;
            return $usuario;
        }
        return null;
        
    }

    public function obtenerUsuarioPorDni($dni)
    {
        $personaNatural = $this->personaNatural->where('dni',$dni)->where('deleted',false)->first();
        if($personaNatural){
            $this->setPersonaNaturalModel($personaNatural);
            $usuario = $personaNatural->usuario;
            $usuario->personaNatural;
            return $usuario;
        }
        return null;
        
    }

    protected function setPersonaNaturalModel($personaNatural){
        $this->personaNatural = $personaNatural;
    }

    public function setTipoUsuarioModel($tipoUsuario){
        $this->tipoUsuario =  $tipoUsuario;
    }

    public function getPassword(){
        return $this->model->password;
    }

    public function setModelUsuario($usuario){
        $this->model =  $usuario;
        $personaNatural =  $usuario->personaNatural;
        if($usuario->personaNatural)
            $this->personaNatural =  $personaNatural;

    }

    public function softDelete(){
        $this->personaNatural->deleted = true;
        $this->personaNatural->save();
        $this->model->deleted = true;
        $this->model->save();
       

    }

    public function listarAdmins(){
        
        $admins = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 0);
        })->get();
        foreach ($admins as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $admins;
    }

    public function listarAdminsSinTienda(){
        
        

        $admins = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 0)->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        foreach ($admins as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $admins;
    }

    public function listarCajeros(){
        
        $cajeros = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where(function($q2){
                return $q2->where('key',4)->orWhere('key',5);
            })->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($cajeros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajeros;

        
    }

    public function listarCajerosSinTienda(){
        
        $cajeros = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where(function($q2){
                return $q2->where('key',4)->orWhere('key',5);
            })->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        
        foreach ($cajeros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajeros;

        
    }

    public function listarCajerosPorTienda($idTienda){
        
        $cajeros = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where(function($q2){
                return $q2->where('key',4)->orWhere('key',5);
            })->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($cajeros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajeros;

        
    }

    public function listarCajerosVentas(){
        
        $cajerosVentas = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 4)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($cajerosVentas as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosVentas;

        
    }

    public function listarCajerosVentasSinTienda(){
        
        

        $cajerosVentas = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 4)->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        foreach ($cajerosVentas as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosVentas;
    }

    public function listarCajerosDevoluciones(){
        
        $cajerosDevoluciones = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 5)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($cajerosDevoluciones as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosDevoluciones;

        
    }

    public function listarCajerosDevolucionesSinTienda(){
        
        

        $cajerosDevoluciones = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 5)->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        foreach ($cajerosDevoluciones as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosDevoluciones;
    }

    public function listarJefesTienda(){
        
        $jefesTienda = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 1)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($jefesTienda as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $jefesTienda;
    }

    public function listarCompradores(){
        
        $compradores = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 2)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($compradores as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $compradores;
    }

    public function listarCompradoresSinTienda(){
        
        

        $compradores = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 2)->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        foreach ($compradores as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $compradores;
    }

    public function listarAlmaceneros(){
        
        $almaceneros = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 6)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($almaceneros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $almaceneros;
    }

    public function listarAlmacenerosSinTienda(){
        
        

        $almaceneros = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 6)->where('deleted',false);
        })->whereDoesntHave('tiendas', function ($query2) {
            $query2->where('tienda.deleted', false);
        })->where('usuario.deleted',false)->get();
        foreach ($almaceneros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $almaceneros;
    }

    public function listarJefesAlmacen(){
        
        $jefesAlmacen = $this->model->whereHas('tipoUsuario', function ($query) {
                $query->where('key', 3)->where('deleted',false);
        })->where('deleted',false)->get();
        foreach ($jefesAlmacen as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $jefesAlmacen;
    }

    
    public function listarJefesTiendaSinTienda(){

        
        $jefesTienda = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 1)->where('deleted',false);
        })->whereDoesntHave('tiendasCargoJefeTienda', function ($query2) {
            $query2->where('deleted', false);
        })->where('deleted',false)->get();
        foreach ($jefesTienda as $key => $usuario) {
            $usuario->personaNatural;
            
        }   
        return $jefesTienda;


      
    }

    public function listarJefesAlmacenSinTienda(){

        
        $jefesAlmacen = $this->model->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 3)->where('deleted',false);
        })->whereDoesntHave('tiendasCargoJefeAlmacen', function ($query2) {
            $query2->where('deleted', false);
        })->where('deleted',false)->get();
        foreach ($jefesAlmacen as $key => $usuario) {
            $usuario->personaNatural;
            
        }  
        return $jefesAlmacen;

      
    }

    public function listarAdminsPorTienda($idTienda){
        $admins = $this->model->whereHas('tipoUsuario', function ($query) use($idTienda){
            $query->where('key', 0)->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($admins as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $admins;
    }

    public function listarCompradoresPorTienda($idTienda){
        $compradores = $this->model->whereHas('tipoUsuario', function ($query) use($idTienda){
            $query->where('key', 2)->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($compradores as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $compradores;
    }

    public function listarCajerosVentasPorTienda($idTienda){
        $cajerosVentas = $this->model->whereHas('tipoUsuario', function ($query) use($idTienda){
            $query->where('key', 4)->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($cajerosVentas as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosVentas;
    }

    public function listarCajerosDevolucionesPorTienda($idTienda){
        $cajerosDevoluciones = $this->model->whereHas('tipoUsuario', function ($query) use($idTienda){
            $query->where('key', 5)->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($cajerosDevoluciones as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $cajerosDevoluciones;
    }

    public function listarAlmacenerosPorTienda($idTienda){
        $almaceneros = $this->model->whereHas('tipoUsuario', function ($query)use($idTienda) {
            $query->where('key', 6)->where('deleted',false);
        })->where('idTienda',$idTienda)->where('deleted',false)->get();
        foreach ($almaceneros as $key => $usuario) {
            $usuario->personaNatural;
            
        } 
        return $almaceneros;
    }

    public function obtenerJefeTiendaPorTienda($idTienda){
        
        $jefeTienda = $this->model->whereHas('tiendasCargoJefeTienda', function ($query) use($idTienda) {
            $query->where('id', $idTienda)->where('deleted',false);
        })->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 1)->where('deleted',false);
        })->where('deleted',false)->first();
        if($jefeTienda){
            $jefeTienda->personaNatural;
        }
        
            
        
        return $jefeTienda;
    }

    public function obtenerJefeAlmacenPorTienda($idTienda){
        $jefeAlmacen = $this->model->whereHas('tiendasCargoJefeAlmacen', function ($query)use($idTienda) {
            $query->where('id', $idTienda)->where('deleted',false);
        })->whereHas('tipoUsuario', function ($query) {
            $query->where('key', 3)->where('deleted',false);
        })->where('deleted',false)->first();
        if($jefeAlmacen){
            $jefeAlmacen->personaNatural;
        }
        
            
        
        return $jefeAlmacen;
    }

    public function obtenerTiendaPorId($idTienda){
        return $this->tienda->where('id',$idTienda)->where('deleted',false)->first();
    }

    public function checkIfIsPrincipalWorkerInSomeTienda(){
        if ($this->model){
            return $this->model->tiendas()->wherePivot('miembroPrincipal',true)->exists();
        }
        return false;
    }
    

    
    
  
  

  
    
}