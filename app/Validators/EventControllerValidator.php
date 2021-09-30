<?php

namespace App\Validators;

use App\Exceptions\InputValidationException;
use Illuminate\Http\Request;

trait EventControllerValidator
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
                'name' => ['required', 'string', 'bail'],
                'description' => ['required', 'string', 'bail'],
                'event_starts_at' => ['required', 'string', 'bail'],
                'event_ends_at' => ['required', 'string', 'bail'],
                'latitude' => ['numeric', 'bail', 'prohibits:location', 'required_with:longitude'],
                'longitude' => ['numeric', 'bail', 'prohibits:location', 'required_with:latitude'],
                'location' => ['bail', 'prohibits:latitude,longitude', 'required_without:longitude,latitude'],
            ]
        );
    }

    /**
     * @param Request $request
     * @throws InputValidationException
     */
    public function validateUpdateInput(Request $request)
    {
        ValidatorWrapper::validatorWrapper(
            $request,
            [
                'name' => ['nullable', 'string', 'bail'],
                'description' => ['nullable', 'string', 'bail'],
                'event_starts_at' => ['nullable', 'string', 'bail'],
                'event_ends_at' => ['nullable', 'string', 'bail'],
                'latitude' => ['numeric', 'nullable', 'bail', 'prohibits:location', 'required_with:longitude'],
                'longitude' => ['numeric', 'nullable', 'bail', 'prohibits:location', 'required_with:latitude'],
                'location' => ['bail', 'nullable', 'prohibits:latitude,longitude'],
            ]
        );
    }
}
