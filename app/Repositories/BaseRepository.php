<?php 

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
abstract class BaseRepository {
    /**
     * The Model instance.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Get number of records.
     *
     * @return int
    **/
    public function cantidadElementos()
    {
        return count($this->obtenerTodos());
        
    }
    
    public function cantidadElementosAnyway()
    {
        $total = $this->model->count();
        return $total;
    }
    /**
     * Destroy a model.
     *
     * @param  int $id
     * @return void
     */
    public function eliminarPorId($id)
    {
        $model = $this->getById($id);
        $model->deleted = true;
        $model->save();
    }
    /**
     * Get Model by id.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function obtenerPorIdHard($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Model by id.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function obtenerPorId($id)
    {
        return $this->model->where('id',$id)->where('deleted',false)->first();
    }

    /**
     * Set a value to the id attribute.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function setId($id)
    {
        return $this->model->id = $id;
    }

    /**
     * Set a certain model to the model attribute.
     *
     * @param  App\Models\Model
     * @return void
     */
    public function setModel($model)
    {
        return $this->model =  $model;
    }
  
    /**
     * Get the whole model.
     *
     * 
     * @return App\Models\Model
     */
    public function obtenerModelo(){
        return $this->model;
    }

    /**
     * Get all of records.
     *
     * @return Model array
     */
    public function obtenerTodos()
    {
        $list = $this->model->where('deleted',false)->get();
        return $list;
    }

    /**
     * Update a model by its id.
     * 
     * @param array $data 
     * @return App\Models\Model
     */
    public function actualiza($data)
    {
        
        Log::info("step 0");
        
        $this->model->update($data);
        Log::info("step 1");
        $newModel = $this->obtenerPorId($this->model->id);
        Log::info("step 2");
        $this->setModel($newModel);
        Log::info("step 3");
        
    }

    public function softDelete()
    {
        $data= ['deleted'=>true];
        $this->model->update($data);
        $this->setModel(null);
        
        
    }

    public function buscarPorFiltro($key, $value){
        
        return $this->model->whereRaw("lower({$key}) like ? ",'%'.$value.'%')->where('deleted',false)->get();
    }

    public function buscarPorFiltroNum($key, $value){
        
        return $this->model->where($key,$value)->where('deleted',false)->get();
    }

    public function crear($data){
        return $this->model->create($data);
    }

    

    

    
}