<?php

namespace Modules\Importer;

use Modules\Importer\Rule\MapItem;
use Modules\Importer\Rule\MapRelashion;

class Rule
{
    protected array $mapItems = [];

    protected array $mapRelashions = [];

    public function __construct(
        array $map,
        array $relashions = []
    ) 
    {
        $this->makeMapItems($map);
        $this->makeMapRelashions($relashions);
    }

    /**
     * Create Map itens Rule
     * 
     * @param array $map
     * 
     * @return void
     */
    protected function makeMapItems(array $map = []): void
    {
        array_map(
            fn ($item) => $this->setMapItem(new MapItem(...$item)), 
            $map
        );
    }

     /**
     * Create Map Relashions Rule
     * 
     * @param array $relashions
     * 
     * @return void
     */
    protected function makeMapRelashions(array $relashions = []): void
    {
        array_map(
            fn ($item) => $this->setMapRelashion(new MapRelashion(...$item)), 
            $relashions
        );
    }

    /**
     * @param string $alias
     * 
     * @return MapItem|null
     */
    public function getMapItem(string $alias = ''): MapItem|null
    {
        return isset($this->mapItems[$alias]) ? $this->mapItems[$alias] : null;
    }

    /**
     * @param MapItem $mapItem
     * 
     * @return void
     */
    public function setMapItem(MapItem $mapItem): void
    {
        if ($alias = $mapItem->getAlias()) {
            $this->mapItems[$alias] = $mapItem;
            return ;
        }
        $this->mapItems[] = $mapItem;
    }

    /**
     * @return array
     */
    public function getMapItems(): array
    {
        return $this->mapItems;
    }

    /**
     * @param string $alias
     * 
     * @return MapRelashion|null
     */
    public function getMapRelashion(string $alias = ''): MapRelashion|null
    {
        return isset($this->mapRelashions[$alias]) ? $this->mapRelashions[$alias] : null;
    }

    /**
     * @param MapRelashion $mapItem
     * 
     * @return void
     */
    public function setMapRelashion(MapRelashion $mapRelashion): void
    {
        if ($alias = $mapRelashion->getAlias()) {
            $this->mapRelashions[$alias] = $mapRelashion;
            return ;
        }
        $this->mapRelashions[] = $mapRelashion;
    }

    /**
     * @return array
     */
    public function getMapRelashions(): array
    {
        return $this->mapRelashions;
    }

}