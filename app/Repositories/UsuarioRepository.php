<?php
namespace App\Repositories;
use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\PersonaNatural;
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

  
    /**
     * Create a new UsuarioRepository instance.
     * @param  App\Models\Usuario $usuario
     * @param  App\Models\PersonaNatural $personaNatural
     * @param  App\Models\TipoUsuario $tipoUsuario
     * @return void
     */
    public function __construct(Usuario $usuario, PersonaNatural $personaNatural, TipoUsuario $tipoUsuario) 
    {
        $this->model = $usuario;
        $this->personaNatural = $personaNatural;
        $this->tipoUsuario = $tipoUsuario;
        
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
        $this->personaNatural->update($dataArray);
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
            $usuario->tipoUsuario;
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

    public function loadTipoUsuarioRelationship(){
        $this->model->tipoUsuario;
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
    
}