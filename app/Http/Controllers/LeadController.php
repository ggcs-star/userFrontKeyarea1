<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Project;

class LeadController extends Controller
{
   public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'         => 'required|string|exists:projects,_id', 
            'name'               => 'required|string|max:100',
            'email'              => 'nullable|email',
            'mobile'             => 'required|string|min:10|max:15',
            'looking_for'        => 'required|array',
            'looking_for.*'      => 'string',
            'preferred_bedrooms' => 'required|array',
            'preferred_bedrooms.*'=> 'string',
            'consent'            => 'required|boolean',
        ]);

        $project = Project::find($validated['project_id']);
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Project ID'
            ], 404);
        }

        $lead = Lead::create([
            'project_id'         => $validated['project_id'],
            'name'               => $validated['name'],
            'email'              => $validated['email'] ?? null,
            'mobile'             => $validated['mobile'],
            'looking_for'        => $validated['looking_for'],
            'preferred_bedrooms' => $validated['preferred_bedrooms'],
            'consent'            => $validated['consent'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead submitted successfully!',
            'data'    => $lead
        ]);
    }
}
