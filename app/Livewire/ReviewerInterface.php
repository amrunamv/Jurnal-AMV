<?php

namespace App\Livewire;

use App\Models\Review;
use App\Services\ReviewerAssignmentService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ReviewerInterface extends Component
{
    public Review $review;
    
    // Review form fields
    public int $overallScore = 0;
    public string $recommendation = '';
    public string $commentsToEditor = '';
    public string $commentsToAuthor = '';
    public array $rubricScores = [];
    
    // UI state
    public bool $showDeclineModal = false;
    public string $declineReason = '';
    
    // PDF viewer state
    public int $currentPage = 1;

    protected array $rules = [
        'overallScore' => 'required|integer|min:1|max:10',
        'recommendation' => 'required|in:accept,minor_revision,major_revision,reject',
        'commentsToEditor' => 'required|string|min:50',
        'commentsToAuthor' => 'required|string|min:100',
    ];

    protected array $messages = [
        'overallScore.required' => 'Please provide an overall score.',
        'overallScore.min' => 'Score must be at least 1.',
        'overallScore.max' => 'Score cannot exceed 10.',
        'recommendation.required' => 'Please select a recommendation.',
        'commentsToEditor.required' => 'Comments to editor are required.',
        'commentsToEditor.min' => 'Comments to editor must be at least 50 characters.',
        'commentsToAuthor.required' => 'Comments to author are required.',
        'commentsToAuthor.min' => 'Comments to author must be at least 100 characters.',
    ];

    public function mount(Review $review): void
    {
        // Verify the reviewer is authorized
        abort_unless(Auth::id() === $review->reviewer_id, 403);
        
        $this->review = $review->load('manuscript');
        
        // Pre-fill if review already has data
        if ($review->overall_score) {
            $this->overallScore = $review->overall_score;
            $this->recommendation = $review->recommendation ?? '';
            $this->commentsToEditor = $review->comments_to_editor ?? '';
            $this->commentsToAuthor = $review->comments_to_author ?? '';
            $this->rubricScores = $review->rubric_scores ?? [];
        }
    }

    public function acceptInvitation(): void
    {
        $service = app(ReviewerAssignmentService::class);
        $service->acceptInvitation($this->review);
        
        $this->review->refresh();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Review invitation accepted.',
        ]);
    }

    public function declineInvitation(): void
    {
        $this->validate(['declineReason' => 'required|min:20']);
        
        $service = app(ReviewerAssignmentService::class);
        $service->declineInvitation($this->review, $this->declineReason);
        
        $this->redirect(route('console.reviews.index'));
    }

    public function submitReview(): void
    {
        $this->validate();

        $service = app(ReviewerAssignmentService::class);
        $service->submitReview(
            $this->review,
            $this->overallScore,
            $this->recommendation,
            $this->commentsToEditor,
            $this->commentsToAuthor,
            $this->rubricScores
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Review submitted successfully.',
        ]);
        $this->redirect(route('console.reviews.index'));
    }

    public function saveDraft(): void
    {
        $this->review->update([
            'overall_score' => $this->overallScore ?: null,
            'recommendation' => $this->recommendation ?: null,
            'comments_to_editor' => $this->commentsToEditor ?: null,
            'comments_to_author' => $this->commentsToAuthor ?: null,
            'rubric_scores' => $this->rubricScores ?: null,
        ]);

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Draft saved.',
        ]);
    }

    public function setRubricScore(string $criterion, int $score): void
    {
        $this->rubricScores[$criterion] = $score;
    }

    public function render()
    {
        return view('livewire.reviewer-interface');
    }
}
