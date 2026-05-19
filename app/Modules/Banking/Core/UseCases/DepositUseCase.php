<?php

namespace App\Modules\Banking\Core\UseCases;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class DepositUseCase
{
    public function __construct(private readonly AccountRepositoryInterface $repository) {}

    /**
     * @param string $destination
     * @param float $amount
     * @return Account
     */
    public function execute(string $destination, float $amount): Account
    {
        $account = $this->repository->findById($destination) ?? new Account($destination, 0);

        $account->deposit($amount);
        $this->repository->save($account);

        return $account;
    }
}
