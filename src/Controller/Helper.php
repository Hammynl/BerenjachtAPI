<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class Helper {

    function addRateLimiter(RateLimiterFactory $factory, Request $request) {

        $limiter = $factory->create($request->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

    }

    function isInRange($lat1, $lon1, $lat2, $lon2, $range): bool
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        // Convert the given coordinates to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Calculate the differences between the coordinates
        $latDiff = $lat2 - $lat1;
        $lonDiff = $lon2 - $lon1;

        // Apply the Haversine formula to calculate the distance
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Calculate the distance in kilometers
        $distance = $earthRadius * $c;

        // Check if the distance is within the specified range
        return $distance <= $range;
    }
}
