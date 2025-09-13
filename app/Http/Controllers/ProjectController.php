<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Support\Facades\DB;
class ProjectController extends Controller
{
       protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    public function index(ProjectService $projectService)
{
    $projects = Project::all()
        ->filter(function ($project) use ($projectService) {
            foreach ($project->configuration as $config) {
                if (isset($config['price'])) {
                    $priceInNumber = $projectService->convertPriceToNumber($config['price']);
                    if ($priceInNumber >= 5000000 && $priceInNumber <= 10000000) {
                        return true;
                    }
                }
            }
            return false;
        });

    $formattedProjects = $projectService->formatProjects($projects);

    return response()->json([
        'status' => 'success',
        'message' => $formattedProjects->isEmpty()
            ? 'No projects found in the specified price range.'
            : 'Filtered projects retrieved successfully.',
        'data' => $formattedProjects,
        'meta' => [
            'total' => $formattedProjects->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getProjectsWith2BHK(ProjectService $projectService)
{
    $projects = Project::where('configuration', 'exists', true)->get()
        ->filter(function ($project) {
            foreach ($project->configuration as $configGroup) {
                if (is_array($configGroup) && isset($configGroup['2BHK'])) {
                    return true;
                }
            }
            return false;
        });

    $formatted = $projectService->formatProjects($projects);

    // Add a flag indicating presence of 2BHK in configuration
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['has_2BHK'] = false;

        if (isset($projects[$key]->configuration['2BHK'])) {
            $item['has_2BHK'] = true;
        }

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No projects with 2BHK configuration found.'
            : '2BHK projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getProjectsWith3BHK(ProjectService $projectService)
{
    $projects = Project::where('configuration', 'exists', true)->get()
        ->filter(function ($project) {
            foreach ($project->configuration as $configGroup) {
                if (is_array($configGroup) && isset($configGroup['3BHK'])) {
                    return true;
                }
            }
            return false;
        });

    $formatted = $projectService->formatProjects($projects);

    $formatted->transform(function ($item, $key) use ($projects) {
        $item['has_3BHK'] = false;

        if (isset($projects[$key]->configuration['3BHK'])) {
            $item['has_3BHK'] = true;
        }

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No projects with 3BHK configuration found.'
            : '3BHK projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getProjectsWith4BHK(ProjectService $projectService)
{
    $projects = Project::where('configuration', 'exists', true)->get()
        ->filter(function ($project) {
            foreach ($project->configuration as $configGroup) {
                if (is_array($configGroup) && isset($configGroup['4BHK'])) {
                    return true;
                }
            }
            return false;
        });

    $formatted = $projectService->formatProjects($projects);

    $formatted->transform(function ($item, $key) use ($projects) {
        $item['has_4BHK'] = false;

        if (isset($projects[$key]->configuration['4BHK'])) {
            $item['has_4BHK'] = true;
        }

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No projects with 4BHK configuration found.'
            : '4BHK projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getProjectsWith5BHK(ProjectService $projectService)
{
    $projects = Project::where('configuration', 'exists', true)->get()
        ->filter(function ($project) {
            foreach ($project->configuration as $configGroup) {
                if (is_array($configGroup) && isset($configGroup['5BHK'])) {
                    return true;
                }
            }
            return false;
        });

    $formatted = $projectService->formatProjects($projects);

    $formatted->transform(function ($item, $key) use ($projects) {
        $item['has_5BHK'] = false;

        if (isset($projects[$key]->configuration['5BHK'])) {
            $item['has_5BHK'] = true;
        }

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No projects with 5BHK configuration found.'
            : '5BHK projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getemerging_property(ProjectService $projectService)
{
    $projects = Project::where('emerging_property', true)->get();

    // Format price & size using ProjectService
    $formatted = $projectService->formatProjects($projects);

    // Add 'emerging_property' field in the formatted result
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['emerging_property'] = $projects[$key]->emerging_property ?? false;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No emerging properties found.'
            : 'Emerging properties retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

 public function getFeaturedProjects(ProjectService $projectService)
{
    $projects = Project::where('featured', true)->get();

    // Format price & size using ProjectService
    $formatted = $projectService->formatProjects($projects);

    // Add 'featured' flag explicitly in the response
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['featured'] = $projects[$key]->featured ?? false;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No featured projects found.'
            : 'Featured projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getLatestProjects(ProjectService $projectService)
{
    $projects = Project::orderBy('created_at', 'desc')->get();

    // Format price & size using ProjectService
    $formatted = $projectService->formatProjects($projects);

    // Add created_at explicitly in the response
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['created_at'] = $projects[$key]->created_at ?? null;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No latest projects found.'
            : 'Latest projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}

public function getemerging_area(ProjectService $projectService)
{
    $projects = Project::where('emerging_area', true)->get();

    // Format price & size using ProjectService
    $formatted = $projectService->formatProjects($projects);

    // Add emerging_area flag in the response
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['emerging_area'] = $projects[$key]->emerging_area ?? false;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No emerging area projects found.'
            : 'Emerging area projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}


  public function getProjectsWithBungalow(ProjectService $projectService)
{
    $projects = Project::where('configuration.bungalow', 'exists', true)->get();

    // Format price & size using ProjectService
    $formatted = $projectService->formatProjects($projects);

    // Add a flag to indicate bungalow presence
    $formatted->transform(function ($item, $key) use ($projects) {
        $item['has_bungalow'] = isset($projects[$key]->configuration['bungalow']);
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'message' => $formatted->isEmpty()
            ? 'No bungalow projects found.'
            : 'Bungalow projects retrieved successfully.',
        'data' => $formatted,
        'meta' => [
            'total' => $formatted->count(),
            'retrieved_at' => now()->toDateTimeString()
        ]
    ]);
}



    public function pindex()
    {
      $projects = Project::all();
$projects->transform(function ($project) {
    $temp = $project->project;

    $assetBaseUrl = env('ASSET_BASE_URL', 'http://127.0.0.1:8000');

    if (!empty($temp['logo_image_id'])) {
        $temp['logo_image_url'] = $assetBaseUrl . $temp['logo_image_id'];
    }

    if (!empty($temp['visual_image_id'])) {
        $temp['visual_image_url'] = $assetBaseUrl . $temp['visual_image_id'];
    }

    $project->project = $temp;
    return $project;
});

        return response()->json($projects);
    }


}
