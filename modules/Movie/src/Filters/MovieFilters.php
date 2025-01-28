<?php

namespace Modules\Movie\Filters;

use Illuminate\Support\Arr;
use Modules\Movie\Dto\Filter\QueryDto;
use Modules\Movie\Models\Movie;

class MovieFilters
{
    public function __construct(
        protected string $abstract = Movie::class
    ) { }

    /**
     * Define filter rules to global scope to abstract model
     * 
     * @return void
     */
    public function apply(): void
    {
        $parameter = request()->only('with', 'fields', 'query');

        foreach ($parameter as $method => $value) {
            if (method_exists($this, $method)) {
                if (is_null($value) || empty($value)) {
                    continue ;
                }
                $this->{$method}($value);
            }
        }
    }

    /**
     * @param string $withs example{with=genres,spoken_languages...}
     * 
     * @return void
     */
    public function with(string $withs): void
    {
        $withs = explode(',', $withs);
        $this->abstract::addGlobalScope(
            'withs', fn ($builder) => $builder->with($withs)
        );
    }

    /**
     * @param string $fields
     * 
     * @return void
     */
    public function fields(string $fields): void
    {
        $columns = $fields == '' ? ['*'] :  explode(',', $fields);

        $this->abstract::addGlobalScope(
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $withFields
     * 
     * @return void
     */
    public function withFields(&$builder, $withFields): void
    {
        $_withs = [];
        foreach ($withFields as $relashion => $columns) {
            $columns = Arr::map($columns, fn ($column) => "$relashion.$column");
            $_withs[$relashion] = fn ($builder) => $builder->select($columns);
        }

        $builder->with($_withs);
    }

    /**
     * @param array $queries
     * 
     * @return void
     */
    public function query($queries)
    {
        $queryDtos = new QueryDto($queries);
        if ($queryDtos->has('and') || $queryDtos->has('or')) {

            $this->abstract::addGlobalScope(
                'and', fn ($builder) => $this->resolveWhere($builder, $queryDtos->and, 'and')
            );

            $this->abstract::addGlobalScope(
                'or', fn ($builder) => $this->resolveWhere($builder, $queryDtos->or, 'or')
            );

        }

        if ($queryDtos->isValid()) {
            $this->abstract::addGlobalScope(
                'condition', fn ($builder) => $builder->where(...$queryDtos->toWhereArray())
            );
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array<QueryDto> $conditions
     * @param string $type
     * 
     * @return void
     */
    protected function resolveWhere(&$builder, array $conditions, $type = 'and'): void
    {
        foreach ($conditions as $condition) {
            if ($type == 'and') {
                $builder->where(...$condition->toWhereArray());
                continue ;
            }

            if ($type == 'or') {
                $builder->orWhere(...$condition->toWhereArray());
                continue ;
            }
        }
    }
}