<?php

namespace App\Livewire;

use App\Models\Journal;
use App\Models\Manuscript;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ArticleDiscovery extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $journalId = null;

    #[Url]
    public ?int $year = null;

    #[Url]
    public string $sortBy = 'latest';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingJournalId(): void
    {
        $this->resetPage();
    }

    public function updatingYear(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->journalId = null;
        $this->year = null;
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Manuscript::published()
            ->with(['journal:id,name,slug', 'contributors:id,manuscript_id,given_name,family_name,is_corresponding']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('abstract', 'like', "%{$this->search}%")
                  ->orWhereHas('contributors', function ($cq) {
                      $cq->where('given_name', 'like', "%{$this->search}%")
                         ->orWhere('family_name', 'like', "%{$this->search}%");
                  });
            });
        }

        // Journal filter
        if ($this->journalId) {
            $query->where('journal_id', $this->journalId);
        }

        // Year filter
        if ($this->year) {
            $query->whereYear('published_at', $this->year);
        }

        // Sorting
        $query = match ($this->sortBy) {
            'oldest' => $query->orderBy('published_at', 'asc'),
            'most_viewed' => $query->orderBy('view_count', 'desc'),
            'most_downloaded' => $query->orderBy('download_count', 'desc'),
            default => $query->orderBy('published_at', 'desc'),
        };

        $articles = $query->paginate(12);

        // Get filter options
        $journals = Journal::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $years = Manuscript::published()
            ->selectRaw('YEAR(published_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('livewire.article-discovery', [
            'articles' => $articles,
            'journals' => $journals,
            'years' => $years,
        ]);
    }
}
