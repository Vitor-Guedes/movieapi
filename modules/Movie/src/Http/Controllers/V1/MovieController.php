<?php

namespace Modules\Movie\Http\Controllers\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use Modules\Movie\Services\MovieService;

class MovieController extends Controller
{
    #[OA\Get(
        path: '/v1/api/movies',
        summary: 'Lista os filmes',
        tags: ['Movies'],
        parameters: [
            new OA\Parameter(
                in: 'query',
                name: 'page',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
            new OA\Parameter(
                in: 'query',
                name: 'limit',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
            new OA\Parameter(
                in: 'query',
                name: 'with',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                        enum: [
                            'genres',
                            'keywords',
                            'production_companies',
                            'production_countries',
                            'spoken_languages'
                        ]
                    )
                ),
                style: 'form',
                explode: false
            ),
            new OA\Parameter(
                in: 'query',
                name: 'fields',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                        enum: [
                            'id',
                            'budget',
                            'homepage',
                            'original_language',
                            'original_title',
                            'overview',
                            'popularity',
                            'release_date',
                            'revenue',
                            'runtime',
                            'status',
                            'tagline',
                            'title',
                            'vote_average',
                            'vote_count',
                            'genres.id',
                            'genres.name',
                            'keywords.id',
                            'keywords.name',
                            'production_companies.id',
                            'production_companies.name',
                            'production_countries.name',
                            'production_countries.iso_3166_1',
                            'spoken_languages.name',
                            'spoken_languages.iso_639_1'
                        ]
                    )
                ),
                style: 'form',
                explode: false
            ),
            new OA\Parameter(
                in: 'query',
                name: 'query',
                required: false,
                schema: new OA\Schema(
                    type: "object",
                    properties: [
                        new OA\Property(property: "field", type: "string", example: "title"),
                        new OA\Property(property: "operator", type: "string", example: "lk"),
                        new OA\Property(property: "value", type: "string", example: "ava"),
                        new OA\Property(
                            property: "and",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "field", type: "string", example: "title"),
                                    new OA\Property(property: "operator", type: "string", example: "lk"),
                                    new OA\Property(property: "value", type: "string", example: "ava")
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "or",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "field", type: "string", example: "title"),
                                    new OA\Property(property: "operator", type: "string", example: "eq"),
                                    new OA\Property(property: "value", type: "string", example: "ava")
                                ]
                            )
                        )
                    ]
                ),
                style: 'form',
                explode: false
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sucesso'
            )
        ]
    )]
    /**
     * @param MovieService $movieService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(MovieService $movieService)
    {
        $data = $movieService->filterApply(function ($service) {
            return $service->list();
        });
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @param string $relation
     * @param MovieService $movieService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function findWithRelation(int $id, string $relation, MovieService $movieService)
    {
        $data = $movieService->findWithRelation($id, $relation);
        return response()->json($data, Response::HTTP_OK);
    }
}