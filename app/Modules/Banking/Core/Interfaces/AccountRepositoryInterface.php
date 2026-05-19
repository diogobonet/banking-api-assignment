<?php

namespace App\Modules\Banking\Core\Interfaces;

use App\Modules\Banking\Core\Domain\Account;

interface AccountRepositoryInterface
{
    /**
     * @param string $id
     * @return Account|null
     */
    public function findById(string $id): ?Account;

    /**
     * @param Account $account
     * @return void
     */
    public function save(Account $account): void;

    /**
     * @return void
     */
    public function reset(): void;
}
