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

        $formatted = $projectService->formatProjects($projects);

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

        $formatted = $projectService->formatProjects($projects);

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

        $formatted = $projectService->formatProjects($projects);

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

        $formatted = $projectService->formatProjects($projects);

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

        $formatted = $projectService->formatProjects($projects);

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
    public function show($id)
{
    try {
        $project = Project::findOrFail($id);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Project not found'
        ], 404);
    }

    $configuration = $project->configuration ?? [];

    $mergedImages = [];
    $mergedFloorPlans = [];
    $mergedGallery = [];
    $price = null;
    $bhkUnits = [];

    if (is_array($configuration)) {
        foreach ($configuration as $configType => $configData) {
            if (is_array($configData)) {
                foreach ($configData as $bhkType => $details) {
                    if (is_array($details)) {

                        if (isset($details['imageslider']) && is_array($details['imageslider'])) {
                            $mergedImages = array_merge($mergedImages, $details['imageslider']);
                        }

                        if (isset($details['floorPlans']) && is_array($details['floorPlans'])) {
                            $mergedFloorPlans = array_merge($mergedFloorPlans, $details['floorPlans']);
                        }

                        if (isset($details['galleryImages']) && is_array($details['galleryImages'])) {
                            $mergedGallery = array_merge($mergedGallery, $details['galleryImages']);
                        }

                        if (isset($details['price']) && $price === null) {
                            $price = $details['price'];
                        }

                        $bhkUnits[] = [
                            'bhkType' => $bhkType,
                            'size'    => $details['size'] ?? null,
                            'price'   => $details['price'] ?? null,
                            'date'   => $details['date'] ?? null,
                        ];
                    }
                }
            }
        }
    }

    $limitedImages = array_slice($mergedImages, 0, 5);

    foreach ($project->configuration as $configGroup) {
        if (is_array($configGroup) && isset($configGroup['price'])) {
            $totalPrice = $configGroup['price'];
            break;
        }
    }

    $data = [
        'project'       => $project->project ?? null,
        'builder'       => $project->builder ?? null,
        'imageslider'   => $limitedImages,
        'floorPlans'    => $mergedFloorPlans,
        'galleryImages' => $mergedGallery,
        'amenities'     => $project->amenities ?? null,
        'totalPrice'    => [
            'price' => $totalPrice ?? null,
        ],
        'bhkUnits'      => $bhkUnits,
    ];

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

 public function similarProjects($id)
{
    $currentProject = Project::findOrFail($id);

    $type = $currentProject->project['type'] ?? null;
    $city = $currentProject->project['location']['city'] ?? null;
    $area = $currentProject->project['location']['area'] ?? null;

    $configKeys = [];
    if (isset($currentProject->configuration) && is_array($currentProject->configuration)) {
        $configKeys = array_keys($currentProject->configuration);
    }

    $strongMatches = Project::where('_id', '!=', $id)
        ->where(function ($query) use ($type, $city, $area, $configKeys) {
            if ($city && $area) {
                $query->orWhere(function ($q) use ($city, $area) {
                    $q->where('project.location.city', $city)
                      ->where('project.location.area', $area);
                });
            }
            if ($type) {
                $query->orWhere('project.type', $type);
            }
            if (!empty($configKeys)) {
                foreach ($configKeys as $key) {
                    $query->orWhere("configuration.$key", 'exists', true);
                }
            }
        })
        ->limit(10)
        ->get();

    if ($strongMatches->count() < 10) {
        $looseMatches = Project::where('_id', '!=', $id)
            ->where(function ($query) use ($type, $city, $area, $configKeys) {
                if ($city) {
                    $query->orWhere('project.location.city', $city);
                }
                if ($area) {
                    $query->orWhere('project.location.area', $area);
                }
                if ($type) {
                    $query->orWhere('project.type', $type);
                }
                if (!empty($configKeys)) {
                    foreach ($configKeys as $key) {
                        $query->orWhere("configuration.$key", 'exists', true);
                    }
                }
            })
            ->limit(10 - $strongMatches->count())
            ->get();

        $similarProjects = $strongMatches->merge($looseMatches);
    } else {
        $similarProjects = $strongMatches;
    }

   $formatted = $similarProjects->take(10)->map(function ($proj) {
    $price = null;
    if (is_array($proj->configuration)) {
        foreach ($proj->configuration as $configGroup) {
            if (is_array($configGroup) && isset($configGroup['price'])) {
                $price = $configGroup['price'];
                break;
            }
        }
    }

    return [
        "_id" => $proj->_id,
        "project" => [
            "name" => $proj->project['name'] ?? null,
            "type" => $proj->project['type'] ?? null,
            "logo_image_id" => $proj->project['logo_image_id'] ?? null,
            "location" => [
                "city" => $proj->project['location']['city'] ?? null,
                "area" => $proj->project['location']['area'] ?? null,
            ]
        ],
        "configuration" => [
            "price" => $price
        ],
    ];
});
    return response()->json([
        'success' => true,
        'similar_projects' => $formatted->values(),
    ]);

}



}
