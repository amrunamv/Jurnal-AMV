<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::active()
            ->orderBy('is_active', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('announcements.index', compact('announcements'));
    }

    public function show($slug)
    {
        $announcement = Announcement::where('slug', $slug)
            ->active()
            ->firstOrFail();

        return view('announcements.show', compact('announcement'));
    }
}
