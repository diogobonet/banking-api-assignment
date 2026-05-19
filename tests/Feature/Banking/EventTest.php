<?php

namespace Tests\Feature\Banking;

use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;
use Tests\TestCase;

class EventTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(AccountRepositoryInterface::class)->reset();
    }

    public function test_reset_clears_all_accounts(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 10]);

        $response = $this->post('/reset');

        $response->assertStatus(200);
        $response->assertSee('OK');

        $this->getJson('/balance?account_id=100')->assertStatus(404);
    }

    public function test_deposit_creates_account_with_correct_balance(): void
    {
        $response = $this->postJson('/event', [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['destination' => ['id' => '100', 'balance' => 10]]);
    }

    public function test_deposit_adds_to_existing_account_balance(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 10]);

        $response = $this->postJson('/event', [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['destination' => ['id' => '100', 'balance' => 20]]);
    }

    public function test_deposit_persists_state_for_subsequent_balance_check(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 10]);

        $this->getJson('/balance?account_id=100')
            ->assertStatus(200)
            ->assertContent('10');
    }

    public function test_withdraw_reduces_account_balance(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 20]);

        $response = $this->postJson('/event', [
            'type' => 'withdraw',
            'origin' => '100',
            'amount' => 5,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['origin' => ['id' => '100', 'balance' => 15]]);
    }

    public function test_withdraw_from_non_existing_account_returns_404(): void
    {
        $response = $this->postJson('/event', [
            'type' => 'withdraw',
            'origin' => '200',
            'amount' => 10,
        ]);

        $response->assertStatus(404);
        $response->assertContent('0');
    }

    public function test_transfer_updates_origin_and_destination_balances(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 15]);
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '300', 'amount' => 15]);

        $response = $this->postJson('/event', [
            'type' => 'transfer',
            'origin' => '100',
            'destination' => '300',
            'amount' => 15,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => ['id' => '100', 'balance' => 0],
            'destination' => ['id' => '300', 'balance' => 30],
        ]);
    }

    public function test_transfer_creates_destination_account_if_not_exists(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 15]);

        $response = $this->postJson('/event', [
            'type' => 'transfer',
            'origin' => '100',
            'destination' => '300',
            'amount' => 15,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => ['id' => '100', 'balance' => 0],
            'destination' => ['id' => '300', 'balance' => 15],
        ]);
    }

    public function test_transfer_from_non_existing_origin_returns_404(): void
    {
        $response = $this->postJson('/event', [
            'type' => 'transfer',
            'origin' => '200',
            'destination' => '300',
            'amount' => 15,
        ]);

        $response->assertStatus(404);
        $response->assertContent('0');
    }

    public function test_get_balance_does_not_alter_account_state(): void
    {
        $this->postJson('/event', ['type' => 'deposit', 'destination' => '100', 'amount' => 10]);

        $this->getJson('/balance?account_id=100');
        $this->getJson('/balance?account_id=100');

        $this->getJson('/balance?account_id=100')
            ->assertStatus(200)
            ->assertContent('10');
    }
}
