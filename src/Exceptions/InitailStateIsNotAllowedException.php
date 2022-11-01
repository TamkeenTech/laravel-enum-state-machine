<?php

namespace TamkeenTech\LaravelEnumStateMachine\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InitailStateIsNotAllowedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
