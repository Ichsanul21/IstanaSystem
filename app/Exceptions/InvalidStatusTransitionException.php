<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(string $message = 'Status transisi tidak valid')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return back()->with('error', $this->getMessage());
    }
}
