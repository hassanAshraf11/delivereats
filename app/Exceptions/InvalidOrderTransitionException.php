<?php

namespace App\Exceptions;

use Exception;

class InvalidOrderTransitionException extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Invalid order transition from '{$from}' to '{$to}'.");
    }
}
