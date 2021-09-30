<?php

namespace App\Validators;

use App\Exceptions\InputValidationException;
use Illuminate\Http\Request;

trait PromoCodeControllerValidator
{
    /**
     * @param Request $request
     * @throws InputValidationException
     */
    public function validateCreationInput(Request $request)
    {
        ValidatorWrapper::validatorWrapper(
            $request,
            [
                'code' => ['required', 'string', 'bail'],
                'event_id' => ['required', 'numeric', 'bail'],
                'amount' => ['required', 'bail', 'numeric'],
                'valid_radius' => ['required', 'numeric', 'bail'],
                'expires_at' => ['required', 'string', 'bail'],
            ]
        );
    }

    /**
     * @param Request $request
     * @throws InputValidationException
     */
    public function validateRadiusConfigurationInput(Request $request)
    {
        ValidatorWrapper::validatorWrapper(
            $request,
            [
                'valid_radius' => ['required', 'numeric', 'bail']
            ]
        );
    }

    /**
     * @param Request $request
     * @throws InputValidationException
     */
    public function validateUsePromoCodeInput(Request $request)
    {
        ValidatorWrapper::validatorWrapper($request, [
            'origin' => [
                'string',
                'prohibits:origin_latitude,origin_longitude',
                'required_without:origin_latitude,origin_longitude',
                'bail'
            ],
            'origin_latitude' => [
                'numeric',
                'prohibits:origin',
                'required_with:origin_longitude',
                'required_without:origin',
                'bail'
            ],
            'origin_longitude' => [
                'numeric',
                'prohibits:origin',
                'required_with:origin_latitude',
                'required_without:origin',
                'bail'
            ],
            'destination' => [
                'string',
                'prohibits:destination_latitude,destination_longitude',
                'required_without:destination_latitude,destination_longitude',
                'bail'
            ],
            'destination_latitude' => [
                'numeric',
                'prohibits:destination',
                'required_with:destination_longitude',
                'required_without:destination',
                'bail'
            ],
            'destination_longitude' => [
                'numeric',
                'prohibits:destination',
                'required_with:destination_latitude',
                'required_without:destination',
                'bail'
            ],
            'code' => ['string', 'required', 'bail'],
        ]);
    }
}
