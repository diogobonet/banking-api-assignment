<?php

namespace App\Modules\Banking\Core\Exceptions;

use RuntimeException;

class AccountNotFoundException extends RuntimeException
{
    public function __construct(string $accountId)
    {
        parent::__construct("Account {$accountId} not found");
    }
}
