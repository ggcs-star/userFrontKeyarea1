<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Project;

class StateController extends Controller
{

public function index()
{
    $state = State::where('name', 'Gujarat')->first();

    if (!$state) {
        return response()->json([
            'success' => false,
            'message' => 'State not found'
        ], 404);
    }

    $ahmedabad = collect($state->cities)
        ->firstWhere('name', 'Ahmedabad');

    if (!$ahmedabad) {
        return response()->json([
            'success' => false,
            'message' => 'Ahmedabad not found'
        ], 404);
    }

    $areas = collect($ahmedabad['areas'])->map(function ($area) {
        $projectCount = Project::where('project.location.city', 'Ahmedabad')
            ->where('project.location.area', $area['name'])
            ->count();

        return [
            'name' => $area['name'],
            'img'  => asset($area['img']),
            'total_projects'=> $projectCount
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'state' => $state->name,
            'city'  => $ahmedabad['name'],
            'areas' => $areas
        ]
    ]);
}



    public function show($id)
    {
        $state = State::find($id);

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'State not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $state
        ]);
    }
}
