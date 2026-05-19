<?php

namespace Tests\Unit\Banking\Support;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class FakeAccountRepository implements AccountRepositoryInterface
{
    /** @var array<string, Account> */
    private array $accounts = [];

    /**
     * @param string $id
     * @return Account|null
     */
    public function findById(string $id): ?Account
    {
        return $this->accounts[$id] ?? null;
    }

    /**
     * @param Account $account
     * @return void
     */
    public function save(Account $account): void
    {
        $this->accounts[$account->getId()] = $account;
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->accounts = [];
    }
}
