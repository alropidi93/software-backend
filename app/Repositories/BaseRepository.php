<?php 

namespace App\Repositories;

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
     */
    public function cantidadElementos()
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
    public function obtenerPorId($id)
    {
        return $this->model->findOrFail($id);
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
}