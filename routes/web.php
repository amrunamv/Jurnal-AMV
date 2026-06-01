<?php

use App\Http\Controllers\Api\OaiPmhController;
use App\Http\Controllers\Api\RssFeedController;
use App\Http\Controllers\Api\SitemapController;
use App\Livewire\ArticleDiscovery;
use App\Livewire\ArticleView;
use App\Livewire\ReviewerInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Localization Route
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Articles
Route::get('/articles', ArticleDiscovery::class)->name('articles.index');
Route::get('/articles/{slug}', ArticleView::class)->name('articles.show');
Route::get('/articles/{slug}/download', function (string $slug) {
    $manuscript = \App\Models\Manuscript::where('slug', $slug)->firstOrFail();
    $manuscript->increment('download_count');
    return response()->download(storage_path('app/manuscripts/' . $manuscript->uuid . '.pdf'), $manuscript->slug . '.pdf');
})->name('articles.download')->middleware('manuscript.access');

// Journals
Route::get('/journals', function () {
    return view('journals.index', [
        'journals' => \App\Models\Journal::where('is_active', true)
            ->withCount(['manuscripts' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get()
    ]);
})->name('journals.index');

Route::get('/journals/{slug}', function (string $slug) {
    $journal = \App\Models\Journal::where('slug', $slug)->with(['volumes.issues'])->firstOrFail();
    $articles = \App\Models\Manuscript::published()
        ->where('journal_id', $journal->id)
        ->with(['contributors'])
        ->latest('published_at')
        ->paginate(12);
    return view('journals.show', compact('journal', 'articles'));
})->name('journals.show');
Route::get('/journals/{slug}/template', function (string $slug) {
    $journal = \App\Models\Journal::where('slug', $slug)->firstOrFail();
    $media = $journal->getFirstMedia('research_template');
    
    if (!$media) {
        return redirect()->back()->with('error', 'No template available for this journal.');
    }

    return $media;
})->name('journals.template');

Route::get('/template/download', function () {
    // 1. Check template from Settings (uploaded via ManageJournalSettings)
    $templatePath = \App\Models\Setting::get('research_template');
    if ($templatePath) {
        $fullPath = storage_path('app/public/' . $templatePath);
        if (file_exists($fullPath)) {
            return response()->download($fullPath, 'Template-Riset-AMV.docx');
        }
    }

    // 2. Fallback: Check from Journal media library (Spatie)
    $journal = \App\Models\Journal::where('is_active', true)->first();
    if ($journal) {
        $media = $journal->getFirstMedia('research_template');
        if ($media) {
            return $media;
        }
    }

    // 3. No template available
    return redirect()->back()->with('error', 'Templat belum tersedia. Silakan hubungi administrator.');
})->name('template.default.download');

// Static pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/submission-guidelines', 'pages.submission-guidelines')->name('submission.guidelines');
Route::view('/panduan-pengajuan', 'pages.submission-guidelines')->name('submission.guidelines.id');

// Announcements
Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{slug}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');

// Authors
Route::get('/authors', [App\Http\Controllers\AuthorController::class, 'index'])->name('authors.index');

// Archives
Route::get('/archives', [App\Http\Controllers\ArchiveController::class, 'index'])->name('archives.index');
Route::get('/archives/{issue}', [App\Http\Controllers\ArchiveController::class, 'show'])->name('archives.show');

// Reviewer interface (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/reviews/{review}', ReviewerInterface::class)->name('reviews.show');
    Route::get('/reviews/{review}/pdf', function (\App\Models\Review $review) {
        abort_unless(\Illuminate\Support\Facades\Auth::id() === $review->reviewer_id, 403);
        return response()->file(storage_path('app/manuscripts/' . $review->manuscript->uuid . '_anonymous.pdf'));
    })->name('reviews.pdf');
});

// ORCID Auth (Publicly accessible to support login/registration)
Route::get('/auth/orcid/redirect', [App\Http\Controllers\Auth\OrcidAuthController::class, 'redirect'])->name('orcid.redirect');
Route::get('/auth/orcid/callback', [App\Http\Controllers\Auth\OrcidAuthController::class, 'callback'])->name('orcid.callback');

// Fallback login route for standard auth middleware
Route::get('/login', function () {
    return redirect()->route('filament.console.auth.login');
})->name('login');

// Sitemap routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-journals.xml', [SitemapController::class, 'journals'])->name('sitemap.journals');
Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('sitemap.articles');

// RSS and Atom feeds
Route::get('/rss.xml', [RssFeedController::class, 'index'])->name('rss');
Route::get('/feed/{journal}', [RssFeedController::class, 'journal'])->name('rss.journal');
Route::get('/atom.xml', [RssFeedController::class, 'atom'])->name('atom');

// OAI-PMH endpoint
Route::get('/oai', [OaiPmhController::class, 'handle'])->name('oai');


