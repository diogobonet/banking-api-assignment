<?php

namespace App\Modules\Banking\Infrastructure\Repositories;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class InMemoryAccountRepository implements AccountRepositoryInterface
{
    private const CACHE_KEY = 'ebanx_accounts';

    /**
     * @param string $id
     * @return Account|null
     */
    public function findById(string $id): ?Account
    {
        $accounts = Cache::get(self::CACHE_KEY, []);

        if (! isset($accounts[$id])) {
            return null;
        }

        return new Account($accounts[$id]['id'], $accounts[$id]['balance']);
    }

    /**
     * @param Account $account
     * @return void
     */
    public function save(Account $account): void
    {
        $accounts = Cache::get(self::CACHE_KEY, []);

        $accounts[$account->getId()] = [
            'id' => $account->getId(),
            'balance' => $account->getBalance(),
        ];

        Cache::put(self::CACHE_KEY, $accounts);
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
