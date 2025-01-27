<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\User\Services\UserService;
use Modules\User\Services\ReviewService;

class ReviewController extends Controller
{
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

    /**
     * @param int $id
     * @param ReviewService $reviewService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, ReviewService $reviewService)
    {
        $validated = request()->validate([
            'review' => 'string|nullable',
            'positive' => 'boolean|nullable'
        ]);

        $reviewService->update($id, $validated);

        return response()->json([
            'success' => true,
            'message' => __('user::app.review.update.success')
        ], Response::HTTP_OK);
    }

      /**
     * @param int $id
     * @param ReviewService $reviewService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, ReviewService $reviewService)
    {
        $reviewService->destroy($id);

        return response()->json([
            'success' => true,
            'message' => __('user::app.review.destroy.success')
        ], Response::HTTP_OK);
    }
}