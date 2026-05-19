<?php

namespace Tests\Unit\Banking;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Exceptions\InsufficientFundsException;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function test_deposit_increases_balance(): void
    {
        $account = new Account('1', 0);

        $account->deposit(50);

        $this->assertSame(50.0, $account->getBalance());
    }

    public function test_deposit_accumulates_correctly(): void
    {
        $account = new Account('1', 10);

        $account->deposit(5);
        $account->deposit(15);

        $this->assertSame(30.0, $account->getBalance());
    }

    public function test_withdraw_reduces_balance(): void
    {
        $account = new Account('1', 20);

        $account->withdraw(5);

        $this->assertSame(15.0, $account->getBalance());
    }

    public function test_withdraw_throws_when_insufficient_funds(): void
    {
        $account = new Account('1', 10);

        $this->expectException(InsufficientFundsException::class);

        $account->withdraw(20);
    }

    public function test_balance_remains_unchanged_after_failed_withdraw(): void
    {
        $account = new Account('1', 10);

        try {
            $account->withdraw(20);
        } catch (InsufficientFundsException) {
        }

        $this->assertSame(10.0, $account->getBalance());
    }
}
