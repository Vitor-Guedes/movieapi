<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class KeywordsDto extends DataTransferObject
{
    public int $id;

    public string $name;
}