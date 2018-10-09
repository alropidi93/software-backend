<?php
namespace App\Repositories;
use App\Models\Movimiento;
use App\Models\Usuario;
	
class MovimientoRepository extends BaseRepository{
    

    protected $usuario;
    /**
     * Create a new MovimientoRepository instance.
     * @param  App\Models\Movimiento $movimiento
     * @param  App\Models\Usuario $usuario
     * @return void
     */
    public function __construct(Movimiento $movimiento, Usuario $usuario) 
    {
        $this->model = $movimiento;
        $this->usuario = $usuario;
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Movimiento
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;
        return $this->model = $this->model->create($dataArray);
        
    }
    /*PARTE DE TUTORIAL PARA RELATIONSHIPS */
    public function loadUsuarioRelationship($movimiento=null){
        
        if (!$movimiento){
        
            

            $this->model = $this->model->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); // hacemos referencia al metodo usuario() en el modelo Movimiento y solo cargamos los que tengan el campo 'deleted' igual a false
                },
                'usuario.personaNatural' => function ($query) {
                    $query->where('personaNatural.deleted', false); // hacemos referencia al metodo personaNatural() en el modelo Usuario y solo cargamos los que tengan el campo 'deleted' igual a false
                }
            ]);
        }
        else{
            
            $this->model =$movimiento->load([
                'usuario'=>function($query){
                    $query->where('usuario.deleted', false); // hacemos referencia al metodo usuario() en el modelo Movimiento y solo cargamos los que tengan el campo 'deleted' igual a false
                },
                'usuario.personaNatural' => function ($query) {
                    $query->where('personaNatural.deleted', false); // hacemos referencia al metodo personaNatural() en el modelo Usuario y solo cargamos los que tengan el campo 'deleted' igual a false
                }
            ]);
        }
        if ($this->model->usuario){
            $this->usuario = $this->model->usuario;
        }
        
    }

    public function obtenerUsuarioModel(){
        return $this->usuario;
    }

    /*FIN DE PARTE DE TUTORIAL PARA RELATIONSHIPS */
}