<?php

namespace App\Services;

class GoogleMapsDistanceService
{
    /**
     * Mock function to simulate calculating the distance between two coordinates.
     * Returns a random distance between 1 and 10 kilometers for testing.
     * In production, this would call the Google Maps Distance Matrix API.
     */
    public function getDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        // Calculate Euclidean distance (very rough approximation for mocking purposes)
        $latDiff = $lat1 - $lat2;
        $lngDiff = $lng1 - $lng2;
        
        $distance = sqrt(($latDiff * $latDiff) + ($lngDiff * $lngDiff)) * 111; // 1 degree is roughly 111km
        
        // Return simulated distance (at least 1km, max 20km)
        return max(1, min(20, $distance + rand(0, 5)));
    }
}
