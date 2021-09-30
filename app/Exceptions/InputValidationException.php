<?php


namespace App\Exceptions;

use Exception;

class InputValidationException extends Exception
{
    public function render() {
        return response()->json(
            [
                'message' => $this->getMessage(),
                'status' => 'failed'
            ],
            400
        );
    }
}
