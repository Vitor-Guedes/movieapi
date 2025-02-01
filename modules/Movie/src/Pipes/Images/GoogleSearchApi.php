<?php

namespace Modules\Movie\Pipes\Images;

use Closure;
use Modules\Movie\Dto\ImageSearchDto;
use Modules\Movie\Services\MovieImageService;

class GoogleSearchApi
{
    public function __construct(
        protected MovieImageService $movieImageService
    ) { }

    /**
     * @param ImageSearchDto $imageSearchDto
     * @param Closure $next
     *  
     * @return mixed
     */
    public function handle(ImageSearchDto $imageSearchDto, $next)
    {
        $imageSearchDto->image_list = $this->movieImageService
            ->googleSearch(
                $imageSearchDto->term
            );

        return $next($imageSearchDto);
    }

}