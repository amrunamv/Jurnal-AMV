<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\User;
use App\Services\ManuscriptWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManuscriptWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_manuscript_can_transition_through_states()
    {
        $manuscript = Manuscript::factory()->create([
            'status' => Manuscript::STATUS_DRAFT
        ]);

        $this->assertTrue($manuscript->canTransitionTo(Manuscript::STATUS_SUBMITTED));
        
        $manuscript->transitionTo(Manuscript::STATUS_SUBMITTED);
        $this->assertEquals(Manuscript::STATUS_SUBMITTED, $manuscript->status);

        $this->assertTrue($manuscript->canTransitionTo(Manuscript::STATUS_EDITOR_REVIEW));
    }

    public function test_forbidden_transitions_are_blocked()
    {
        $manuscript = Manuscript::factory()->create([
            'status' => Manuscript::STATUS_DRAFT
        ]);

        $this->assertFalse($manuscript->canTransitionTo(Manuscript::STATUS_PUBLISHED));
    }
}
