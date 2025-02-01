<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;
use Modules\Movie\Models\Movie;

class ImageSearchDto extends DataTransferObject
{
    public string $term = '';

    public Movie $movie;

    public array $image_list = [];
}