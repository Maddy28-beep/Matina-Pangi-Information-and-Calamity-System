<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\ResponseTeamMemberResource;
use App\Models\ResponseTeamMember;
use Illuminate\Http\Request;

class ResponseTeamMemberController extends Controller
{
    public function index()
    {
        return ResponseTeamMemberResource::collection(ResponseTeamMember::with(['calamity', 'evacuationCenter'])->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'skills' => 'nullable',
            'calamity_id' => 'nullable|exists:calamities,id',
            'evacuation_center_id' => 'nullable|exists:evacuation_centers,id',
            'assignment_notes' => 'nullable|string',
        ]);
        if (isset($data['skills']) && is_string($data['skills'])) {
            $skillsText = $data['skills'];
            $skillsArr = collect(preg_split('/\s*,\s*/', $skillsText, -1, PREG_SPLIT_NO_EMPTY))
                ->filter(fn ($s) => strlen($s) > 0)
                ->values()
                ->toArray();
            $data['skills'] = $skillsArr;
        }
        $member = ResponseTeamMember::create($data);
        if ($request->expectsJson()) {
            return new ResponseTeamMemberResource($member);
        }

        return redirect()->route('web.response-team-members.index')
            ->with('success', 'Response team member added successfully');
    }

    public function show(ResponseTeamMember $response_team_member)
    {
        if (request()->expectsJson()) {
            return new ResponseTeamMemberResource($response_team_member->load(['calamity', 'evacuationCenter']));
        }

        $response_team_member->load(['calamity', 'evacuationCenter', 'rescueOperations']);

        return view('calamity.response_team.show', compact('response_team_member'));
    }

    public function update(Request $request, ResponseTeamMember $response_team_member)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|string|max:100',
            'skills' => 'nullable|array',
            'calamity_id' => 'nullable|exists:calamities,id',
            'evacuation_center_id' => 'nullable|exists:evacuation_centers,id',
            'assignment_notes' => 'nullable|string',
        ]);
        $response_team_member->update($data);

        return new ResponseTeamMemberResource($response_team_member->load(['calamity', 'evacuationCenter']));
    }

    public function destroy(ResponseTeamMember $response_team_member)
    {
        // Use the Archivable trait to archive
        $response_team_member->archive('Archived by '.auth()->user()->name);

        return response()->json(null, 204);
    }
}
