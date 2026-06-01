<?php

namespace App\Services;

use App\Models\Manuscript;
use App\Models\ManuscriptRevision;
use App\Models\Review;
use App\Notifications\RevisionRequestedNotification;
use Illuminate\Support\Collection;

class RevisionLoopService
{
    protected ManuscriptWorkflowService $workflowService;

    public function __construct(ManuscriptWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Analyze reviews and determine required action
     */
    public function analyzeReviews(Manuscript $manuscript): array
    {
        $completedReviews = $manuscript->reviews()
            ->where('status', Review::STATUS_COMPLETED)
            ->latest('completed_at')
            ->get();

        if ($completedReviews->isEmpty()) {
            return [
                'action' => 'waiting',
                'message' => 'Waiting for reviews to be completed',
                'completed_count' => 0,
            ];
        }

        $recommendations = $completedReviews->pluck('recommendation')->countBy();

        // Decision logic based on recommendations
        $decision = $this->determineDecision($recommendations);

        return [
            'action' => $decision['action'],
            'message' => $decision['message'],
            'completed_count' => $completedReviews->count(),
            'recommendations' => $recommendations->toArray(),
            'revision_type' => $decision['revision_type'] ?? null,
        ];
    }

    /**
     * Determine the editorial decision based on review recommendations
     */
    private function determineDecision(Collection $recommendations): array
    {
        $total = $recommendations->sum();
        $acceptCount = $recommendations->get(Review::RECOMMENDATION_ACCEPT, 0);
        $minorCount = $recommendations->get(Review::RECOMMENDATION_MINOR_REVISION, 0);
        $majorCount = $recommendations->get(Review::RECOMMENDATION_MAJOR_REVISION, 0);
        $rejectCount = $recommendations->get(Review::RECOMMENDATION_REJECT, 0);

        // All reviewers recommend rejection
        if ($rejectCount === $total) {
            return [
                'action' => 'reject',
                'message' => 'All reviewers recommend rejection',
            ];
        }

        // Majority recommend rejection
        if ($rejectCount > $total / 2) {
            return [
                'action' => 'reject',
                'message' => 'Majority of reviewers recommend rejection',
            ];
        }

        // All reviewers recommend acceptance
        if ($acceptCount === $total) {
            return [
                'action' => 'accept',
                'message' => 'All reviewers recommend acceptance',
            ];
        }

        // Majority recommend acceptance with no major revisions
        if ($acceptCount >= $total / 2 && $majorCount === 0) {
            if ($minorCount > 0) {
                return [
                    'action' => 'revision',
                    'revision_type' => 'minor',
                    'message' => 'Minor revisions recommended',
                ];
            }
            return [
                'action' => 'accept',
                'message' => 'Majority recommend acceptance',
            ];
        }

        // Any major revision recommendations
        if ($majorCount > 0) {
            return [
                'action' => 'revision',
                'revision_type' => 'major',
                'message' => 'Major revisions required',
            ];
        }

        // Default to minor revision
        return [
            'action' => 'revision',
            'revision_type' => 'minor',
            'message' => 'Revisions recommended',
        ];
    }

    /**
     * Execute the decision based on review analysis
     */
    public function executeDecision(Manuscript $manuscript, string $action, ?string $revisionType = null): bool
    {
        switch ($action) {
            case 'accept':
                return $this->workflowService->accept($manuscript);

            case 'reject':
                return $this->workflowService->reject($manuscript);

            case 'revision':
                $result = $this->workflowService->requestRevision($manuscript, $revisionType ?? 'major');
                
                if ($result) {
                    // Notify the author
                    $editorComments = $this->compileReviewerComments($manuscript);
                    $manuscript->submitter->notify(
                        new RevisionRequestedNotification($manuscript, $revisionType ?? 'major', $editorComments)
                    );
                }
                
                return $result;

            default:
                return false;
        }
    }

    /**
     * Compile reviewer comments for the author (without revealing identities)
     */
    public function compileReviewerComments(Manuscript $manuscript): string
    {
        $reviews = $manuscript->reviews()
            ->where('status', Review::STATUS_COMPLETED)
            ->whereNotNull('comments_to_author')
            ->get();

        $comments = [];
        foreach ($reviews as $index => $review) {
            $reviewerLabel = 'Reviewer ' . ($index + 1);
            $recommendation = ucfirst(str_replace('_', ' ', $review->recommendation));
            
            $comments[] = "**{$reviewerLabel}** (Recommendation: {$recommendation}):\n{$review->comments_to_author}";
        }

        return implode("\n\n---\n\n", $comments);
    }

    /**
     * Get the revision history for a manuscript
     */
    public function getRevisionHistory(Manuscript $manuscript): Collection
    {
        return $manuscript->revisions()
            ->with('uploader:id,name')
            ->orderBy('version', 'asc')
            ->get()
            ->map(function (ManuscriptRevision $revision) {
                return [
                    'version' => $revision->version,
                    'title' => $revision->title_snapshot,
                    'abstract' => $revision->abstract_snapshot,
                    'uploaded_by' => $revision->uploader?->name ?? 'Unknown',
                    'uploaded_at' => $revision->created_at->format('d M Y H:i'),
                    'author_notes' => $revision->author_notes,
                    'editor_notes' => $revision->editor_notes,
                    'status' => $revision->status,
                ];
            });
    }

    /**
     * Get current revision round
     */
    public function getCurrentRound(Manuscript $manuscript): int
    {
        return $manuscript->reviews()->max('round') ?? 1;
    }

    /**
     * Check if manuscript needs another review round
     */
    public function needsAnotherRound(Manuscript $manuscript): bool
    {
        // If status is revision_submitted, it needs to go back to review
        return $manuscript->status === Manuscript::STATUS_REVISION_SUBMITTED;
    }

    /**
     * Start a new review round after revision submission
     */
    public function startNewReviewRound(Manuscript $manuscript): bool
    {
        if (!$this->needsAnotherRound($manuscript)) {
            return false;
        }

        // Transition manuscript back to under_review
        return $this->workflowService->sendForReview($manuscript);
    }
}
