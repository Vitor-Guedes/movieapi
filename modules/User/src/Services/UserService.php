<?php

namespace Modules\User\Services;

use Exception;
use Modules\User\Models\User;

class UserService
{
    /**
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $data): \Illuminate\Database\Eloquent\Model
    {
        return User::create($data);
    }

    /**
     * @param array $credentials email,password
     * 
     * @return array [$token, $code]
     */
    public function generateToken(array $credentials): array
    {
        try {
            /** @var \Tymon\JWTAuth\JWTGuard $guard */
            $guard = auth('api');
            if (!$token = $guard->attempt($credentials)) {
                $message = ['error' => __('user::app.credentials.invalid')];
                $code = \Illuminate\Http\Response::HTTP_BAD_REQUEST;
                return [$message, $code];
            }
    
            $user = $guard->user();

            $token = $guard->claims(['role' => 'movie_user'])->fromUser($user);
            
            $token = [
                'token' => $token,
                'type' => 'bearer',
                'expire' => $guard->factory()->getTTL() * 60
            ];

            return [$token, \Illuminate\Http\Response::HTTP_OK];
        } catch (Exception $e) {
            $message = ['error' => $e->getMessage()];
            $code = \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR;
            return [$message, $code];
        }
    }

    /**
     * @return User
     */
    public function getLoggedUser(): User
    {
        return auth('api')->user();
    }

    /**
     * @return array
     */
    public function getLoggedUserArray(): array
    {
        return $this->getLoggedUser()->toArray();
    }

    /**
     * @return array
     */
    public function logout(): array
    {
        auth('api')->parseToken()->invalidate(true);
        return [];
    }
}