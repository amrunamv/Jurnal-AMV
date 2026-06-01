<?php

namespace App\Http\Controllers\Api;

use App\Models\Manuscript;
use App\Models\Journal;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class RssFeedController extends Controller
{
    /**
     * Generate RSS feed for all articles
     */
    public function index(): Response
    {
        $manuscripts = Manuscript::published()
            ->with(['journal', 'contributors'])
            ->orderBy('published_at', 'desc')
            ->take(50)
            ->get();

        $content = $this->generateFeed(
            'AMV Open Science - Latest Articles',
            'Latest published articles from AMV Open Science',
            config('app.url'),
            $manuscripts
        );

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    /**
     * Generate RSS feed for a specific journal
     */
    public function journal(string $slug): Response
    {
        $journal = Journal::where('slug', $slug)->firstOrFail();

        $manuscripts = Manuscript::published()
            ->where('journal_id', $journal->id)
            ->with(['contributors'])
            ->orderBy('published_at', 'desc')
            ->take(50)
            ->get();

        $content = $this->generateFeed(
            $journal->name . ' - Latest Articles',
            $journal->description ?? "Latest articles from {$journal->name}",
            config('app.url') . '/journals/' . $journal->slug,
            $manuscripts
        );

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    /**
     * Generate Atom feed for all articles
     */
    public function atom(): Response
    {
        $manuscripts = Manuscript::published()
            ->with(['journal', 'contributors'])
            ->orderBy('published_at', 'desc')
            ->take(50)
            ->get();

        $content = $this->generateAtomFeed($manuscripts);

        return response($content, 200)
            ->header('Content-Type', 'application/atom+xml; charset=utf-8');
    }

    /**
     * Generate RSS 2.0 feed
     */
    protected function generateFeed(string $title, string $description, string $link, $manuscripts): string
    {
        $items = '';
        foreach ($manuscripts as $manuscript) {
            $items .= $this->buildRssItem($manuscript);
        }

        $buildDate = now()->format('D, d M Y H:i:s O');
        $title = $this->escapeXml($title);
        $description = $this->escapeXml($description);

        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
            <channel>
                <title>{$title}</title>
                <link>{$link}</link>
                <description>{$description}</description>
                <language>id</language>
                <lastBuildDate>{$buildDate}</lastBuildDate>
                <atom:link href="{$link}/rss.xml" rel="self" type="application/rss+xml"/>
                {$items}
            </channel>
        </rss>
        XML;
    }

    /**
     * Build RSS item
     */
    protected function buildRssItem(Manuscript $manuscript): string
    {
        $title = $this->escapeXml($manuscript->title);
        $link = config('app.url') . '/articles/' . $manuscript->slug;
        $description = $this->escapeXml(substr($manuscript->abstract, 0, 500) . '...');
        $pubDate = $manuscript->published_at->format('D, d M Y H:i:s O');
        $guid = $manuscript->doi 
            ? "https://doi.org/{$manuscript->doi}" 
            : $link;

        // Build authors
        $creators = '';
        foreach ($manuscript->contributors as $contributor) {
            $name = $this->escapeXml($contributor->full_name);
            $creators .= "<dc:creator>{$name}</dc:creator>\n";
        }

        return <<<XML
        <item>
            <title>{$title}</title>
            <link>{$link}</link>
            <description>{$description}</description>
            <pubDate>{$pubDate}</pubDate>
            <guid isPermaLink="true">{$guid}</guid>
            {$creators}
        </item>
        XML;
    }

    /**
     * Generate Atom 1.0 feed
     */
    protected function generateAtomFeed($manuscripts): string
    {
        $entries = '';
        foreach ($manuscripts as $manuscript) {
            $entries .= $this->buildAtomEntry($manuscript);
        }

        $baseUrl = config('app.url');
        $updated = now()->format('Y-m-d\TH:i:s\Z');

        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <feed xmlns="http://www.w3.org/2005/Atom">
            <title>AMV Open Science - Latest Articles</title>
            <link href="{$baseUrl}/atom.xml" rel="self"/>
            <link href="{$baseUrl}"/>
            <id>{$baseUrl}</id>
            <updated>{$updated}</updated>
            {$entries}
        </feed>
        XML;
    }

    /**
     * Build Atom entry
     */
    protected function buildAtomEntry(Manuscript $manuscript): string
    {
        $title = $this->escapeXml($manuscript->title);
        $link = config('app.url') . '/articles/' . $manuscript->slug;
        $summary = $this->escapeXml($manuscript->abstract);
        $updated = $manuscript->updated_at->format('Y-m-d\TH:i:s\Z');
        $id = $manuscript->doi 
            ? "doi:{$manuscript->doi}" 
            : "urn:uuid:{$manuscript->uuid}";

        // Build authors
        $authors = '';
        foreach ($manuscript->contributors as $contributor) {
            $name = $this->escapeXml($contributor->full_name);
            $authors .= <<<XML
            <author>
                <name>{$name}</name>
            </author>
            XML;
        }

        return <<<XML
        <entry>
            <title>{$title}</title>
            <link href="{$link}"/>
            <id>{$id}</id>
            <updated>{$updated}</updated>
            <summary>{$summary}</summary>
            {$authors}
        </entry>
        XML;
    }

    /**
     * Escape XML special characters
     */
    protected function escapeXml($string): string
    {
        return htmlspecialchars((string) $string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
