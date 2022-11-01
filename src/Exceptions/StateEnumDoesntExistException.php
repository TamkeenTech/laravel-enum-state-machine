<?php

namespace TamkeenTech\LaravelEnumStateMachine\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class StateEnumDoesntExistException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
