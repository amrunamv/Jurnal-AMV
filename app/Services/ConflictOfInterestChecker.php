<?php

namespace App\Services;

use App\Models\Contributor;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ConflictOfInterestChecker
{
    /**
     * Check if a reviewer has conflict of interest with the manuscript
     */
    public function hasConflict(User $reviewer, Manuscript $manuscript): bool
    {
        // Get all contributor affiliations for this manuscript
        $authorAffiliationIds = $this->getManuscriptAffiliationIds($manuscript);
        
        // Check if reviewer is from the same institution
        if ($reviewer->affiliation_id && in_array($reviewer->affiliation_id, $authorAffiliationIds)) {
            return true;
        }

        // Check if reviewer is one of the contributors
        $contributorEmails = $manuscript->contributors->pluck('email')->toArray();
        if (in_array($reviewer->email, $contributorEmails)) {
            return true;
        }

        // Check if reviewer is the submitter
        if ($reviewer->id === $manuscript->submitter_id) {
            return true;
        }

        return false;
    }

    /**
     * Get eligible reviewers for a manuscript (no conflict of interest)
     */
    public function getEligibleReviewers(Manuscript $manuscript, int $limit = 10): Collection
    {
        $authorAffiliationIds = $this->getManuscriptAffiliationIds($manuscript);
        $contributorEmails = $manuscript->contributors->pluck('email')->toArray();
        $contributorUserIds = $manuscript->contributors->pluck('user_id')->filter()->toArray();

        return User::role('reviewer')
            ->where('id', '!=', $manuscript->submitter_id)
            ->whereNotIn('id', $contributorUserIds)
            ->whereNotIn('email', $contributorEmails)
            ->where(function ($query) use ($authorAffiliationIds) {
                $query->whereNull('affiliation_id')
                    ->orWhereNotIn('affiliation_id', $authorAffiliationIds);
            })
            // Exclude reviewers who already reviewed this manuscript
            ->whereDoesntHave('reviews', function ($query) use ($manuscript) {
                $query->where('manuscript_id', $manuscript->id);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Get all affiliation IDs associated with the manuscript
     */
    private function getManuscriptAffiliationIds(Manuscript $manuscript): array
    {
        return $manuscript->contributors
            ->pluck('affiliation_id')
            ->filter()
            ->unique()
            ->toArray();
    }

    /**
     * Generate a conflict report for audit purposes
     */
    public function generateConflictReport(User $reviewer, Manuscript $manuscript): array
    {
        $conflicts = [];

        // Check institution conflict
        $authorAffiliationIds = $this->getManuscriptAffiliationIds($manuscript);
        if ($reviewer->affiliation_id && in_array($reviewer->affiliation_id, $authorAffiliationIds)) {
            $conflicts[] = [
                'type' => 'institution',
                'description' => 'Reviewer belongs to the same institution as one of the authors',
                'affiliation_id' => $reviewer->affiliation_id,
            ];
        }

        // Check if reviewer is contributor
        $contributorEmails = $manuscript->contributors->pluck('email')->toArray();
        if (in_array($reviewer->email, $contributorEmails)) {
            $conflicts[] = [
                'type' => 'contributor',
                'description' => 'Reviewer is listed as a contributor on this manuscript',
            ];
        }

        // Check if reviewer is submitter
        if ($reviewer->id === $manuscript->submitter_id) {
            $conflicts[] = [
                'type' => 'submitter',
                'description' => 'Reviewer is the manuscript submitter',
            ];
        }

        return $conflicts;
    }
}
