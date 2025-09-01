<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class VisitorController extends Controller
{
    /**
     * Store visitor tracking data
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'visitor_id' => 'required|string',
            'ip' => 'nullable|ip',
            'location' => 'nullable|array',
            'isp' => 'nullable|string',
            'device' => 'nullable|string',
            'browser' => 'nullable|string',
            'os' => 'nullable|string',
            'page_url' => 'required|url',
            'referrer' => 'nullable|url',
            'actions' => 'nullable|array',
            'time_spent' => 'nullable|integer|min:0',
            'session_id' => 'nullable|string',
            'page_entered_at' => 'nullable|date',
            'page_exited_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get client IP if not provided
            $ip = $request->ip ?? $request->ip();

            // Get location data if not provided
            $location = $request->location;
            if (!$location) {
                $location = $this->getLocationFromIP($ip);
            }

            // Create visitor record
            $visitor = Visitor::create([
                'visitor_id' => $request->visitor_id,
                'ip' => $ip,
                'location' => $location,
                'isp' => $request->isp,
                'device' => $request->device,
                'browser' => $request->browser,
                'os' => $request->os,
                'page_url' => $request->page_url,
                'referrer' => $request->referrer,
                'actions' => $request->actions,
                'time_spent' => $request->time_spent ?? 0,
                'session_id' => $request->session_id ?? Str::uuid(),
                'page_entered_at' => $request->page_entered_at ?? now(),
                'page_exited_at' => $request->page_exited_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visitor data recorded successfully',
                'data' => [
                    'id' => $visitor->id,
                    'session_id' => $visitor->session_id
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record visitor data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update page exit time and time spent
     */
    public function updateExit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'page_url' => 'required|string',
            'time_spent' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $visitor = Visitor::where('session_id', $request->session_id)
                ->where('page_url', $request->page_url)
                ->whereNull('page_exited_at')
                ->latest()
                ->first();

            if ($visitor) {
                $visitor->update([
                    'page_exited_at' => now(),
                    'time_spent' => $request->time_spent,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Page exit updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Visitor session not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page exit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location data from IP address
     */
    private function getLocationFromIP($ip)
    {
        try {
            // Handle localhost and private IPs
            if (
                in_array($ip, ['127.0.0.1', '::1', 'localhost']) ||
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
            ) {

                // For localhost/private IPs, try to get external IP
                $externalIP = $this->getExternalIP();
                if ($externalIP && $externalIP !== $ip) {
                    $ip = $externalIP;
                } else {
                    // Fallback for localhost
                    return [
                        'country' => 'Local Development',
                        'city' => 'Localhost',
                        'region' => 'Development Environment',
                        'ip' => $ip,
                        'isp' => 'Local Network'
                    ];
                }
            }

            // Use ip-api.com for location data
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,mobile,proxy,hosting,query");

            if ($response === false) {
                throw new Exception('Failed to fetch location data');
            }

            $data = json_decode($response, true);

            if ($data['status'] === 'success') {
                return [
                    'country' => $data['country'] ?? 'Unknown',
                    'country_code' => $data['countryCode'] ?? '',
                    'region' => $data['regionName'] ?? 'Unknown',
                    'city' => $data['city'] ?? 'Unknown',
                    'zip' => $data['zip'] ?? '',
                    'lat' => $data['lat'] ?? null,
                    'lon' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? '',
                    'isp' => $data['isp'] ?? 'Unknown',
                    'org' => $data['org'] ?? '',
                    'ip' => $data['query'] ?? $ip,
                    'mobile' => $data['mobile'] ?? false,
                    'proxy' => $data['proxy'] ?? false,
                    'hosting' => $data['hosting'] ?? false
                ];
            }

            throw new Exception('Location API returned error: ' . ($data['message'] ?? 'Unknown error'));
        } catch (Exception $e) {
            Log::warning('Failed to get location from IP: ' . $e->getMessage());

            // Return fallback data
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'region' => 'Unknown',
                'ip' => $ip,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get external IP address
     */
    private function getExternalIP()
    {
        try {
            $services = [
                'https://api.ipify.org',
                'https://ipinfo.io/ip',
                'https://icanhazip.com',
                'https://ident.me'
            ];

            foreach ($services as $service) {
                $ip = @file_get_contents($service);
                if ($ip !== false && filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    return trim($ip);
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to get external IP: ' . $e->getMessage());
        }

        return null;
    }
}
