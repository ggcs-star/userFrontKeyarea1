<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;
use App\Services\ProjectFilterConfig;
use App\Services\ProjectService;
use Illuminate\Http\Request;

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
            ->filter(function ($project) {

                $configurations = $project->configuration ?? [];

                foreach ($configurations as $config) {

                    if (!isset($config['price']))
                        continue;

                    $priceText = $config['price'];

                    if (str_contains($priceText, '-')) {

                        [$min, $max] = array_map('trim', explode('-', $priceText));

                        $min = $this->normalizePrice($min);
                        $max = $this->normalizePrice($max);

                        if ($min >= 5000000 && $max <= 10000000)
                            return true;
                    } else {
                        $price = $this->normalizePrice($priceText);

                        if ($price >= 5000000 && $price <= 10000000)
                            return true;
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
        ]);
    }
    private function normalizePrice($value)
    {
        $v = strtolower(trim($value));
        $v = str_replace(',', '', $v);

        $num = (float) filter_var($v, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if (preg_match('/(lac|lakh|l)/i', $v)) {
            return $num * 100000;
        }
        if (preg_match('/(cr|crore)/i', $v)) {
            return $num * 10000000;
        }

        return $num;
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
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
                'retrieved_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function show($id)
    {
        try {
            $project = Project::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        try {
            $configuration = $project->configuration ?? [];

            $mergedImages = [];
            $mergedFloorPlans = [];
            $mergedGallery = [];
            $price = null;
            $totalPrice = null;
            $bhkUnits = [];

            if (is_array($configuration)) {
                foreach ($configuration as $configType => $configData) {
                    if (is_array($configData)) {
                        foreach ($configData as $bhkType => $details) {
                            if (!is_array($details)) {
                                continue;
                            }

                            if (!empty($details['imageslider']) && is_array($details['imageslider'])) {
                                $mergedImages = array_merge($mergedImages, $details['imageslider']);
                            }

                            if (!empty($details['floorPlans']) && is_array($details['floorPlans'])) {
                                $mergedFloorPlans = array_merge($mergedFloorPlans, $details['floorPlans']);
                            }

                            if (!empty($details['galleryImages']) && is_array($details['galleryImages'])) {
                                $mergedGallery = array_merge($mergedGallery, $details['galleryImages']);
                            }

                            if (isset($details['price']) && $price === null) {
                                $price = $details['price'];
                            }

                            $bhkUnits[] = [
                                'bhkType' => $bhkType,
                                'size' => $details['size'] ?? null,
                                'price' => $details['price'] ?? null,
                                'date' => $details['date'] ?? null,
                            ];
                        }
                    }
                }
            }

            $limitedImages = array_slice($mergedImages, 0, 5);

            if (is_array($project->configuration)) {
                foreach ($project->configuration as $configGroup) {
                    if (is_array($configGroup) && isset($configGroup['price'])) {
                        $totalPrice = $configGroup['price'];
                        break;
                    }
                }
            }

            $configurations = [];

            if (is_array($configuration)) {
                foreach ($configuration as $configType => $configData) {
                    if (is_array($configData)) {
                        foreach ($configData as $bhkType => $details) {
                            if (!is_array($details)) {
                                continue;
                            }

                            if (preg_match('/^\d+(\.\d+)?BHK$/i', $bhkType)) {
                                $configurations[] = strtoupper($bhkType);
                            }
                        }
                    }
                }
            }

            $configurations = array_values(array_unique($configurations));

            $sizes = [];
            foreach ($project->configuration ?? [] as $configGroup) {
                if (is_array($configGroup)) {
                    if (isset($configGroup['size'])) {
                        $sizes[] = (int) str_replace([' Sq. ft.', ','], '', $configGroup['size']);
                    } else {
                        foreach ($configGroup as $subConfig) {
                            if (is_array($subConfig) && isset($subConfig['size'])) {
                                $sizes[] = (int) str_replace([' Sq. ft.', ','], '', $subConfig['size']);
                            }
                        }
                    }
                }
            }
            $totalUnits = Unit::where('project_id', $id)->sum('total_units');

            // dd($totalUnits);

            $sizeRange = count($sizes) > 0
                ? min($sizes) . ' - ' . max($sizes) . ' Sq. ft.'
                : null;
            $data = [
                'project' => $project->project ?? null,
                'builder' => $project->builder ?? null,
                'imageslider' => $limitedImages,
                'floorPlans' => $mergedFloorPlans,
                'galleryImages' => $mergedGallery,
                'amenities' => $project->amenities ?? null,
                'configurations' => $configurations ?? null,
                'totalPrice' => [
                    'price' => $totalPrice,

                ],
                'totalSize' => [
                    'size' => $sizeRange,
                ],
                'bhkUnits' => $bhkUnits,
                'totalUnits' => $totalUnits,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while processing project details.',
            ], 500);
        }
    }

    public function similarProjects($id)
    {
        try {
            $currentProject = Project::findOrFail($id);

            $type = $currentProject->project['type'] ?? null;
            $city = $currentProject->project['location']['city'] ?? null;
            $area = $currentProject->project['location']['area'] ?? null;

            $configKeys = [];
            if (!empty($currentProject->configuration) && is_array($currentProject->configuration)) {
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
                if (!empty($proj->configuration) && is_array($proj->configuration)) {
                    foreach ($proj->configuration as $configGroup) {
                        if (is_array($configGroup) && isset($configGroup['price'])) {
                            $price = $configGroup['price'];
                            break;
                        }
                    }
                }

                return [
                    '_id' => $proj->_id ?? null,
                    'project' => [
                        'name' => $proj->project['name'] ?? null,
                        'type' => $proj->project['type'] ?? null,
                        'logo_image_id' => $proj->project['logo_image_id'] ?? null,
                        'location' => [
                            'city' => $proj->project['location']['city'] ?? null,
                            'area' => $proj->project['location']['area'] ?? null,
                        ],
                    ],
                    'configuration' => [
                        'price' => $price,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'similar_projects' => $formatted->values(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch similar projects.',
            ], 500);
        }
    }

    public function filterProjects(Request $request)
    {
        $filters = ProjectFilterConfig::filters();
        $query = Project::query();

        foreach ($request->all() as $key => $value) {
            if (!isset($filters[$key]) || $value === null) {
                continue;
            }

            $field = $filters[$key];

            if ($key === 'bhk') {
                $bhkType = $value;

                $query->where(function ($q) use ($bhkType) {
                    $q->orWhere("configuration.Homes.$bhkType", 'exists', true);
                    $q->orWhere("configuration.Villa.$bhkType", 'exists', true);
                    $q->orWhere("configuration.Bungalow.$bhkType", 'exists', true);
                });
            } elseif (in_array($key, ['featured', 'emerging_property', 'emerging_area'])) {
                $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN); // string → bool
                $query->where($field, $boolValue);
            } else {
                $query->where($field, $value);
            }
        }

        $projects = $query->get();

        return response()->json([
            'filters' => $this->projectService->extractAvailableFilters($projects),
            'projects' => $this->projectService->formatProjects($projects),
        ]);
    }

    public function mapProjects(Request $req)
    {
        try {

            $query = Project::query();


            if ($req->city) {
                $query->where('project.location.city', $req->city);
            }


            if ($req->area) {
                $query->where('project.location.area', $req->area);
            }


            if ($req->bhk) {
                $bhk = $req->bhk;
                $groups = ['Homes', 'Villa', 'bungalow', 'Apartments', 'Duplex', 'Penthouse'];

                $query->where(function ($q) use ($groups, $bhk) {
                    foreach ($groups as $grp) {
                        $q->orWhere("configuration.$grp.$bhk", '!=', null);
                    }
                });
            }


            if ($req->type) {
                $type = strtolower($req->type);
                $groups = ['Homes', 'Villa', 'bungalow', 'Apartments', 'Duplex', 'Penthouse'];

                $query->where(function ($q) use ($groups, $type) {
                    foreach ($groups as $grp) {
                        if (strtolower($grp) === $type) {
                            $q->orWhere("configuration.$grp", '!=', null);
                        }
                    }
                });
            }


            if ($req->amenities) {
                foreach ($req->amenities as $amenity) {
                    $query->where("amenities.$amenity", true);
                }
            }


            $projects = $query->get();


            if ($req->max_price) {
                $maxPrice = (float) $req->max_price;
                $groups = ['Homes', 'Villa', 'Bungalow', 'Apartments', 'Duplex', 'Penthouse'];

                $projects = $projects->filter(function ($p) use ($groups, $maxPrice) {

                    foreach ($groups as $grp) {
                        if (!isset($p->configuration[$grp]['price']))
                            continue;

                        $priceString = $p->configuration[$grp]['price'];
                        $numeric = $this->convertPriceToNumber($priceString);

                        if ($numeric && $numeric <= $maxPrice) {
                            return true;
                        }
                    }

                    return false;
                });
            }


            if ($req->lat && $req->lng && $req->km) {

                $lat0 = (float) $req->lat;
                $lng0 = (float) $req->lng;
                $radius = (float) $req->km;

                $projects = $projects->filter(function ($p) use ($lat0, $lng0, $radius) {

                    $loc = $p->project['location'] ?? [];

                    if (!isset($loc['latitude'], $loc['longitude'])) {
                        return false;
                    }

                    $lat = $loc['latitude'];
                    $lng = $loc['longitude'];

                    $distance = $this->haversine($lat0, $lng0, $lat, $lng);

                    return $distance <= $radius;
                });
            }


            $projects = $projects->filter(function ($p) {
                $loc = $p->project['location'] ?? [];

                return isset($loc['latitude'], $loc['longitude'])
                    && $loc['latitude'] !== null
                    && $loc['longitude'] !== null;
            });


            $response = $projects->map(function ($p) {

                $pr = $p->project;
                $loc = $pr['location'] ?? [];

                $groups = ['Homes', 'Villa', 'Bungalow', 'Apartments', 'Duplex', 'Penthouse'];

                $price = null;

                foreach ($groups as $grp) {
                    if (isset($p->configuration[$grp]['price'])) {
                        $price = $p->configuration[$grp]['price'];
                        break;
                    }
                }
                $sizes = [];

                $config = $p->configuration ?? [];

                foreach ($config as $group => $unitTypes) {
                    if (!is_array($unitTypes))
                        continue;

                    foreach ($unitTypes as $key => $unit) {

                        if (!is_array($unit))
                            continue;

                        if (isset($unit['size'])) {
                            $sizes[] = (int) filter_var($unit['size'], FILTER_SANITIZE_NUMBER_INT);
                        }

                        foreach ($unit as $subKey => $subVal) {
                            if (is_array($subVal) && isset($subVal['size'])) {
                                $sizes[] = (int) filter_var($subVal['size'], FILTER_SANITIZE_NUMBER_INT);
                            }
                        }
                    }
                }

                $sizeRange = count($sizes) > 0
                    ? min($sizes) . ' - ' . max($sizes) . ' Sq. ft.'
                    : null;


                return [
                    "id" => (string) $p->_id,
                    "name" => $pr['name'] ?? '',
                    "slug" => $pr['slug'] ?? '',
                    "address" => $loc['address'] ?? '',
                    "city" => $loc['city'] ?? '',
                    "area" => $loc['area'] ?? '',
                    "latitude" => (float) $loc['latitude'],
                    "longitude" => (float) $loc['longitude'],
                    "type" => $pr['type'] ?? '',
                    "price" => $price ?? null,
                    "logo_image_id" => $pr['logo_image_id'] ?? null,
                    "phase" => $pr['phase'] ?? null,
                    'size' => $sizeRange,
                ];
            });


            return response()->json([
                "status" => true,
                "count" => $response->count(),
                "projects" => $response->values()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "line" => $e->getLine()
            ], 500);
        }
    }


    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        return $earth * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function convertPriceToNumber($price)
    {
        $price = strtolower(trim($price));
        $price = str_replace(['₹', ',', ' '], '', $price);

        if (str_contains($price, '-')) {
            $price = explode('-', $price)[0];
        }

        if (str_contains($price, 'cr')) {
            return floatval(str_replace('cr', '', $price)) * 10000000;
        }

        if (str_contains($price, 'lac')) {
            return floatval(str_replace('lac', '', $price)) * 100000;
        }

        return floatval($price);
    }




}
