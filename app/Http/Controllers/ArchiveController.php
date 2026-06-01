<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Volume;
use App\Models\Journal;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        $volumes = Volume::with(['issues' => function ($query) {
                $query->where('is_published', true)
                      ->orderBy('issue_number', 'desc');
            }])
            ->whereHas('issues', function ($query) {
                $query->where('is_published', true);
            })
            ->orderBy('year', 'desc')
            ->orderBy('volume_number', 'desc')
            ->get();

        return view('archives.index', compact('volumes'));
    }

    public function show(Issue $issue)
    {
        $issue->load(['volume', 'journal', 'manuscripts' => function ($query) {
            $query->published()->with('contributors');
        }]);

        if (!$issue->is_published) {
            abort(404);
        }

        return view('archives.show', compact('issue'));
    }
}
