<?php


namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PromoCode;
use App\Utility\Coordinate;
use App\Utility\DistanceManager;
use App\Utility\GeoCodingManager;
use App\Validators\PromoCodeControllerValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;


class PromoCodeController extends BaseController
{
    use PromoCodeControllerValidator;

    public function createPromoCode(Request $request)
    {
        $this->validateCreationInput($request);
        $code = $request->input('code');
        $eventId = $request->input('event_id');
        $codeAvailabilityErrorResponse = $this->checkForCodeAvailability($code);
        if (!is_null($codeAvailabilityErrorResponse)) {
            return $codeAvailabilityErrorResponse;
        }
        if ($this->checkIfEventExists($eventId)) {
            $promoCode = new PromoCode();
            $promoCode->code = $code;
            $promoCode->event_id = $eventId;
            $promoCode->amount = $request->input('amount');
            $promoCode->expires_at = Carbon::parse($request->input('expires_at'));
            $promoCode->valid_radius = $request->input('valid_radius') ?? 23;
            $promoCode->save();
            $promoCode->load('event');
            return response(
                [
                    'message' => 'Promo code created successfully',
                    'status' => 'success',
                    'data' => $promoCode
                ],
                201
            );
        }
        return response(
            [
                'message' => 'The event_id given is invalid. It must match an event on our system.',
                'status' => 'failed'
            ],
            400
        );
    }

    private function checkForCodeAvailability(string $code)
    {
        $preExistingActivePromoCode = $this->checkForActivePromoCode($code);
        if (!is_null($preExistingActivePromoCode) && !$this->deActivatePromoCodeIfExpired(
                $preExistingActivePromoCode
            )) {
            return response(
                [
                    'message' => "A valid promo code already exists with the code '$code'",
                    'status' => "failed"
                ],
                400
            );
        }
        return null;
    }

    private function checkForActivePromoCode(string $code): ?PromoCode
    {
        return PromoCode::where('code', $code)->where('is_active', true)->first();
    }

