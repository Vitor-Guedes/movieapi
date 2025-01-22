<?php

namespace Modules\Movie\Facets;

class MovieFacet
{
    public function apply()
    {
        $parameter = request()->all();

        foreach ($parameter as $method => $value) {
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    public function with(string $withs)
    {
        $withs = explode(',', $withs);
        \Modules\Movie\Models\Movie::addGlobalScope(
            'withs', fn ($builder) => $builder->with($withs)
        );
    }

    public function fields(string $fields)
    {
        $columns = $fields == '' ? ['*'] :  explode(',', $fields);

        \Modules\Movie\Models\Movie::addGlobalScope(
            'fields', function ($builder) use ($columns) {
                $_columns = $columnRelashion = [];
                foreach ($columns as $column ) {
                    if (strpos($column, '.') === false ) {
                        $_columns[] = $column;
                        continue ;
                    }

                    [$relashion, $column] = explode('.', $column);
                    $columnRelashion[$relashion][] = $column;
                };

                $builder->select($_columns);
                if ($columnRelashion) {
                    $this->withFields($builder, $columnRelashion);
                }
            }
        );
    }

    public function withFields(&$builder, $withFields)
    {
        $withs = [];
        foreach ($withFields as $relashion => $columns) {
            $withs[] = "$relashion:" . implode(',', $columns);
        }
        $builder->with($withs);
    }
}