<?php

namespace App\Modules\Banking\Core\UseCases;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class GetBalanceUseCase
{
    public function __construct(private readonly AccountRepositoryInterface $repository) {}

    /**
     * @param string $accountId
     * @return Account|null
     */
    public function execute(string $accountId): ?Account
    {
        return $this->repository->findById($accountId);
    }
}
