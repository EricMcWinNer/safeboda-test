<?php


namespace App\Utility;


use App\Exceptions\GeoCodingException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeoCodingManager
{
    public static function getAddressFromCoordinates(Coordinate $coordinate) {
        $request = env('GEOCODING_API_URL') . "?latlng=$coordinate->latitude,$coordinate->longitude&key=" . env('GEOCODING_API_KEY');
        $client = new Client();
        try {
            $response = $client->get($request);
            $body = json_decode($response->getBody(), true);
            if($body['status'] === "OK") {
                return $body['results'][0]['formatted_address'];
            }
            throw new GeoCodingException("Failed to get a valid address from latitude and longitude");
        } catch (GuzzleException $e) {
            throw new GeoCodingException($e->getMessage());
        }
    }

    public static function getCoordinatesFromAddress(string $address): ?Coordinate
    {
        $address = str_replace(" ", "+",$address);
        $request = env('GEOCODING_API_URL') . "?address=$address&key=" . env('GEOCODING_API_KEY');
        $client = new Client();
        try {
            $response = $client->get($request);
            $body = json_decode($response->getBody(), true);
            if($body['status'] === "OK") {
                $location  = $body['results'][0]['geometry']['location'];
                return new Coordinate($location['lat'], $location['lng']);
            }
            throw new GeoCodingException("Failed to get a valid address from latitude and longitude");
        } catch (GuzzleException $e) {
            throw new GeoCodingException($e->getMessage());
        }
    }
}
