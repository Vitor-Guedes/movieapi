<?php

namespace Modules\Movie\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Server("http://dev.backend.com")]
#[OA\Info("1.0", "Api para vizualização de informações de filmes migrados a partir de uma dataset", "Api de Files")]
class MovieController extends Controller
{
    #[OA\Get(path: '/v1/api/movies')]
    #[OA\Response(
        response: 200, 
        description: 'Successful operation',
    )]
    public function index()
    {
        
    }
}