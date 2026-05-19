<?php

namespace App\Modules\Banking\Core\Domain;

use App\Modules\Banking\Core\Exceptions\InsufficientFundsException;

class Account
{
    public function __construct(
        private readonly string $id,
        private float $balance,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientFundsException();
        }

        $this->balance -= $amount;
    }
}
