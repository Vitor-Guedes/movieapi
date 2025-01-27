<?php

namespace Modules\User\Services;

use Modules\User\Models\Review;
use Modules\User\Models\User;

class ReviewService
{
    /**
     * @param array $data
     * @param User $user
     * 
     * @return Review
     */
    public function store(array $data, User $user): Review
    {
        return $user->reviews()->create($data);
    }

    /**
     * @param User $user
     * 
     * @return array
     */
    public function list(User $user): array
    {
        $limit = request()->input('limit', 10);
        return $user->reviews()->simplePaginate($limit)->toArray();
    }

    /**
     * @param int $id
     * @param array $data
     * 
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $review = Review::findOrFail($id);
        return $review->update($data);
    }

    /**
     * @param int $id
     * 
     * @return bool
     */
    public function destroy(int $id): bool
    {
        $review = Review::findOrFail($id);
        return $review->delete();
    }
}