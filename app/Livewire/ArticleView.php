<?php

namespace App\Livewire;

use App\Models\Manuscript;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ArticleView extends Component
{
    public Manuscript $article;
    public string $citationFormat = 'apa';
    public bool $showCitationModal = false;

    public function mount(string $slug): void
    {
        $this->article = Manuscript::where('slug', $slug)
            ->published()
            ->with(['journal', 'contributors.affiliation', 'citations', 'issue.volume'])
            ->firstOrFail();

        // Increment view count
        $this->article->increment('view_count');
    }

    public function downloadPdf(): void
    {
        $this->article->increment('download_count');
        
        // Redirect to PDF download
        $this->dispatch('download', url: route('articles.download', $this->article->slug));
    }

    public function toggleCitationModal(): void
    {
        $this->showCitationModal = !$this->showCitationModal;
    }

    public function getCitation(): string
    {
        return match ($this->citationFormat) {
            'apa' => $this->getApaCitation(),
            'mla' => $this->getMlaCitation(),
            'harvard' => $this->getHarvardCitation(),
            'chicago' => $this->getChicagoCitation(),
            'bibtex' => $this->getBibtexCitation(),
            default => $this->getApaCitation(),
        };
    }

    protected function getApaCitation(): string
    {
        $authors = $this->formatAuthorsApa();
        $year = $this->article->published_at?->year ?? date('Y');
        $title = $this->article->title;
        $journal = $this->article->journal->name;
        $volume = $this->article->issue?->volume?->number ?? '';
        $issue = $this->article->issue?->number ?? '';
        $pages = $this->article->page_range ?? '';
        $doi = $this->article->doi;

        $citation = "{$authors} ({$year}). {$title}. *{$journal}*";
        
        if ($volume) {
            $citation .= ", *{$volume}*";
            if ($issue) {
                $citation .= "({$issue})";
            }
        }
        
        if ($pages) {
            $citation .= ", {$pages}";
        }
        
        $citation .= ".";
        
        if ($doi) {
            $citation .= " https://doi.org/{$doi}";
        }

        return $citation;
    }

    protected function getMlaCitation(): string
    {
        $authors = $this->formatAuthorsMla();
        $title = "\"{$this->article->title}.\"";
        $journal = "*{$this->article->journal->name}*";
        $volume = $this->article->issue?->volume?->number ?? '';
        $issue = $this->article->issue?->number ?? '';
        $year = $this->article->published_at?->year ?? date('Y');
        $pages = $this->article->page_range ?? '';
        $doi = $this->article->doi;

        $citation = "{$authors} {$title} {$journal}";
        
        if ($volume) {
            $citation .= ", vol. {$volume}";
            if ($issue) {
                $citation .= ", no. {$issue}";
            }
        }
        
        $citation .= ", {$year}";
        
        if ($pages) {
            $citation .= ", pp. {$pages}";
        }
        
        $citation .= ".";
        
        if ($doi) {
            $citation .= " DOI: {$doi}.";
        }

        return $citation;
    }

    protected function getHarvardCitation(): string
    {
        $authors = $this->formatAuthorsHarvard();
        $year = $this->article->published_at?->year ?? date('Y');
        $title = "'{$this->article->title}'";
        $journal = "*{$this->article->journal->name}*";
        $volume = $this->article->issue?->volume?->number ?? '';
        $issue = $this->article->issue?->number ?? '';
        $pages = $this->article->page_range ?? '';
        $doi = $this->article->doi;

        $citation = "{$authors} ({$year}) {$title}, {$journal}";
        
        if ($volume) {
            $citation .= ", {$volume}";
            if ($issue) {
                $citation .= "({$issue})";
            }
        }
        
        if ($pages) {
            $citation .= ", pp. {$pages}";
        }
        
        if ($doi) {
            $citation .= ". Available at: https://doi.org/{$doi}";
        }

        return $citation . ".";
    }

    protected function getChicagoCitation(): string
    {
        $authors = $this->formatAuthorsChicago();
        $title = "\"{$this->article->title}.\"";
        $journal = "*{$this->article->journal->name}*";
        $volume = $this->article->issue?->volume?->number ?? '';
        $issue = $this->article->issue?->number ?? '';
        $year = $this->article->published_at?->year ?? date('Y');
        $pages = $this->article->page_range ?? '';
        $doi = $this->article->doi;

        $citation = "{$authors} {$title} {$journal}";
        
        if ($volume) {
            $citation .= " {$volume}";
            if ($issue) {
                $citation .= ", no. {$issue}";
            }
        }
        
        $citation .= " ({$year})";
        
        if ($pages) {
            $citation .= ": {$pages}";
        }
        
        $citation .= ".";
        
        if ($doi) {
            $citation .= " https://doi.org/{$doi}.";
        }

        return $citation;
    }

    protected function getBibtexCitation(): string
    {
        $firstAuthor = $this->article->contributors->first();
        $key = strtolower($firstAuthor?->family_name ?? 'unknown') . ($this->article->published_at?->year ?? date('Y'));
        $authors = $this->article->contributors->map(fn($c) => "{$c->family_name}, {$c->given_name}")->join(' and ');
        
        return "@article{{$key},
  author = {{$authors}},
  title = {{{$this->article->title}}},
  journal = {{{$this->article->journal->name}}},
  year = {" . ($this->article->published_at?->year ?? date('Y')) . "},
  volume = {" . ($this->article->issue?->volume?->number ?? '') . "},
  number = {" . ($this->article->issue?->number ?? '') . "},
  pages = {" . ($this->article->page_range ?? '') . "},
  doi = {" . ($this->article->doi ?? '') . "}
}";
    }

    protected function formatAuthorsApa(): string
    {
        $contributors = $this->article->contributors;
        
        if ($contributors->count() === 1) {
            $c = $contributors->first();
            return "{$c->family_name}, " . substr($c->given_name, 0, 1) . ".";
        }
        
        if ($contributors->count() === 2) {
            return $contributors->map(fn($c) => "{$c->family_name}, " . substr($c->given_name, 0, 1) . ".")->join(' & ');
        }
        
        if ($contributors->count() > 7) {
            $first6 = $contributors->take(6)->map(fn($c) => "{$c->family_name}, " . substr($c->given_name, 0, 1) . ".")->join(', ');
            $last = $contributors->last();
            return "{$first6}, ... {$last->family_name}, " . substr($last->given_name, 0, 1) . ".";
        }
        
        $allButLast = $contributors->slice(0, -1)->map(fn($c) => "{$c->family_name}, " . substr($c->given_name, 0, 1) . ".")->join(', ');
        $last = $contributors->last();
        return "{$allButLast}, & {$last->family_name}, " . substr($last->given_name, 0, 1) . ".";
    }

    protected function formatAuthorsMla(): string
    {
        $contributors = $this->article->contributors;
        
        if ($contributors->count() === 1) {
            $c = $contributors->first();
            return "{$c->family_name}, {$c->given_name}.";
        }
        
        if ($contributors->count() === 2) {
            $first = $contributors->first();
            $second = $contributors->last();
            return "{$first->family_name}, {$first->given_name}, and {$second->given_name} {$second->family_name}.";
        }
        
        $first = $contributors->first();
        return "{$first->family_name}, {$first->given_name}, et al.";
    }

    protected function formatAuthorsHarvard(): string
    {
        return $this->formatAuthorsApa();
    }

    protected function formatAuthorsChicago(): string
    {
        $contributors = $this->article->contributors;
        
        if ($contributors->count() === 1) {
            $c = $contributors->first();
            return "{$c->family_name}, {$c->given_name}.";
        }
        
        $first = $contributors->first();
        $rest = $contributors->slice(1)->map(fn($c) => "{$c->given_name} {$c->family_name}")->join(', ');
        return "{$first->family_name}, {$first->given_name}, {$rest}.";
    }

    #[Layout('components.layouts.app')]
    public function render(\App\Services\SeoService $seoService)
    {
        /** @var \Livewire\Features\SupportPageComponents\View $view */
        $view = view('livewire.article-view', [
            'citation' => $this->getCitation(),
        ]);

        return $view->layoutData([
            'head' => $seoService->getHeadMarkup($this->article),
        ]);
    }
}
