<?php


namespace App\Validators;


use App\Exceptions\InputValidationException;
use Illuminate\Support\Facades\Validator;

class ValidatorWrapper
{
    public static function validatorWrapper($request, $rules) {
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            throw new InputValidationException($validator->errors()->first());
        }
    }
}
