<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class ProductionCountriesDto extends DataTransferObject
{
    public string $name;

    public string $iso_3166_1;
}