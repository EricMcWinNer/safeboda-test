<?php


namespace App\Utility;


class DistanceManager
{
    private $coordinate;

    public function __construct($latitude, $longitude) {
        $this->coordinate = new Coordinate($latitude, $longitude);
    }

    public function calculateDistance(Coordinate $coordinate, $unit = 'meters') {
        $distanceInMeters = $this->haversineGreatCircleDistance($this->coordinate, $coordinate);
        switch($unit){
            case 'kilometers':
                $distance = $distanceInMeters / 1000;
                break;
            case 'miles':
                $distance = $distanceInMeters * 0.000621371192;
                break;
            default:
                $distance = $distanceInMeters;
        }
        return $distance;
    }

    private function haversineGreatCircleDistance(
        Coordinate $from, Coordinate $to, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($from->latitude);
        $lonFrom = deg2rad($from->longitude);
        $latTo = deg2rad($to->latitude);
        $lonTo = deg2rad($to->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
                              (sin($latDelta / 2) ** 2) +
                               cos($latFrom) * cos($latTo) * (sin($lonDelta / 2) ** 2)
                          ));
        return $angle * $earthRadius;
    }
}
