<?php

namespace Modules\Movie\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\DataTransferObject\DataTransferObject;

class Parser
{
    protected $relashions = [];

    protected $main;

    public function __construct(
        protected DataTransferObject $dto,
        protected array $rules
    ) {  }

    public function parse()
    {
        if (isset($this->rules['relashions'])) {
            $this->resolveRelashions();
        }

        if (isset($this->rules['map'])) {
            $this->resolveMap();
        }

        return $this->main;
    }

    protected function resolveRelashions()
    {
        Arr::map($this->rules['relashions'], function ($relashion) {
            [$model, $with] = explode(':', $relashion);
            $this->relashions[$model][] = $with;
        });
    }

    protected function resolveMap()
    {
        $mapper = $this->rules['map'];

        $main = null;
        foreach ($mapper as $dtoClass => $model) {

            if ($dtoClass == get_class($this->dto)) {
                $mainAlias = key($model);
                $main = $this->createModel($model, $this->dto);
                $main->incrementing = false;
                $main->save();
                continue ;
            } 
            
            $alias = key($model);
            if (isset($this->dto->{$alias}) && is_array($this->dto->{$alias})) {
                foreach ($this->dto->{$alias} as $dto) {
                    if (in_array($alias, $this->relashions[$mainAlias])) {
                        $dtoArray = $dto->toArrayWithPrefix($alias);
                        
                        if (isset($dto->id)) {
                            $main->{$alias}()->updateOrCreate($dtoArray, $dto->toArray());
                        } else {
                            $table = DB::table($main->{$alias}()->getRelated()->getTable());
                            $dtoExists = $table->where($dto->toArray())->exists();
                            if (! $dtoExists) {
                                $table->insert($dto->toArray());
                                DB::table($main->{$alias}()->getTable())->insert([
                                    $main->{$alias}()->getForeignPivotKeyName() => $main->{$main->getKeyName()},
                                    $main->{$alias}()->getRelatedPivotKeyName() => DB::getPdo()->lastInsertId()
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    protected function createModel($classModel, $dto)
    {
        $alias = key($classModel);
        $class = $classModel[$alias];
        
        return new $class($dto->toArray());
    }
}