<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\River;
use App\Models\User;
use App\Models\FloodAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FloodAlertController extends Controller
{
    private const EARTH_RADIUS = 6371; // km

    public function index(Request $request)
    {
        $query = FloodAlert::query();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        } 

        $alerts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.alerts', compact('alerts'));
    }

    public function destroy(FloodAlert $alert)
    {
        $alert->delete();
        return redirect()->back()->with('success', 'Flood alert deleted successfully.');
    }

    public function checkFlood()
    {
        $radiusLimit = 10; // km
        $kmToDegrees = 1 / 111.32; // Approx km per degree
        $degreeOffset = $radiusLimit * $kmToDegrees;

        // Deactivate old alerts
        FloodAlert::where('is_active', true)
        ->where('created_at', '<', now()->subDay())
        ->update(['is_active' => false]);

        $users = User::all();
        $allRivers = cache()->rememberForever('constant_rivers', function () {
            return River::all();
        });
        
        $userToRiversMap = [];

        // Match users to nearby rivers within 10km
        foreach ($users as $user) {
            $userLat = $user->latitude;
            $userLon = $user->longitude;

            $nearbyRivers = $allRivers->filter(function ($river) use ($userLat, $userLon, $degreeOffset) {
                return abs($river->latitude - $userLat) <= $degreeOffset
                    && abs($river->longitude - $userLon) <= $degreeOffset;
            });

            foreach ($nearbyRivers as $river) {
                $distance = $this->calculateDistance(
                    $userLat, $userLon,
                    $river->latitude, $river->longitude
                );
                
                if ($distance <= $radiusLimit) {
                    $userToRiversMap[$river->river_id][] = [
                        'user_id' => $user->user_id,
                        'distance' => $distance,
                    ];
                }
            }
        }

        // Get unique river IDs to fetch only those forecasts
        $uniqueRiverIds = array_keys($userToRiversMap);

        $riverForecastCache = [];

        // Fetch and cache the forecast data for each river
        foreach ($uniqueRiverIds as $riverId) {
            $raw = $this->getRawApiData($riverId);
            $riverForecastCache[$riverId] = $this->cleanForecastData($raw);
        }

        // Loop through each river once and process it

        foreach ($userToRiversMap as $riverId => $usersData) {
            $clean = $riverForecastCache[$riverId] ?? null;

            if ($clean && !empty($clean['forecast'])) {
                foreach ($clean['forecast'] as $pt) {
                    if ($pt['value'] === null) continue;

                    $levelInfo = $this->getFloodLevel($pt['value'], $clean['thresholds']);

                    if ($levelInfo) {
                        $message = $this->generateAlertMessage($levelInfo['level'], $pt['value'], $levelInfo['threshold'], $distance);
                        
                        // Save only one flood alert for the river
                        FloodAlert::create([
                            'admin_id' => Auth::id(),
                            'message'  => $message,
                            'severity' => $levelInfo['level'],
                            'description' => "River ID $riverId, affecting " . count($usersData) . " users",
                            'is_active' => true,
                        ]);

                        // Notify affected users
                        foreach ($usersData as $userData) {
                            $user = User::find($userData['user_id']);
                            if ($user) {
                                $user->notify(new \App\Notifications\FloodAlertNotification($message, $levelInfo['level']));
                            }
                        }

                        break; // Only log one alert per river
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Flood alerts sent successfully.');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat/2)**2 + cos($lat1)*cos($lat2)*sin($dLon/2)**2;
        return self::EARTH_RADIUS * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    private function getRawApiData(int $riverId): ?array
    {
        $date = now()->subDay()->format('Ymd');
        $url  = "https://tethys.icimod.org/apps/flashfloodnp/chartHiwat/?stID={$riverId}&date={$date}";

        $res = Http::get($url);
        return $res->successful() ? $res->json() : null;
    }

    private function cleanForecastData(?array $raw): array
    {
        if (!$raw) {
            return ['forecast' => [], 'thresholds' => []];
        }

        $forecast = [];
        foreach ($raw['dates'] as $i => $dt) {
            $forecast[] = [
                'datetime' => $dt,
                'value'    => $raw['values'][$i] ?? null,
            ];
        }

        $thresholds = [
            'return_max' => $raw['return_max'] ?? null,
            'return_2'   => $raw['return_2']   ?? null,
            'return_10'  => $raw['return_10']  ?? null,
            'return_20'  => $raw['return_20']  ?? null,
        ];

        return compact('forecast', 'thresholds');
    }

    private function getFloodLevel($value, $thresholds)
    {
        // Prioritize higher thresholds first
        if ($value >= $thresholds['return_max']) {
            return ['level' => 'max', 'threshold' => $thresholds['return_max']];
        } elseif ($value >= $thresholds['return_20']) {
            return ['level' => 'high', 'threshold' => $thresholds['return_20']];
        } elseif ($value >= $thresholds['return_10']) {
            return ['level' => 'medium', 'threshold' => $thresholds['return_10']];
        } elseif ($value >= $thresholds['return_2']) {
            return ['level' => 'low', 'threshold' => $thresholds['return_2']];
        }
        return null;
    }

    private function generateAlertMessage($level, $value, $threshold, $distance)
    {
        switch ($level) {
            case 'max':
                return "Extreme flood risk! Forecasted: $value, Threshold: $threshold. Evacuate ASAP!. Distance: $distance";
            case 'high':
                return "Severe flood warning! Forecasted: $value, Threshold: $threshold. Prepare immediately. Distance: $distance";
            case 'medium':
                return "Moderate flood risk. Forecasted: $value, Threshold: $threshold. Stay alert. Distance: $distance";
            case 'low':
                return "Minor flood possibility. Forecasted: $value, Threshold: $threshold. Be cautious. Distance: $distance";
            default:
                return "No flood.";
        }
    }
}
