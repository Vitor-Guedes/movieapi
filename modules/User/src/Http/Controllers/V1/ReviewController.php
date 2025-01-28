<?php

namespace Modules\User\Http\Controllers\V1;

use OpenApi\Attributes as OA;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Exception;
use Modules\User\Services\UserService;
use Modules\User\Services\ReviewService;

class ReviewController extends Controller
{
    #[OA\Post(
        path: '/v1/api/reviews', 
        summary: 'Cria uma nova review do usuário para o filme',
        security: [['bearer_auth' => []]],
        tags: ['Review'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'review',
                        type: 'string',
                        description: 'Texto contendo o conteúdo da review'
                    ),
                    new OA\Property(
                        property: 'movie_id',
                        type: 'integer',
                        description: 'Id do filme que vai receber a review'
                    ),
                    new OA\Property(
                        property: 'positive',
                        type: 'boolean',
                        description: 'Se a review é positiva ou negativa'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Sucesso", 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            description: 'Se foi um sucesso'
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            description: 'Texto de retorno da requisição'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: 'Não autorizado'
            ),
        ]
    )]
    /**
     * @param ReviewService $reviewService
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ReviewService $reviewService, UserService $userService)
    {
        $validated = request()->validate([
            'review' => 'string|required',
            'positive' => 'boolean|nullable',
            'movie_id' => 'exists:movies,id|required'
        ]);
        $user = $userService->getLoggedUser();

        $reviewService->store($validated, $user);

        return response()->json([
            'success' => true,
            'message' => __('user::app.review.store.success')
        ], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/v1/api/reviews',
        summary: 'Lista de reviews do usuário',
        security: [['bearer_auth' => []]],
        tags: ['Review'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sucesso'
            ),
            new OA\Response(
                response: 401, 
                description: 'Não autorizado'
            )
        ]
    )]
    /**
     * @param ReviewService $reviewService
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(ReviewService $reviewService, UserService $userService)
    {
        return response()->json(
            $reviewService->list($userService->getLoggedUser()),
            Response::HTTP_OK
        );
    }

    #[OA\Put(
        path: '/v1/api/reviews/{reviewId}', 
        summary: 'Atualiza uma review do usuário para o filme',
        security: [['bearer_auth' => []]],
        tags: ['Review'],
        parameters: [
            new OA\Parameter(
                in: 'path',
                name: 'reviewId',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'review',
                        type: 'string',
                        description: 'Texto contendo o conteúdo da review'
                    ),
                    new OA\Property(
                        property: 'positive',
                        type: 'boolean',
                        description: 'Se a review é positiva ou negativa'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Sucesso", 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            description: 'Se foi um sucesso'
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            description: 'Texto de retorno da requisição'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: 'Não autorizado'
            ),
            new OA\Response(
                response: 400, 
                description: 'Parametros inválidos'
            ),
        ]
    )]
    /**
     * @param int $id
     * @param ReviewService $reviewService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, ReviewService $reviewService)
    {
        try {
            $validated = request()->validate([
                'review' => 'string|nullable',
                'positive' => 'boolean|nullable'
            ]);
    
            $reviewService->update($id, $validated);
    
            return response()->json([
                'success' => true,
                'message' => __('user::app.review.update.success')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Delete(
        path: '/v1/api/reviews/{reviewId}', 
        summary: 'Deleta uma review do usuário para o filme',
        security: [['bearer_auth' => []]],
        tags: ['Review'],
        parameters: [
            new OA\Parameter(
                in: 'path',
                name: 'reviewId',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Sucesso", 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            description: 'Se foi um sucesso'
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            description: 'Texto de retorno da requisição'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: 'Não autorizado'
            ),
            new OA\Response(
                response: 400, 
                description: 'Parametros inválidos'
            ),
        ]
    )]
    /**
     * @param int $id
     * @param ReviewService $reviewService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, ReviewService $reviewService)
    {
        try {
            $reviewService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => __('user::app.review.destroy.success')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}