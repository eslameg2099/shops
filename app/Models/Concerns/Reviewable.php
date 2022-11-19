<?php

namespace App\Models\Concerns;

use App\Models\Contracts\Reviewer;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Reviewable
{
    /**
     * Get all the entity reviews.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reviews()
    {
        return $this->morphMany(config('reviewable.review_class'), 'reviewable');
    }

    /**
     * Attach a review to this model.
     *
     * @param float $rate
     * @param string $review
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function review(float $rate, string $review)
    {
        return $this->reviewAsUser(auth()->user(), $rate, $review);
    }

    /**
     * Attach a review to this model as a specific user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param float $rate
     * @param string $review
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reviewAsUser(User $user, float $rate, string $review)
    {
        $review = $this->reviews()
            ->updateOrCreate([
                'user_id' => $user->getKey(),
            ], [
                'comment' => $review,
                'rate' => $rate,
                'is_approved' => ($user instanceof Reviewer) ? ! $user->needsReviewApproval($this) : false,
            ]);

        $this->afterReview($review);

        return $review;
    }

    /**
     * Handel an event after review.
     *
     * @param $review
     * @return void|mixed
     */
    protected function afterReview($review)
    {
        //
    }

    /**
     * Boot the reviewable trait for a model.
     *
     * @return void
     */
    public static function bootReviewable()
    {
        static::deleting(function (self $model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (! $model->forceDeleting) {
                    return;
                }
            }

            $model->reviews()->cursor()->each(fn ($review) => $review->delete());
        });
    }
}
