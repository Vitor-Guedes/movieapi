<?php

namespace Modules\Importer\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Importer\Import;

class ImportEloquent extends Import
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $subject
     * 
     * @return mixed
     */
    public function register($subject): mixed
    {
        return DB::transaction(function () use ($subject) {
            $model = $subject->newInstance($subject->toArray());
            $model->save();

            $this->resolveRelations($model, $subject->getRelations());

            return $model;
        });
    }

    /**
     * @param array $relations
     * 
     * @return void
     */
    protected function resolveRelations($model, array $relations = []): void
    {
        foreach ($relations as $relation => $models) {
            foreach ($models as $relationModel) {
                $data = $relationModel->toArray();
                $condition = $this->getConditions($relation, $data);
                $model->{$relation}()->updateOrCreate($condition, $data);
            }
        }
    }

    /**
     * @param string $relation
     * @param array $data
     * 
     * @return array
     */
    protected function getConditions(string $relation, array $data): array
    {
        return Arr::mapWithKeys($data, 
            fn ($attribute, $key) => ["$relation.$key" => $attribute]
        );
    }
}