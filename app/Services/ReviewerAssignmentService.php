<?php

namespace App\Services;

use App\Models\Manuscript;
use App\Models\ManuscriptActivity;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewerAssignedNotification;
use Illuminate\Support\Facades\DB;

class ReviewerAssignmentService
{
    protected ConflictOfInterestChecker $conflictChecker;

    public function __construct(ConflictOfInterestChecker $conflictChecker)
    {
        $this->conflictChecker = $conflictChecker;
    }

    /**
     * Assign a reviewer to a manuscript (double-blind)
     */
    public function assignReviewer(
        Manuscript $manuscript,
        User $reviewer,
        User $assigner,
        ?int $daysUntilDue = 14
    ): ?Review {
        // Check for conflicts
        if ($this->conflictChecker->hasConflict($reviewer, $manuscript)) {
            return null;
        }

        // Check if reviewer is already assigned
        $existingReview = Review::where('manuscript_id', $manuscript->id)
            ->where('reviewer_id', $reviewer->id)
            ->whereIn('status', [Review::STATUS_PENDING, Review::STATUS_ACCEPTED])
            ->first();

        if ($existingReview) {
            return null;
        }

        // Get current review round
        $currentRound = Review::where('manuscript_id', $manuscript->id)->max('round') ?? 1;

        return DB::transaction(function () use ($manuscript, $reviewer, $assigner, $daysUntilDue, $currentRound) {
            $review = Review::create([
                'manuscript_id' => $manuscript->id,
                'revision_id' => $manuscript->latestRevision?->id,
                'reviewer_id' => $reviewer->id,
                'assigned_by' => $assigner->id,
                'status' => Review::STATUS_PENDING,
                'round' => $currentRound,
                'assigned_at' => now(),
                'due_date' => now()->addDays($daysUntilDue),
            ]);

            ManuscriptActivity::log(
                $manuscript,
                'assigned_reviewer',
                null,
                null,
                'Reviewer assigned (anonymous)',
                ['review_id' => $review->id, 'round' => $currentRound]
            );

            // Send notification to reviewer via queue
            $reviewer->notify(new ReviewerAssignedNotification($review));

            return $review;
        });
    }

    /**
     * Get suggested reviewers for a manuscript
     */
    public function getSuggestedReviewers(Manuscript $manuscript, int $limit = 10): array
    {
        $eligibleReviewers = $this->conflictChecker->getEligibleReviewers($manuscript, $limit);

        return $eligibleReviewers->map(function ($reviewer) {
            return [
                'id' => $reviewer->id,
                'name' => $reviewer->name,
                'email' => $reviewer->email,
                'affiliation' => $reviewer->affiliation?->name ?? 'N/A',
                'research_interests' => $reviewer->research_interests ?? [],
            ];
        })->toArray();
    }

    /**
     * Reviewer accepts the review invitation
     */
    public function acceptReviewInvitation(Review $review): bool
    {
        if ($review->status !== Review::STATUS_PENDING) {
            return false;
        }

        $review->status = Review::STATUS_ACCEPTED;
        $review->accepted_at = now();
        $review->save();

        ManuscriptActivity::log(
            $review->manuscript,
            'review_invitation_accepted',
            null,
            null,
            'Reviewer accepted invitation'
        );

        return true;
    }

    /**
     * Reviewer declines the review invitation
     */
    public function declineReviewInvitation(Review $review, string $reason = ''): bool
    {
        if ($review->status !== Review::STATUS_PENDING) {
            return false;
        }

        $review->status = Review::STATUS_DECLINED;
        $review->save();

        ManuscriptActivity::log(
            $review->manuscript,
            'review_invitation_declined',
            null,
            null,
            $reason ?: 'Reviewer declined invitation'
        );

        return true;
    }

    /**
     * Submit review
     */
    public function submitReview(
        Review $review,
        string $commentsToAuthor,
        string $commentsToEditor,
        string $recommendation,
        array $scores = []
    ): bool {
        if ($review->status !== Review::STATUS_ACCEPTED) {
            return false;
        }

        $review->comments_to_author = $commentsToAuthor;
        $review->comments_to_editor = $commentsToEditor;
        $review->recommendation = $recommendation;
        $review->scores = $scores;
        $review->status = Review::STATUS_COMPLETED;
        $review->completed_at = now();
        $review->save();

        ManuscriptActivity::log(
            $review->manuscript,
            'review_completed',
            null,
            null,
            "Review submitted with recommendation: {$recommendation}"
        );

        return true;
    }
}
