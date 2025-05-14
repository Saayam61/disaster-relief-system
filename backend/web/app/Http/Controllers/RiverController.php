<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\River;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

class RiverController extends Controller
{
    protected $proj4;
    protected $srcProj;
    protected $dstProj;

    public function __construct()
    {
        // Initialize projection (Web Mercator to WGS84)
        $this->proj4 = new Proj4php();
        $this->srcProj = new Proj('EPSG:3857', $this->proj4);
        $this->dstProj = new Proj('EPSG:4326', $this->proj4);
    }

    /**
     * Run this to fetch ONE chunk of data — manually update startIndex/maxFeatures per run.
     */
    public function fetchRiversData(Request $request)
    {
        $startIndex = 13000;          // pass ?start=0
        $maxFeatures = 1000;       // pass ?limit=100

        $apiUrl = "https://tethys.icimod.org:8443/geoserver/hydroviewer/ows?" .
            "service=WFS&version=1.0.0&request=GetFeature" .
            "&typeName=hydroviewer:nepalriver" .
            "&maxFeatures={$maxFeatures}" .
            "&outputFormat=application/json" .
            "&startIndex={$startIndex}" .
            "&sortBy=linkno";

        $response = Http::timeout(60)->get($apiUrl);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        }

        $data = $response->json();

        if (empty($data['features'])) {
            return response()->json(['message' => 'No data returned. Probably reached the end.']);
        }

        $stored = [];

        foreach ($data['features'] as $feature) {
            $riverData = $this->processRiverData($feature);

            if ($riverData && !River::where('river_id', $riverData['river_id'])->exists()) {
                River::create($riverData);
                $stored[] = $riverData;
            }
        }

        return response()->json([
            $riverData,
            'message' => 'Chunk fetched and stored.',
            'start_index' => $startIndex,
            'limit' => $maxFeatures,
            'stored_count' => count($stored),
            'sample' => array_slice($stored, 0, 5),
        ]);
    }

    /**
     * Convert projected bbox midpoint to lat/lon.
     */
    private function processRiverData($feature)
    {
        $bbox = $feature['bbox'] ?? null;

        if (!$bbox || count($bbox) !== 4) {
            return null;
        }

        $midX = round(($bbox[0] + $bbox[2]) / 2, 7);
        $midY = round(($bbox[1] + $bbox[3]) / 2, 7);


        $point = new Point($midX, $midY, $this->srcProj);
        $converted = $this->proj4->transform($this->dstProj, $point);

        return [
            'river_id' => $feature['properties']['linkno'],
            'latitude' => $converted->y,
            'longitude' => $converted->x,
        ];
    }
}
