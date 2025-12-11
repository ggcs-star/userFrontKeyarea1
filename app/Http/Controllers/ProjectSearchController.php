<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectSearchController extends Controller
{
    /**
     * Query params:
     * q = free text (name/address/type)
     * city = city name
     * area = area name (e.g. Gota)
     * lat, lng = point from which to search nearby (optional)
     * radius_km = numeric (default 5)
     * sort = distance|recent|relevance (default relevance)
     * page, per_page
     */
    public function search(Request $req)
    {
        dd($req);
        $q = $req->input('q');
        $city = $req->input('city');
        $area = $req->input('area');
        $lat = $req->input('lat');
        $lng = $req->input('lng');
        $radiusKm = (float) $req->input('radius_km', 5);
        $sort = $req->input('sort', 'relevance');
        $perPage = (int) $req->input('per_page', 20);
        $page = max(1, (int) $req->input('page', 1));

        $pipeline = [];

        // If lat & lng provided => $geoNear stage (must be first)
        if ($lat && $lng) {
            $radiusMeters = $radiusKm * 1000;
            $pipeline[] = [
                '$geoNear' => [
                    'near' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$lng, (float)$lat]
                    ],
                    'distanceField' => 'dist.calculated',
                    'maxDistance' => $radiusMeters,
                    'spherical' => true,
                    // optional: query to filter status/city...
                    'query' => (object)[],
                    'distanceMultiplier' => 1 // meters returned; we'll convert in response
                ]
            ];
        }

        // Match filters by city/area/status
        $match = ['status' => 'active']; // only active by default
        if ($city) $match['location.city'] = ['$regex' => new \MongoDB\BSON\Regex('^' . preg_quote($city) . '$', 'i')];
        if ($area) $match['location.area'] = ['$regex' => new \MongoDB\BSON\Regex('^' . preg_quote($area) . '$', 'i')];

        // If user provided only area/city without coords: we still want close-by areas.
        // We'll do a text + regex OR fallback to a geo 'near area centroid' if you have area centroid data.
        // For now, apply the match:
        $pipeline[] = ['$match' => $match];

        // If free-text q is provided, add $match with $text or regex
        if ($q) {
            // prefer text search if index exists
            $pipeline[] = [
                '$match' => [
                    '$or' => [
                        ['$text' => ['$search' => $q]],
                        ['name' => ['$regex' => new \MongoDB\BSON\Regex(preg_quote($q), 'i')]],
                        ['location.address' => ['$regex' => new \MongoDB\BSON\Regex(preg_quote($q), 'i')]],
                        ['type' => ['$regex' => new \MongoDB\BSON\Regex(preg_quote($q), 'i')]]
                    ]
                ]
            ];
        }

        // Sorting
        $sortStage = [];
        if ($sort === 'distance' && $lat && $lng) {
            $sortStage = ['$sort' => ['dist.calculated' => 1]];
        } elseif ($sort === 'recent') {
            $sortStage = ['$sort' => ['created_at' => -1]];
        } else {
            // relevance: if we used $text, we can project score; else fallback created_at desc
            if ($q) {
                // Add projection of score if text used — but $text must be in top-level pipeline, to get score use $meta in projection.
                // Simpler: sort by created_at desc if no geo sorting
                $sortStage = ['$sort' => ['created_at' => -1]];
            } else {
                $sortStage = ['$sort' => ['created_at' => -1]];
            }
        }

        if ($sortStage) $pipeline[] = $sortStage;

        // Pagination: skip/limit
        $skip = ($page - 1) * $perPage;
        $pipeline[] = ['$skip' => $skip];
        $pipeline[] = ['$limit' => $perPage];

        // Project fields and convert distance to km
        $pipeline[] = [
            '$project' => [
                '_id' => 1,
                'name' => 1,
                'slug' => 1,
                'brochure' => 1,
                'type' => 1,
                'location' => 1,
                'status' => 1,
                'created_at' => 1,
                'dist_meters' => ['$ifNull' => ['$dist.calculated', null]],
                'dist_km' => [
                    '$cond' => [
                        ['if' => ['$ifNull' => ['$dist.calculated', false]], 'then' => ['$divide' => ['$dist.calculated', 1000]], 'else' => null
                    ]
                ]
            ]
        ]
                    ];

        // Run aggregation
        $collection = (new Project())->getCollection();
        $cursor = $collection->aggregate($pipeline);

        $results = iterator_to_array($cursor);

        // If you want total count, need separate aggregation (count stage) — omitted for speed.
        return response()->json([
            'page' => $page,
            'per_page' => $perPage,
            'count' => count($results),
            'data' => $results
        ]);
    }
}
