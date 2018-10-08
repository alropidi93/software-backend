<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Models\Usuario;
	
class TiendaRepository extends BaseRepository {
    /**
     * The Usuario instance.
     *
     * @var App\Models\Usuario
     */
    protected $jefeDeTienda;
    /**
     * The Usuario instance.
     *
     * @var App\Models\Usuario
     */
    protected $jefeDeAmacen;
    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Tienda $tienda, Usuario $jefeDeTienda, Usuario $jefeDeAlmacen) 
    {
        $this->model = $tienda;
        $this->jefeDeTienda = $jefeDeTienda;
        $this->jefeDeAlmacen = $jefeDeAlmacen;
        
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;

        return $this->model = $this->model->create($dataArray);
        
    }

    public function attachJefeTienda(){
           
        $this->model->jefeDeTienda()->associate($this->jefeDeTienda);
        $this->model->save();
    }

    public function attachJefeAlmacen(){
           
        $this->model->jefeDeAlmacen()->associate($this->jefeDeAlmacen);
        $this->model->save();
    }

    public function loadJefeDeTiendaRelationship($tienda=null){
        if (!$tienda){
            $this->model->load('jefeDeTienda');
        }
        else{
            $tienda->load('jefeDeTienda');
        }
        
    }

    public function loadJefeDeAlmacenRelationship($tienda=null){
        if (!$tienda){
            $this->model->load('jefeDeAlmacen');
        }
        else{
            $tienda->load('jefeDeAlmacen');
        }
    }

    public function setJefeDeTiendaModel($usuario){
        $this->jefeDeTienda = $usuario;       
    }

    public function setJefeDeAlmacenModel($usuario){
        $this->jefeDeAlmacen = $usuario;       
    }


}