<?php

namespace Tests\Unit\Banking;

use App\Modules\Banking\Core\Domain\Account;
use App\Modules\Banking\Core\Exceptions\AccountNotFoundException;
use App\Modules\Banking\Core\Exceptions\InsufficientFundsException;
use App\Modules\Banking\Core\UseCases\WithdrawUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Banking\Support\FakeAccountRepository;

class WithdrawUseCaseTest extends TestCase
{
    private FakeAccountRepository $repository;
    private WithdrawUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = new FakeAccountRepository();
        $this->useCase = new WithdrawUseCase($this->repository);
    }

    public function test_reduces_balance_on_withdraw(): void
    {
        $this->repository->save(new Account('100', 20));

        $account = $this->useCase->execute('100', 5);

        $this->assertSame(15.0, $account->getBalance());
    }

    public function test_persists_updated_balance_after_withdraw(): void
    {
        $this->repository->save(new Account('100', 20));

        $this->useCase->execute('100', 5);

        $this->assertSame(15.0, $this->repository->findById('100')->getBalance());
    }

    public function test_throws_when_account_does_not_exist(): void
    {
        $this->expectException(AccountNotFoundException::class);

        $this->useCase->execute('999', 10);
    }

    public function test_throws_when_insufficient_funds(): void
    {
        $this->repository->save(new Account('100', 5));

        $this->expectException(InsufficientFundsException::class);

        $this->useCase->execute('100', 20);
    }
}
