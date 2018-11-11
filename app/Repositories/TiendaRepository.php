<?php
namespace App\Repositories;
use App\Models\Tienda;
use App\Models\Almacen;
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
  
    protected $trabajadores;
    protected $almacen;

    /**
     * Create a new TiendaRepository instance.
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Tienda $tienda, Usuario $jefeDeTienda, Usuario $jefeDeAlmacen, Almacen $almacen=null)  
    {
        $this->model = $tienda;
        $this->almacen = $almacen;
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
        $this->model = $this->model->create($dataArray);
        $this->almacen->nombre =  $dataArray['nombre'];
        $this->almacen->deleted =  false;
        $this->model->almacen()->save($this->almacen);
        return $this->model;
    }

    public function attachJefeTienda(){
        $this->model->jefeDeTienda()->associate($this->jefeDeTienda);
        $this->model->save();
    }

    public function attachJefeAlmacen(){
        $this->model->jefeDeAlmacen()->associate($this->jefeDeAlmacen);
        $this->model->save();
    }

    public function attachTrabajador($trabajador, $data=null){
        if (!$data)
            $this->model->trabajadores()->save($trabajador,['deleted' => false, 'miembroPrincipal' => true]);
        else
            $this->model->trabajadores()->save($trabajador,['deleted' => false, 'miembroPrincipal' => $data['miembroPrincipal'] ]);
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

    public function loadAlmacenRelationship($tienda=null){
        if (!$tienda){
            $this->model->load('almacen');
        }
        else{
            $tienda->load('almacen');
        }   
    }

    public function setJefeDeTiendaModel($usuario){
        $this->jefeDeTienda = $usuario;       
    }

    public function setJefeDeAlmacenModel($usuario){
        $this->jefeDeAlmacen = $usuario;       
    }

    public function loadTrabajadoresRelationship($tienda=null){
      


        if (!$tienda){
                  

            $this->model = $this->model->load([
                'trabajadores'=>function($query){
                    $query->where('usuario.deleted', false); 
                },
                'trabajadores.personaNatural'=>function($query){
                    $query->where('personaNatural.deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$tienda->load([
                'trabajadores'=>function($query){
                    $query->where('usuario.deleted', false); 
                },
                'trabajadores.personaNatural'=>function($query){
                    $query->where('personaNatural.deleted', false); 
                }
            ]);
        }
        if ($this->model->trabajadores){
            $this->trabajadores = $this->model->trabajadores;
        }
    }

    public function checkIfOwnModelTiendaHasJefeTienda(){
        return strval($this->model->jefeDeTienda()->where('usuario.deleted',false)
                ->whereHas('personaNatural', function ($query){
                    $query->where('personaNatural.deleted',false);
                })->exists());
    
    }

    public function checkIfOwnModelTiendaHasJefeAlmacen(){
        return strval($this->model->jefeDeAlmacen()->where('usuario.deleted',false)
                ->whereHas('personaNatural', function ($query){
                    $query->where('personaNatural.deleted',false);
                })->exists());
    
    }

    // public function checkIfOwnModelTiendaHasTrabajadoPorId($id){
    //     return $this->model->trabajadores()->where('usuario.idPersonaNatural',$id)->where('usuario.deleted',false)
    //             ->whereHas('personaNatural', function ($query){
    //                 $query->where('personaNatural.deleted',false);
    //             })->exists();
    
    // }

    public function checkIfUsuarioAttachedBefore ($usuario){
        return $this->model->trabajadores->contains($usuario);
    }

    public function deleteUsuarioRelationship($usuario){
        return $this->model->trabajadores()->detach($usuario->idPersonaNatural);
    }

    public function obtenerTiendasFuncionales()
    {
        //lista las tiendas que tengan jefes asignados
        $list = $this->model->whereNotNull('idJefeTienda')->whereNotNull('idJefeAlmacen')->where('deleted',false)->get();
        return $list;
    }
}