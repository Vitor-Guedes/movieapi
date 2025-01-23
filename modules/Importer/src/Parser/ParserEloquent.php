<?php

namespace Modules\Importer\Parser;

use Illuminate\Support\Arr;
use Modules\Importer\Parser;

class ParserEloquent extends Parser
{
    public function parse(): mixed
    {
        $instance = $this->createToFromDto();
        
        $properties = $this->getPropertiesFromDto();

        foreach ($properties as $key => $value) {
            if ($this->rules->getMapItem($key)) {
                if (is_array($this->dto->{$key})) {
                    $parsedList = Arr::map($this->dto->{$key}, function ($dto) {
                        $parser = new ParserEloquent($dto, $this->rules);
                        return $parser->parse();
                    });

                    if ($mapRelashion = $this->rules->getMapRelashion($key)) {
                        $relations = array_merge(
                            $instance->getRelations() ?? [], [
                                $mapRelashion->getAlias() => $parsedList
                            ]);

                        $instance->setRelations($relations);
                    }
                }

                continue ;
            }

            if (is_null($instance)) {
                $a = 1;
            }

            $instance->{$key} = $value;
        }

        return $instance;
    }

    /**
     * @return object|null
     */
    protected function createToFromDto(): ?object
    {
        $dtoClass = get_class($this->dto);

        foreach ($this->rules->getMapItems() as $mapItem) {
            if ($mapItem->getFrom() === $dtoClass) {
                return $mapItem->createNewTo();
            }
        }

        return null;
    }

    protected function getPropertiesFromDto(): array
    {
        return $this->dto->toArray();
    }
}