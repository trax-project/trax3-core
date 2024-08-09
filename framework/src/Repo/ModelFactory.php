<?php

namespace Trax\Framework\Repo;

use Illuminate\Database\Eloquent\Model;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\StdClass;

abstract class ModelFactory
{
    /**
     * @var bool
     */
    protected $jsonEncode;

    /**
     * @param  bool  $jsonEncode
     * @return void
     */
    public function __construct(bool $jsonEncode = true)
    {
        $this->jsonEncode = $jsonEncode;
    }

    /**
     * Return the model class.
     *
     * @return string
     */
    abstract public function modelClass(): string;

    /**
     * Return the unique columns used to upsert data.
     *
     * @return array
     */
    abstract public function uniqueColumns(): array;

    /**
     * Given a record, return a string made with the unique field values.
     *
     * @param  array  $record
     * @return string
     */
    public function uniqueColumnsValue(array $record): string
    {
        return collect($this->uniqueColumns())->map(function ($column) use ($record) {
            // The 'id' column may be missing and replaced by '_id' with some drivers, including MongoDB.
            if ($column == 'id' && isset($record['_id'])) {
                return $record['_id'];
            }
            return $record[$column];
        })->join('|');
    }

    /**
     * Return the model name.
     *
     * @return string
     */
    public function modelName(): string
    {
        $segments = explode('\\', $this->modelClass());
        return end($segments);
    }

    /**
     * Return an Eloquent model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(): Model
    {
        $modelClass = $this->modelClass();
        return new $modelClass;
    }

    /**
     * Return the DB connection.
     *
     * @return string
     */
    public function connection(): string
    {
        return $this->model()->getConnectionName();
    }

    /**
     * Return the DB table.
     *
     * @return string
     */
    public function table(): string
    {
        return $this->model()->getTable();
    }

    /**
     * Create a new model instance given some data.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function make(array $data)
    {
        return $this->model()->fill(
            $this->prepare($data)
        );
    }

    /**
     * Prepare data before recording (used for bulk insert).
     *
     * @param array  $data
     * @return array
     */
    abstract public function prepare(array $data);

    /**
     * Prepare data before recording (used for bulk insert).
     *
     * @param array  $data
     * @return array
     */
    public function prepareWithoutChange(array $data)
    {
        return $data;
    }
    
    /**
     * Update an existing model in the database, given some data.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($model, array $data)
    {
        $model->fill($data);
        return $model;
    }

    /**
     * Duplicate an existing model in the database, given some data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicate($model, array $data = [])
    {
        return $model->replicate()->fill($data);
    }

    /**
     * Prepare data for export.
     *
     * @param  \Illuminate\Database\Eloquent\Model|object  $model
     * @param  bool  $rawFormat
     * @return array
     */
    public function export($model, bool $rawFormat = false): array
    {
        return StdClass::only($model);
    }

    /**
     * Prepare data for import (upsert).
     *
     * @param  object  $item
     * @return array
     */
    public function import(object $item): array
    {
        return Json::array($item);
    }
}
