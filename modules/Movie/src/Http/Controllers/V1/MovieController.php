<?php

namespace Modules\Movie\Http\Controllers\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use Modules\Movie\Services\MovieService;

#[OA\Server("http://dev.backend.com")]
#[OA\Info("1.0", "Api para vizualização de informações de filmes migrados a partir de uma dataset", "Api de Files")]
class MovieController extends Controller
{
    #[OA\Get(path: '/v1/api/movies')]
    #[OA\Response(
        response: 200, 
        description: 'Successful operation',
    )]
    public function index(MovieService $movieService)
    {
        $data = $movieService->filterApply(function ($service) {
            return $service->list();
        });
        return response()->json($data, Response::HTTP_OK);
    }

    #[OA\Get(path: '/v1/api/movies/{id}/{border}')]
    #[OA\Response(
        response: 200, 
        description: 'Successful operation',
    )]
    public function findWithRelation(int $id, string $relation, MovieService $movieService)
    {
        $data = $movieService->findWithRelation($id, $relation);
        return response()->json($data, Response::HTTP_OK);
    }
}