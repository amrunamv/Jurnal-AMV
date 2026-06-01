<?php

namespace App\Http\Middleware;

use App\Models\Manuscript;
use App\Models\Review;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckManuscriptAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        
        if (!$slug) {
            return $next($request);
        }

        $manuscript = Manuscript::where('slug', $slug)->firstOrFail();

        // 1. If published, everyone can access
        if ($manuscript->status === Manuscript::STATUS_PUBLISHED) {
            return $next($request);
        }

        // 2. Check for authenticated user
        if (!Auth::check()) {
            abort(403, 'Unauthorized access to unpublished manuscript.');
        }

        $user = Auth::user();

        // 3. Super Admin has full access
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // 4. Submitter (Author) has access
        if ($manuscript->submitter_id === $user->id) {
            return $next($request);
        }

        // 5. Assigned Editor has access
        if ($manuscript->current_editor_id === $user->id) {
            return $next($request);
        }

        // 6. Assigned Reviewer has access
        $isReviewer = Review::where('manuscript_id', $manuscript->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($isReviewer) {
            return $next($request);
        }

        // 7. General Editor role also often has access to all manuscripts
        if ($user->hasRole('editor')) {
            return $next($request);
        }

        return response('You do not have permission to access this manuscript.', 403);
    }
}


