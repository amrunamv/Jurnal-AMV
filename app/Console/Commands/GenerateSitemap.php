<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for the application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $baseUrl = config('app.url');
        $today = now()->format('Y-m-d');

        // Generate sitemap index
        $sitemapIndex = $this->generateSitemapIndex($baseUrl, $today);
        file_put_contents(public_path('sitemap.xml'), $sitemapIndex);

        // Generate pages sitemap
        $pagesSitemap = $this->generatePagesSitemap($baseUrl, $today);
        file_put_contents(public_path('sitemap-pages.xml'), $pagesSitemap);

        // Generate journals sitemap
        $journalsSitemap = $this->generateJournalsSitemap($baseUrl);
        file_put_contents(public_path('sitemap-journals.xml'), $journalsSitemap);

        // Generate articles sitemap
        $articlesSitemap = $this->generateArticlesSitemap($baseUrl);
        file_put_contents(public_path('sitemap-articles.xml'), $articlesSitemap);

        $this->info('Sitemap generated successfully!');
    }

    protected function generateSitemapIndex(string $baseUrl, string $today): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{$baseUrl}/sitemap-pages.xml</loc>
        <lastmod>{$today}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{$baseUrl}/sitemap-journals.xml</loc>
        <lastmod>{$today}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{$baseUrl}/sitemap-articles.xml</loc>
        <lastmod>{$today}</lastmod>
    </sitemap>
</sitemapindex>
XML;
    }

    protected function generatePagesSitemap(string $baseUrl, string $today): string
    {
        $urls = <<<XML
        <url>
            <loc>{$baseUrl}</loc>
            <lastmod>{$today}</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.0</priority>
        </url>
        <url>
            <loc>{$baseUrl}/about</loc>
            <lastmod>{$today}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
        <url>
            <loc>{$baseUrl}/submission-guidelines</loc>
            <lastmod>{$today}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
        <url>
            <loc>{$baseUrl}/contact</loc>
            <lastmod>{$today}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
        </url>
XML;

        return $this->wrapUrlset($urls);
    }

    protected function generateJournalsSitemap(string $baseUrl): string
    {
        $journals = \App\Models\Journal::where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->get();

        $urls = '';
        foreach ($journals as $journal) {
            $loc = $baseUrl . '/journals/' . $journal->slug;
            $lastmod = $journal->updated_at->format('Y-m-d');
            
            $urls .= <<<XML
        <url>
            <loc>{$loc}</loc>
            <lastmod>{$lastmod}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
XML;
        }

        return $this->wrapUrlset($urls);
    }

    protected function generateArticlesSitemap(string $baseUrl): string
    {
        $manuscripts = \App\Models\Manuscript::published()
            ->orderBy('published_at', 'desc')
            ->select(['slug', 'published_at', 'updated_at'])
            ->get();

        $urls = '';
        foreach ($manuscripts as $manuscript) {
            $loc = $baseUrl . '/articles/' . $manuscript->slug;
            $lastmod = ($manuscript->updated_at ?? $manuscript->published_at)->format('Y-m-d');
            
            $urls .= <<<XML
        <url>
            <loc>{$loc}</loc>
            <lastmod>{$lastmod}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
XML;
        }

        return $this->wrapUrlset($urls);
    }

    protected function wrapUrlset(string $urls): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {$urls}
</urlset>
XML;
    }
}
