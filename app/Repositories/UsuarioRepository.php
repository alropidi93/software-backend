<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Http\Helpers\DateFormat;
	
class TiendaRepository extends BaseRepository {
    /**
     * The PersonaNatural instance.
     *
     * @var App\Models\PersonaNatural
     */
    protected $personaNatural;

  
    /**
     * Create a new UsuarioRepository instance.
     * @param  App\Models\Usuario $usuario
     * @param  App\Models\PersonaNatural $personaNatural
     * @return void
     */
    public function __construct(Usuario $usuario, PersonaNatural $personaNatural) 
    {
        $this->model = $usuario;
        $this->personaNatural = $personaNatural;
        
    }

    protected function setPersonaNatural($dataArray)
    {
        $this->personaNatural['nombre'] =  $dataArray['nombre'];
        $this->personaNatural['apellidos'] =  $dataArray['apellidos'];
        $this->personaNatural['genero'] =  $dataArray['genero'];
        $this->personaNatural['dni'] =  $dataArray['dni'];
        $this->personaNatural['email'] =  $dataArray['email'];
        $this->personaNatural['fechaNac'] =  DateFormat::spanishDateToEnglishDate($dataArray['fechaNac']);
        $this->personaNatural['direccion'] =  $dataArray['direccion'];
        $this->personaNatural['deleted'] =  false; //default value
        
    }

    protected function savePersonaNatural(){
        $this->personaNatural->save();
    }

   

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        $this->setPersonaNatural($dataArray);
        $this->savePersonaNaural();

        return $this->model = $this->model->create($dataArray);
        
    }
    
}