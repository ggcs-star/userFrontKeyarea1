<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\PriceParser;

class PriceProjectionController extends Controller
{
    public function show(Request $request, $id)
    {
        try {

            $years = max(1, (int) $request->query('years', 5));
            $growthPercent = floatval($request->query('growth', 10));
            $basedOn = $request->query('based_on');

            $project = Project::select('project', 'price', 'configuration')->find($id);

            if (!$project) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            $rawPrice = null;

            if ($basedOn) {
                $rawPrice = data_get($project, $basedOn);
            }

            if (!$rawPrice) {

                $config = data_get($project, 'configuration', []);

                if (is_array($config)) {
                    foreach ($config as $sectionName => $sectionValue) {

                        if (isset($sectionValue['price'])) {
                            $rawPrice = $sectionValue['price'];
                            break;
                        }

                        if (is_array($sectionValue)) {
                            foreach ($sectionValue as $unitName => $unitValue) {
                                if (is_array($unitValue) && isset($unitValue['price'])) {
                                    $rawPrice = $unitValue['price'];
                                    break 2;
                                }
                            }
                        }
                    }
                }

                if (!$rawPrice) {
                    $fallbackPaths = [
                        'project.price',
                        'price'
                    ];

                    foreach ($fallbackPaths as $path) {
                        $val = data_get($project, $path);
                        if (!empty($val)) {
                            $rawPrice = $val;
                            break;
                        }
                    }
                }

                if (!$rawPrice) {
                    $json = json_encode($project);
                    if (preg_match('/"price"\s*:\s*"([^"]+)"/i', $json, $m)) {
                        $rawPrice = $m[1];
                    }
                }
            }


            if (!$rawPrice) {
                return response()->json([
                    'status' => false,
                    'message' => 'Price not found'
                ], 422);
            }

            $baseline = PriceParser::parseToRupees($rawPrice);

            if (!$baseline) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to parse price string',
                    'raw_price' => $rawPrice
                ], 422);
            }

            $projection = [];
            $current = $baseline;

            $projection[] = [
                'year' => 0,
                'label' => now()->format('Y') . ' (Now)',
                'price' => round($current, 2),
                'display' => PriceParser::formatIndian($current),
            ];

            $growthMultiplier = 1 + ($growthPercent / 100);

            for ($i = 1; $i <= $years; $i++) {
                $current *= $growthMultiplier;

                $projection[] = [
                    'year' => $i,
                    'label' => now()->addYears($i)->format('Y'),
                    'price' => round($current, 2),
                    'display' => PriceParser::formatIndian($current),
                ];
            }

            return response()->json([
                'status' => true,
                'project_id' => (string) $project->_id,
                'raw_price' => $rawPrice,
                'baseline_rupees' => $baseline,
                'growth_percent' => $growthPercent,
                'years' => $years,
                'projection' => $projection
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
