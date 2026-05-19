<?php

namespace Tests\Unit\Banking;

use App\Modules\Banking\Core\UseCases\DepositUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Banking\Support\FakeAccountRepository;

class DepositUseCaseTest extends TestCase
{
    private FakeAccountRepository $repository;
    private DepositUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = new FakeAccountRepository();
        $this->useCase = new DepositUseCase($this->repository);
    }

    public function test_creates_account_when_it_does_not_exist(): void
    {
        $account = $this->useCase->execute('100', 10);

        $this->assertSame('100', $account->getId());
        $this->assertSame(10.0, $account->getBalance());
    }

    public function test_adds_amount_to_existing_account(): void
    {
        $this->useCase->execute('100', 10);

        $account = $this->useCase->execute('100', 15);

        $this->assertSame(25.0, $account->getBalance());
    }

    public function test_persists_account_state_after_deposit(): void
    {
        $this->useCase->execute('100', 10);

        $persisted = $this->repository->findById('100');

        $this->assertNotNull($persisted);
        $this->assertSame(10.0, $persisted->getBalance());
    }
}
