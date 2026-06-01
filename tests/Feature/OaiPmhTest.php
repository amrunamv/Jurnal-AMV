<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OaiPmhTest extends TestCase
{
    use RefreshDatabase;

    public function test_oai_identify_verb_returns_valid_xml()
    {
        $response = $this->get(route('oai', ['verb' => 'Identify']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $this->assertStringContainsString('<repositoryName>AMV Open Science</repositoryName>', $response->getContent());
    }

    public function test_oai_list_metadata_formats_returns_oai_dc()
    {
        $response = $this->get(route('oai', ['verb' => 'ListMetadataFormats']));

        $response->assertStatus(200);
        $this->assertStringContainsString('<metadataPrefix>oai_dc</metadataPrefix>', $response->getContent());
    }
}

