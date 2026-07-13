<?php

namespace App\Exceptions;

use Exception;

class InsufficientPermissionException extends Exception
{
    public function __construct(string $permission = '', int $code = 403, ?\Throwable $previous = null)
    {
        $message = $permission
            ? "Insufficient permission: {$permission} is required."
            : 'Insufficient permission to perform this action.';

        parent::__construct($message, $code, $previous);
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->code);
    }
}
