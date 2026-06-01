<?php

namespace App\Http\Controllers\Api;

use App\Models\Manuscript;
use App\Models\Journal;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap.xml
     */
    public function index(): Response
    {
        $content = $this->generateSitemapIndex();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Generate sitemap for articles
     */
    public function articles(): Response
    {
        $manuscripts = Manuscript::published()
            ->orderBy('published_at', 'desc')
            ->select(['slug', 'published_at', 'updated_at'])
            ->get();

        $urls = '';
        foreach ($manuscripts as $manuscript) {
            $loc = config('app.url') . '/articles/' . $manuscript->slug;
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

        return $this->sitemapResponse($urls);
    }

    /**
     * Generate sitemap for journals
     */
    public function journals(): Response
    {
        $journals = Journal::where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->get();

        $urls = '';
        foreach ($journals as $journal) {
            $loc = config('app.url') . '/journals/' . $journal->slug;
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

        return $this->sitemapResponse($urls);
    }

    /**
     * Generate sitemap for static pages
     */
    public function pages(): Response
    {
        $baseUrl = config('app.url');
        $today = now()->format('Y-m-d');

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

        return $this->sitemapResponse($urls);
    }

    /**
     * Generate sitemap index
     */
    protected function generateSitemapIndex(): string
    {
        $baseUrl = config('app.url');
        $today = now()->format('Y-m-d');

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

    /**
     * Generate sitemap response
     */
    protected function sitemapResponse(string $urls): Response
    {
        $content = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            {$urls}
        </urlset>
        XML;

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
