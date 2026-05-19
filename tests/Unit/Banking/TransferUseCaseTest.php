<?php

namespace Tests\Unit\Banking;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Exceptions\AccountNotFoundException;
use App\Modules\Banking\Core\Exceptions\InsufficientFundsException;
use App\Modules\Banking\Core\UseCases\TransferUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Banking\Support\FakeAccountRepository;

class TransferUseCaseTest extends TestCase
{
    private FakeAccountRepository $repository;
    private TransferUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = new FakeAccountRepository();
        $this->useCase = new TransferUseCase($this->repository);
    }

    public function test_deducts_from_origin_and_credits_destination(): void
    {
        $this->repository->save(new Account('100', 20));
        $this->repository->save(new Account('300', 10));

        [$origin, $destination] = $this->useCase->execute('100', '300', 15);

        $this->assertSame(5.0, $origin->getBalance());
        $this->assertSame(25.0, $destination->getBalance());
    }

    public function test_creates_destination_account_when_it_does_not_exist(): void
    {
        $this->repository->save(new Account('100', 20));

        [$origin, $destination] = $this->useCase->execute('100', '300', 15);

        $this->assertSame(5.0, $origin->getBalance());
        $this->assertSame(15.0, $destination->getBalance());
    }

    public function test_persists_both_accounts_after_transfer(): void
    {
        $this->repository->save(new Account('100', 20));

        $this->useCase->execute('100', '300', 15);

        $this->assertSame(5.0, $this->repository->findById('100')->getBalance());
        $this->assertSame(15.0, $this->repository->findById('300')->getBalance());
    }

    public function test_throws_when_origin_does_not_exist(): void
    {
        $this->expectException(AccountNotFoundException::class);

        $this->useCase->execute('999', '300', 10);
    }

    public function test_throws_when_origin_has_insufficient_funds(): void
    {
        $this->repository->save(new Account('100', 5));

        $this->expectException(InsufficientFundsException::class);

        $this->useCase->execute('100', '300', 20);
    }

    public function test_destination_is_not_credited_when_origin_has_insufficient_funds(): void
    {
        $this->repository->save(new Account('100', 5));
        $this->repository->save(new Account('300', 10));

        try {
            $this->useCase->execute('100', '300', 20);
        } catch (InsufficientFundsException) {
        }

        $this->assertSame(10.0, $this->repository->findById('300')->getBalance());
    }
}
