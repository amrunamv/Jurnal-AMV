<?php

namespace App\Services;

use App\Models\Manuscript;
use App\Models\ManuscriptActivity;
use App\Models\ManuscriptRevision;
use Illuminate\Support\Facades\DB;

class ManuscriptWorkflowService
{
    /**
     * Submit a manuscript for review
     */
    public function submit(Manuscript $manuscript): bool
    {
        if ($manuscript->status !== Manuscript::STATUS_DRAFT) {
            return false;
        }

        return DB::transaction(function () use ($manuscript) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_SUBMITTED;
            $manuscript->submitted_at = now();
            $manuscript->save();

            // Create initial revision snapshot
            $this->createRevisionSnapshot($manuscript, 1);

            ManuscriptActivity::log(
                $manuscript,
                'submitted',
                $fromStatus,
                Manuscript::STATUS_SUBMITTED,
                'Manuscript submitted for review'
            );

            return true;
        });
    }

    /**
     * Assign an editor to the manuscript
     */
    public function assignEditor(Manuscript $manuscript, int $editorId): bool
    {
        if (!in_array($manuscript->status, [Manuscript::STATUS_SUBMITTED, Manuscript::STATUS_EDITOR_REVIEW])) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $editorId) {
            $fromStatus = $manuscript->status;
            $manuscript->current_editor_id = $editorId;
            $manuscript->status = Manuscript::STATUS_EDITOR_REVIEW;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'assigned_editor',
                $fromStatus,
                Manuscript::STATUS_EDITOR_REVIEW,
                'Editor assigned to manuscript'
            );

            return true;
        });
    }

    /**
     * Send manuscript for peer review
     */
    public function sendForReview(Manuscript $manuscript): bool
    {
        if ($manuscript->status !== Manuscript::STATUS_EDITOR_REVIEW) {
            return false;
        }

        return DB::transaction(function () use ($manuscript) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_UNDER_REVIEW;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'sent_for_review',
                $fromStatus,
                Manuscript::STATUS_UNDER_REVIEW,
                'Manuscript sent for peer review'
            );

            return true;
        });
    }

    /**
     * Request revision from author
     */
    public function requestRevision(Manuscript $manuscript, string $type = 'major'): bool
    {
        if (!in_array($manuscript->status, [Manuscript::STATUS_UNDER_REVIEW, Manuscript::STATUS_REVISION_SUBMITTED])) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $type) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_REVISION_REQUIRED;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'revision_requested',
                $fromStatus,
                Manuscript::STATUS_REVISION_REQUIRED,
                "Revision requested: {$type}"
            );

            return true;
        });
    }

    /**
     * Submit revision by author
     */
    public function submitRevision(Manuscript $manuscript, array $data): bool
    {
        if ($manuscript->status !== Manuscript::STATUS_REVISION_REQUIRED) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $data) {
            $fromStatus = $manuscript->status;
            
            // Get next version number
            $nextVersion = $manuscript->revisions()->count() + 1;
            
            // Create new revision with metadata snapshot
            $this->createRevisionSnapshot($manuscript, $nextVersion, $data);

            // Update manuscript with new data if provided
            if (isset($data['title'])) {
                $manuscript->title = $data['title'];
            }
            if (isset($data['abstract'])) {
                $manuscript->abstract = $data['abstract'];
            }
            if (isset($data['file_path'])) {
                $manuscript->manuscript_file = $data['file_path'];
            }

            $manuscript->status = Manuscript::STATUS_REVISION_SUBMITTED;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'revision_submitted',
                $fromStatus,
                Manuscript::STATUS_REVISION_SUBMITTED,
                "Revision v{$nextVersion} submitted"
            );

            return true;
        });
    }

    /**
     * Accept manuscript for publication
     */
    public function accept(Manuscript $manuscript): bool
    {
        if (!in_array($manuscript->status, [Manuscript::STATUS_UNDER_REVIEW, Manuscript::STATUS_REVISION_SUBMITTED])) {
            return false;
        }

        return DB::transaction(function () use ($manuscript) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_COPYEDITING;
            $manuscript->accepted_at = now();
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'accepted',
                $fromStatus,
                Manuscript::STATUS_COPYEDITING,
                'Manuscript accepted for publication'
            );

            return true;
        });
    }

    /**
     * Move to production
     */
    public function moveToProduction(Manuscript $manuscript): bool
    {
        if ($manuscript->status !== Manuscript::STATUS_COPYEDITING) {
            return false;
        }

        return DB::transaction(function () use ($manuscript) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_PRODUCTION;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'production',
                $fromStatus,
                Manuscript::STATUS_PRODUCTION,
                'Manuscript moved to production'
            );

            return true;
        });
    }

    /**
     * Publish manuscript
     */
    public function publish(Manuscript $manuscript, ?int $issueId = null): bool
    {
        if ($manuscript->status !== Manuscript::STATUS_PRODUCTION) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $issueId) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_PUBLISHED;
            $manuscript->published_at = now();
            
            if ($issueId) {
                $manuscript->issue_id = $issueId;
            }
            
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'published',
                $fromStatus,
                Manuscript::STATUS_PUBLISHED,
                'Manuscript published'
            );

            return true;
        });
    }

    /**
     * Reject manuscript
     */
    public function reject(Manuscript $manuscript, string $reason = ''): bool
    {
        $allowedStatuses = [
            Manuscript::STATUS_SUBMITTED,
            Manuscript::STATUS_EDITOR_REVIEW,
            Manuscript::STATUS_UNDER_REVIEW,
            Manuscript::STATUS_REVISION_SUBMITTED,
        ];

        if (!in_array($manuscript->status, $allowedStatuses)) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $reason) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_REJECTED;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'rejected',
                $fromStatus,
                Manuscript::STATUS_REJECTED,
                $reason ?: 'Manuscript rejected'
            );

            return true;
        });
    }

    /**
     * Withdraw manuscript by author
     */
    public function withdraw(Manuscript $manuscript, string $reason = ''): bool
    {
        $allowedStatuses = [
            Manuscript::STATUS_SUBMITTED,
            Manuscript::STATUS_EDITOR_REVIEW,
            Manuscript::STATUS_UNDER_REVIEW,
            Manuscript::STATUS_REVISION_REQUIRED,
        ];

        if (!in_array($manuscript->status, $allowedStatuses)) {
            return false;
        }

        return DB::transaction(function () use ($manuscript, $reason) {
            $fromStatus = $manuscript->status;
            $manuscript->status = Manuscript::STATUS_WITHDRAWN;
            $manuscript->save();

            ManuscriptActivity::log(
                $manuscript,
                'withdrawn',
                $fromStatus,
                Manuscript::STATUS_WITHDRAWN,
                $reason ?: 'Manuscript withdrawn by author'
            );

            return true;
        });
    }

    /**
     * Create a revision snapshot with metadata
     */
    private function createRevisionSnapshot(Manuscript $manuscript, int $version, array $data = []): ManuscriptRevision
    {
        return ManuscriptRevision::create([
            'manuscript_id' => $manuscript->id,
            'version' => $version,
            'title_snapshot' => $data['title'] ?? $manuscript->title,
            'abstract_snapshot' => $data['abstract'] ?? $manuscript->abstract,
            'keywords_snapshot' => $data['keywords'] ?? $manuscript->keywords,
            'file_path' => $data['file_path'] ?? $manuscript->manuscript_file,
            'author_notes' => $data['author_notes'] ?? null,
            'status' => 'submitted',
            'uploaded_by' => auth()->id() ?? $manuscript->submitter_id,
        ]);
    }
}
