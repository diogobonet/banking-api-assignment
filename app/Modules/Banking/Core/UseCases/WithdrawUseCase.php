<?php

namespace App\Modules\Banking\Core\UseCases;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Exceptions\AccountNotFoundException;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class WithdrawUseCase
{
    public function __construct(private readonly AccountRepositoryInterface $repository) {}

    /**
     * @param string $origin
     * @param float $amount
     * @return Account
     * @throws AccountNotFoundException
     */
    public function execute(string $origin, float $amount): Account
    {
        $account = $this->repository->findById($origin);

        if ($account === null) {
            throw new AccountNotFoundException($origin);
        }

        $account->withdraw($amount);
        $this->repository->save($account);

        return $account;
    }
}
