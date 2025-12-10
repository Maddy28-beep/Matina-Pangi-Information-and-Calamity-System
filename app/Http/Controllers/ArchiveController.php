<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Archive::with('archivedBy')->latest('archived_at');

        // Filter by module type
        if ($request->filled('module_type')) {
            $query->where('module_type', $request->module_type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $archives = $query->paginate(20);

        // Get unique module types for filter
        $moduleTypes = Archive::select('module_type')
            ->distinct()
            ->orderBy('module_type')
            ->pluck('module_type');

        return view('archives.index', compact('archives', 'moduleTypes'));
    }

    public function show(Archive $archive)
    {
        $archive->load('archivedBy');

        return view('archives.show', compact('archive'));
    }

    public function restore(Archive $archive)
    {
        // Logic to restore the archived item
        // This would depend on the module type

        return redirect()->route('archives.index')->with('success', 'Item restored successfully');
    }

    public function destroy(Archive $archive)
    {
        $archive->delete();

        return redirect()->route('archives.index')->with('success', 'Archive deleted permanently');
    }
}
