<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class ProductionCompanyDto extends DataTransferObject
{
    public int $id;

    public string $name;
}