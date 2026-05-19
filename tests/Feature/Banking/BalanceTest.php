<?php

namespace Tests\Feature\Banking;

use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(AccountRepositoryInterface::class)->reset();
    }

    public function test_returns_404_for_non_existing_account(): void
    {
        $response = $this->getJson('/balance?account_id=1234');

        $response->assertStatus(404);
        $response->assertContent('0');
    }

    public function test_returns_balance_for_existing_account(): void
    {
        $this->postJson('/event', [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 20,
        ]);

        $response = $this->getJson('/balance?account_id=100');

        $response->assertStatus(200);
        $response->assertContent('20');
    }

    public function test_returns_404_when_account_id_is_missing(): void
    {
        $response = $this->getJson('/balance');

        $response->assertStatus(404);
        $response->assertContent('0');
    }
}
