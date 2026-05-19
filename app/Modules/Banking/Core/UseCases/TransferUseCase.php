<?php

namespace App\Modules\Banking\Core\UseCases;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Exceptions\AccountNotFoundException;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class TransferUseCase
{
    public function __construct(private readonly AccountRepositoryInterface $repository) {}

    /**
     * @param string $origin
     * @param string $destination
     * @param float $amount
     * @return array{Account, Account}
     * @throws AccountNotFoundException
     */
    public function execute(string $origin, string $destination, float $amount): array
    {
        $originAccount = $this->repository->findById($origin);

        if ($originAccount === null) {
            throw new AccountNotFoundException($origin);
        }

        $destinationAccount = $this->repository->findById($destination) ?? new Account($destination, 0);

        $originAccount->withdraw($amount);
        $destinationAccount->deposit($amount);

        $this->repository->save($originAccount);
        $this->repository->save($destinationAccount);

        return [$originAccount, $destinationAccount];
    }
}
