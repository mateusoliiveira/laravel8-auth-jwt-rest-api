<?php


namespace App\Repositories\Eloquent;

abstract class AbstractRepository 
{

    protected $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    public function all() 
    {
        return $this->model->all();
    }

    public function find($id) 
    {
        return $this->model->find($id);
    }

    public function with($table) 
    {
        return $this->model->with($table);
    }

    public function create($data) 
    {
        return $this->model->create($data);
    }

    public function update($data) 
    {
        return $this->model->update($data);
    }

    public function delete() 
    {
        return $this->model->delete();
    }

    protected function resolveModel()
    {
        return app($this->model);
    }

}