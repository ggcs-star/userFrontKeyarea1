<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function formatProjects($projects)
    {
        return $projects->map(function ($project) {

            $sizes = [];
            $price = null;

            foreach ($project->configuration as $configGroup) {
                if (is_array($configGroup) && isset($configGroup['price'])) {
                    $price = $configGroup['price'];
                    break;
                }
            }

            foreach ($project->configuration as $configGroup) {
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

            $logoImageUrl = !empty($project->project['logo_image_id']) 
                ? $project->project['logo_image_id']
                : null;

            $visualImageUrl = !empty($project->project['visual_image_id']) 
                ? $project->project['visual_image_id']
                : null;

            return [
                'id' => (string) $project->_id,
                'project' => [
                    'name' => $project->project['name'],
                    'type' => $project->project['type'],
                    'logo_image_url' => $logoImageUrl,
                    'visual_image_url' => $visualImageUrl,
                    'location' => [
                        'city' => $project->project['location']['city'] ?? null,
                        'area' => $project->project['location']['area'] ?? null,
                    ],
                ],
                'configuration' => [
                    'price' => $price,
                    'size' => $sizeRange,
                ],
            ];
        })->values();
    }

    public function convertPriceToNumber($priceString)
    {
        $priceString = strtolower(str_replace(',', '', $priceString));
        $priceString = str_replace(['lca', 'lc', 'lack', 'lacks'], 'lac', $priceString);

        if (str_contains($priceString, '-')) {
            [$from, $to] = explode('-', $priceString);
            $fromNumber = $this->extractSinglePrice(trim($from));
            $toNumber = $this->extractSinglePrice(trim($to));
            return ($fromNumber + $toNumber) / 2;
        }

        return $this->extractSinglePrice(trim($priceString));
    }

    private function extractSinglePrice($pricePart)
    {
        if (str_contains($pricePart, 'lac')) {
            preg_match('/(\d+\.?\d*)\s*lac/', $pricePart, $matches);
            return isset($matches[1]) ? floatval($matches[1]) * 1e5 : 0;
        }

        if (str_contains($pricePart, 'cr')) {
            preg_match('/(\d+\.?\d*)\s*cr/', $pricePart, $matches);
            return isset($matches[1]) ? floatval($matches[1]) * 1e7 : 0;
        }

        if (preg_match('/(\d+\.?\d*)/', $pricePart, $matches)) {
            return isset($matches[1]) ? floatval($matches[1]) : 0;
        }

        return 0;
    }
}
