<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/17/2018
 * Time: 7:30 PM
 */

namespace App\Services\Repositories\Eloquent;


use App\Services\Repositories\Interfaces\RepositoryInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

class RepositoriesAbstract implements RepositoryInterface
{

    /**
     * @var Eloquent
     */
    protected $model;

    /**
     * UserRepository constructor.
     * @param Model|Eloquent $model
     * @author Toinn
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function fill(array $fields)
    {
        return $this->model->fill($fields);
    }

    /**
     * Get empty model.
     *
     * @return object
     * @author Toinn
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get table name.
     *
     * @return string
     * @author Toinn
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = [])
    {
        $query = $this->make($with);

        if (!empty($select)) {
            return $query->where($condition)->select($select)->first();
        }

        return $query->where($condition)->first();
    }

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function make(array $with = [])
    {
        return $this->model->with($with);
    }

    /**
     * Retrieve model by id regardless of status.
     *
     * @param $id
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function findById($id, array $with = [])
    {
        return $this->make($with)->where('id', $id)->firstOrFail();
    }

    /**
     * Get all models.
     *
     * @param array $with Eager load related models
     * @return mixed
     * @author Toinn
     */
    public function all(array $with = [])
    {
        $query = $this->make($with);

        return $query->get();
    }

    /**
     * @param string $column
     * @param string $key
     * @return mixed
     * @author Toinn
     */
    public function pluck($column, $key = null)
    {
        return $this->model->pluck($column, $key)->all();
    }

    /**
     * Get all models by key/value.
     *
     * @param array $condition
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function allBy(array $condition, array $with = [])
    {
        return $this->make($with)->where($condition)->get();
    }

    /**
     * Get single model by Slug.
     *
     * @param string $slug slug
     * @param array $with related tables
     * @return mixed
     * @author Toinn
     */
    public function bySlug($slug, array $with = [])
    {
        $model = $this->make($with)->where('slug', '=', $slug)->firstOrFail();

        if (!$model) {
            abort(404);
        }

        return $model;
    }

    /**
     * @param array $data
     * @return mixed
     * @author Toinn
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Create a new model.
     *
     * @param Model $model
     * @return mixed Model or false on error during save
     * @author Toinn
     */
    public function createOrUpdate(Model $model)
    {
        if ($model->save()) {
            return $model;
        }
        return false;
    }

    /**
     * @param Model $model
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * @param array $data
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function firstOrCreate(array $data, array $with = [])
    {
        return $this->model->firstOrCreate($data, $with);
    }

    /**
     * @param array $condition
     * @param array $data
     * @return mixed
     * @author Toinn
     */
    public function update(array $condition, array $data)
    {
        return $this->model->where($condition)->update($data);
    }

    /**
     * @param array $select
     * @param array $condition
     * @return mixed
     * @author Toinn
     */
    public function select(array $select = ['*'], array $condition = [])
    {
        return $this->model->where($condition)->select($select);
    }

    /**
     * @param array $condition
     * @return mixed
     * @author Toinn
     */
    public function deleteBy(array $condition = [])
    {
        return $this->model->where($condition)->delete();
    }

    /**
     * @param array $condition
     * @return mixed
     * @author Toinn
     */
    public function count(array $condition = [])
    {
        return $this->model->where($condition)->count();
    }

    /**
     * @param $column
     * @param array $value
     * @param array $args
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author Toinn
     */
    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        $data = $this->model->whereIn($column, $value);

        if (!empty(array_get($args, 'where'))) {
            $data = $data->where($args['where']);
        }

        if (!empty(array_get($args, 'paginate'))) {
            return $data->paginate($args['paginate']);
        }

        if (!empty(array_get($args, 'limit'))) {
            $data = $data->limit($args['limit']);
        }

        return $data->get();
    }

}