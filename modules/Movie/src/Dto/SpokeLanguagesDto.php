<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class SpokeLanguagesDto extends DataTransferObject
{
    public string $name;

    public string $iso_639_1;
}