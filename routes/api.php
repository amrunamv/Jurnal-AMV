<?php

use App\Http\Controllers\Api\OaiPmhController;
use App\Http\Controllers\Api\RssFeedController;
use App\Http\Controllers\Api\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rate limited API endpoints
Route::middleware(['throttle:api'])->group(function () {
    // OAI-PMH Endpoint (for harvesting by DOAJ, Scopus, etc.)
    Route::get('/oai', [OaiPmhController::class, 'handle'])->name('api.oai');

    // RSS & Sitemap Routes
    Route::get('/rss.xml', [RssFeedController::class, 'index'])->name('api.rss');
    Route::get('/journal/{slug}/rss.xml', [RssFeedController::class, 'journal'])->name('api.rss.journal');
    Route::get('/atom.xml', [RssFeedController::class, 'atom'])->name('api.atom');

    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('api.sitemap');
    Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('api.sitemap.pages');
    Route::get('/sitemap-journals.xml', [SitemapController::class, 'journals'])->name('api.sitemap.journals');
    Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('api.sitemap.articles');
});

// Public API endpoints (higher rate limit)
Route::middleware(['throttle:60,1'])->group(function () {
    // Article API
    Route::prefix('articles')->group(function () {
        Route::get('/', function (Request $request) {
            $query = \App\Models\Manuscript::published()
                ->with(['journal:id,name,slug', 'contributors:id,manuscript_id,given_name,family_name'])
                ->orderBy('published_at', 'desc');

            if ($journalId = $request->input('journal_id')) {
                $query->where('journal_id', $journalId);
            }

            if ($search = $request->input('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('abstract', 'like', "%{$search}%");
                });
            }

            return $query->paginate($request->input('per_page', 15));
        })->name('api.articles.index');

        Route::get('/{slug}', function (string $slug) {
            $manuscript = \App\Models\Manuscript::where('slug', $slug)
                ->published()
                ->with(['journal', 'contributors.affiliation', 'citations'])
                ->firstOrFail();

            return response()->json($manuscript);
        })->name('api.articles.show');
    });

    // Journal API
    Route::prefix('journals')->group(function () {
        Route::get('/', function () {
            return \App\Models\Journal::where('is_active', true)
                ->withCount(['manuscripts' => fn($q) => $q->published()])
                ->get();
        })->name('api.journals.index');

        Route::get('/{slug}', function (string $slug) {
            return \App\Models\Journal::where('slug', $slug)
                ->with(['volumes.issues'])
                ->firstOrFail();
        })->name('api.journals.show');
    });
});

// Search API (with stricter rate limit)
Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/search', function (Request $request) {
        $query = $request->input('q');
        
        if (!$query || strlen($query) < 3) {
            return response()->json(['error' => 'Query must be at least 3 characters'], 422);
        }

        $manuscripts = \App\Models\Manuscript::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('abstract', 'like', "%{$query}%")
                  ->orWhereJsonContains('keywords', $query);
            })
            ->with(['journal:id,name,slug'])
            ->select(['id', 'uuid', 'title', 'slug', 'abstract', 'journal_id', 'published_at', 'doi'])
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'query' => $query,
            'count' => $manuscripts->count(),
            'results' => $manuscripts,
        ]);
    })->name('api.search');
});
