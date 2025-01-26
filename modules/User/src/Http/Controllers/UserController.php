<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserService $userService)
    {
        $validated = request()->validate([
            'name' => 'string|required|min:3|max:150',
            'email' => 'string|required|min:10|max:150|unique:users',
            'password' => 'string|confirmed:password'
        ]);

        $userService->store($validated);

        return response()->json([], Response::HTTP_CREATED);
    }

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

    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(UserService $userService)
    {
        return response()->json($userService->getLoggedUser(), Response::HTTP_OK);
    }

    /**
     * @param UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(UserService $userService)
    {
        return response()->json($userService->logout(), Response::HTTP_OK);
    }
}