<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Project;
class UnitController extends Controller
{
    public function getUnits(Request $request, $projectId)
    {
        try {
            $blockId = $request->query('block');
            $floorNumber = $request->query('floor');

            $project = Project::find($projectId);

            if (!$project) {
                return response()->json([
                    "status" => false,
                    "message" => "Project not found."
                ], 404);
            }

            $blocks = Unit::where('project_id', $projectId)->get();

            if ($blocks->isEmpty()) {
                return response()->json([
                    "status" => false,
                    "message" => "No unit data found for this project."
                ], 404);
            }

            if (!$blockId) {
                $blockNames = $blocks->pluck('block')->unique()->values();

                return response()->json([
                    "status" => true,
                    "level" => "blocks",
                    "data" => $blockNames
                ]);
            }

            $selectedBlock = $blocks->where('block', $blockId)->first();

            if (!$selectedBlock) {
                return response()->json([
                    "status" => false,
                    "message" => "Block not found."
                ], 404);
            }

            if (!$floorNumber) {
                $floors = collect($selectedBlock->floors)->pluck('floor_number')->values();

                return response()->json([
                    "status" => true,
                    "level" => "floors",
                    "block" => $blockId,
                    "data" => $floors
                ]);
            }

            $floorData = collect($selectedBlock->floors)
                ->firstWhere('floor_number', (int) $floorNumber);

            if (!$floorData) {
                return response()->json([
                    "status" => false,
                    "message" => "Floor not found in this block."
                ], 404);
            }

            $allUnits = collect($floorData['units'] ?? [])
                ->merge($floorData['penthouse'] ?? [])
                ->values();

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

            $sizeRange = count($sizes) > 0
                ? min($sizes) . ' - ' . max($sizes) . ' Sq. ft.'
                : null;

            $projectLogo = $project->project['logo_image_id'] ?? null;

            return response()->json([
                "status" => true,
                "level" => "units",
                "block" => $blockId,
                "floor" => $floorNumber,
                "project_logo" => $projectLogo,
                "size_range" => $sizeRange,
                "data" => $allUnits
            ]);


        } catch (\Throwable $e) {
            return response()->json([
                "status" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }


}
