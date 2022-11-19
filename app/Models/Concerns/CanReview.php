<?php

namespace App\Models\Concerns;

trait CanReview
{
    /**
     * Check if a review for a specific model needs to be approved.
     *
     * @param mixed $model
     * @return bool
     */
    public function needsReviewApproval($model): bool
    {
        return false;
    }
}