    private function deActivatePromoCodeIfExpired(PromoCode $promoCode): bool
    {
        if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($promoCode->expires_at))) {
            $promoCode->is_active = false;
            $promoCode->save();
            return true;
        }
        return false;
    }

    private function checkIfEventExists(int $eventId): bool
    {
        $event = Event::find($eventId);
        return !is_null($event);
    }

    public function readAllPromoCodes()
    {
        $promoCodes = PromoCode::with('event')->get();
        return response(
            [
                'status' => 'success',
                'data' => $promoCodes,
            ],
            200
        );
    }

    public function readAllActivePromoCodes()
    {
        $promoCodes = PromoCode::with('event')->where('is_active', true)
            ->where('expires_at', '>', Carbon::now()->toDateTimeString())
            ->get();
        return response(
            [
                'status' => 'success',
                'data' => $promoCodes,
            ],
            200
        );
    }

    public function deactivatePromoCode($id)
    {
        try {
            $promoCode = PromoCode::findOrFail($id);
            $promoCode->is_active = false;
            $promoCode->save();
            return response(
                [
                    'status' => 'success',
                    'message' => 'Promo code was successfully deactivated'
                ]
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    public function configurePromoCodeRadius(Request $request, $id)
    {
        try {
            $this->validateRadiusConfigurationInput($request);
            $promoCode = PromoCode::findOrFail($id);
            if ($promoCode->isValid()) {
                $promoCode->valid_radius = $request->input('valid_radius');
                $promoCode->save();
                return response(
                    [
                        'message' => 'Promo Code radius updated successfully',
                        'status' => 'success',
                        'data' => $promoCode,
                    ],
                    201
                );
            }
            return response(
                [
                    'message' => "Cannot configure the valid radius of an invalid promo code",
                    'status' => "failed"
                ],
                400
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    public function activateDeactivatedPromoCode($id)
    {
        try {
            $promoCode = PromoCode::findOrFail($id);
            if ($promoCode->isNotExpired()) {
                if ($promoCode->isActive()) {
                    return response(
                        [
                            'message' => 'You cannot activate an already active promo code',
                            'status' => 'failed'
                        ],
                        400
                    );
                }
                $promoCode->is_active = true;
                $promoCode->save();
                return response(
                    [
                        'message' => 'Promo code activated successfully',
                        'status' => 'success'
                    ],
                    200
                );
            }
            $promoCode->is_active = false;
            $promoCode->save();
            return response(
                [
                    'message' => 'You cannot activate an expired promo code',
                    'status' => 'failed'
                ],
                400
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    public function usePromoCode(Request $request)
    {
        try {
            $this->validateUsePromoCodeInput($request);
            $promoCode = PromoCode::with('event')->where('code', $request->input('code'))->firstOrFail();
            if ($promoCode->isNotExpired()) {
                if ($promoCode->isActive()) {
                    $requestCoordinates = $this->getCoordinatesFromRequest($request);
                    [$originCoordinates, $destinationCoordinates] = $requestCoordinates;
                    if ($this->checkIfOriginOrDestinationIsWithinValidRadius($requestCoordinates, $promoCode)) {
                        // Return polyline

                        return response(
                            [
                                'message' => 'Promo code is valid',
                                'status' => 'success',
                                'data' => [
                                    'promo_code' => $promoCode,
                                    'event_location' => [
                                        'latitude' => $promoCode->event->latitude,
                                        'longitude' => $promoCode->event->longitude
                                    ],
                                    'origin' => [
                                        'latitude' => $originCoordinates->latitude,
                                        'longitude' => $originCoordinates->longitude
                                    ],
                                    'destination' => [
                                        'latitude' => $destinationCoordinates->latitude,
                                        'longitude' => $destinationCoordinates->longitude
                                    ]
                                ]
                            ],
                            201
                        );
                    }
                    return response(
                        [
                            'message' => "The promo code is not valid for the requested origin or destination",
                            "status" => "failed"
                        ],
                        400
                    );
                }
                return response(
                    [
                        'message' => 'The promo code entered has been deactivated.',
                        'status' => 'failed'
                    ],
                    400
                );
            }
            return response(
                [
                    'message' => 'The promo code entered has expired.',
                    'status' => 'failed'
                ],
                400
            );
        } catch (ModelNotFoundException $e) {
            return response(
                [
                    'message' => 'No event exists with that id on our system',
                    'status' => 'failed'
                ],
                404
            );
        }
    }

    private function getCoordinatesFromRequest($request): array
    {
        $origin = $request->input('origin');
        $origin_latitude = $request->input('origin_latitude');
        $origin_longitude = $request->input('origin_longitude');
        $destination = $request->input('destination');
        $destination_latitude = $request->input('destination_latitude');
        $destination_longitude = $request->input('destination_longitude');
        if ($origin_latitude) {
            $originCoordinates = new Coordinate($origin_latitude, $origin_longitude);
        }
        if ($origin) {
            $originCoordinates = GeoCodingManager::getCoordinatesFromAddress($origin);
        }
        if ($destination_latitude) {
            $destinationCoordinates = new Coordinate($destination_latitude, $destination_longitude);
        }
        if ($destination) {
            $destinationCoordinates = GeoCodingManager::getCoordinatesFromAddress($destination);
        }
        return [$originCoordinates, $destinationCoordinates];
    }

    private function checkIfOriginOrDestinationIsWithinValidRadius($requestCoordinates, $promoCode): bool
    {
        $distanceManager = new DistanceManager($promoCode->event->latitude, $promoCode->event->longitude);
        [$originCoordinates, $destinationCoordinates] = $requestCoordinates;
        $originDistanceFromEvent = $distanceManager->calculateDistance($originCoordinates);
        $destinationDistanceFromEvent = $distanceManager->calculateDistance($destinationCoordinates);
        return $originDistanceFromEvent <= $promoCode->valid_radius || $destinationDistanceFromEvent <= $promoCode->valid_radius;
    }

    public function checkIfPromoCodeIsValid(PromoCode $promoCode): bool
    {
        if (!$promoCode->is_valid) {
            return false;
        }
        $expiresAt = Carbon::parse($promoCode->expires_at);
        if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
            return false;
        }
        return true;
    }
}
