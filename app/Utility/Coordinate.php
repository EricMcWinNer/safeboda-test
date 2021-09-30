<?php


namespace App\Utility;


class Coordinate
{
    public $latitude;
    public $longitude;

    /**
     * Coordinate constructor.
     * @param $latitude
     * @param $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function toArray(): array
    {
        return [$this->latitude, $this->longitude];
    }

}
