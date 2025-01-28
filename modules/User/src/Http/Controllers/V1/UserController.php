<?php

namespace Modules\User\Http\Controllers\V1;

use OpenApi\Attributes as OA;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\User\Services\UserService;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    #[OA\Post(
        path: '/v1/api/users', 
        summary: 'Cria um novo usuário no sistema',
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'Nome do usário'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        description: 'Email do usuário usado para obeter o token de acesso',
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: 'Senha de acesso do usuário'
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        description: 'Confirmação da senha de acesso do usuário'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: 'Sucesso'
            ),
            new OA\Response(
                response: 422, 
                description: 'Parametros inválidos ou nulos'
            )
        ]
    )]
    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserService $userService)
    {
        try {
            $validated = request()->validate([
                'name' => 'string|required|min:3|max:150',
                'email' => 'email|string|required|min:10|max:150|unique:users',
                'password' => 'required|string|confirmed:password'
            ]);
    
            $userService->store($validated);
            return response()->json([], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[OA\Post(
        path: '/v1/api/users/token',
        summary: 'Obtem o token de acesso da api',
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        description: 'Email de acesso'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: 'Senha de acesso'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'token',
                            type: 'string',
                            description: 'Token de acesso'
                        ),
                        new OA\Property(
                            property: 'type',
                            type: 'string',
                            description: 'Tipo de token'
                        ),
                        new OA\Property(
                            property: 'expire',
                            type: 'integer',
                            description: 'Tempo de expiração'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Credênciais inválidas'
            )
        ]
    )]
    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(UserService $userService)
    {
        $credentials = request()->only('email', 'password');
        [$token, $code] = $userService->generateToken($credentials);
        return response($token, $code);
    }

    #[OA\Get(
        path: '/v1/api/users',
        summary: 'Dados do usuário logado',
        security: [['bearer_auth' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sucesso"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            ),
        ]
    )]
    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(UserService $userService)
    {
        return response()->json($userService->getLoggedUser(), Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/v1/api/users/logout',
        summary: 'Inválidar token de acesso',
        security: [['bearer_auth' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sucesso"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            ),
        ]
    )]
    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(UserService $userService)
    {
        return response()->json($userService->logout(), Response::HTTP_OK);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized()
    {
        return response()->json([
            'message' => __('user::app.user.unauthorized')
        ], Response::HTTP_UNAUTHORIZED);
    }
}