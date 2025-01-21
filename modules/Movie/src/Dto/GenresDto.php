<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class GenresDto extends DataTransferObject
{
    public int $id;

    public string $name;
}