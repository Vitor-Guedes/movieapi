<?php

namespace Modules\Movie\Pipes\Images;

use Modules\Movie\Dto\ImageSearchDto;
use Modules\Movie\Services\MovieImageService;

class StoreInDatabase
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
        $this->movieImageService->store(
            $imageSearchDto->movie, 
            $imageSearchDto->image_list
        );

        return $next($imageSearchDto);
    }
}