<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Ensure role exists before filtering to avoid Spatie exceptions
        if (\Spatie\Permission\Models\Role::where('name', 'author')->exists()) {
            $query->role('author');
        } else {
            // Return empty if role doesn't exist
            $query->whereRaw('1 = 0');
        }
        
        $query->with('affiliation');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('research_interests', 'like', "%{$search}%");
            });
        }

        // Sort by H-Index Scopus by default
        $authors = $query->orderByDesc('h_index_scopus')
                         ->paginate(12);

        return view('authors.index', compact('authors'));
    }
}
