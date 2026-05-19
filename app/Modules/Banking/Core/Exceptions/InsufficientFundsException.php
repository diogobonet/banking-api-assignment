<?php

namespace App\Modules\Banking\Core\Exceptions;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Insufficient funds');
    }
}
