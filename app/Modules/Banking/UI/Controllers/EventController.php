<?php

namespace App\Modules\Banking\UI\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banking\Core\Exceptions\AccountNotFoundException;
use App\Modules\Banking\Core\Exceptions\InsufficientFundsException;
use App\Modules\Banking\Core\UseCases\DepositUseCase;
use App\Modules\Banking\Core\UseCases\TransferUseCase;
use App\Modules\Banking\Core\UseCases\WithdrawUseCase;
use App\Modules\Banking\UI\Requests\EventRequest;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly DepositUseCase $depositUseCase,
        private readonly WithdrawUseCase $withdrawUseCase,
        private readonly TransferUseCase $transferUseCase,
    ) {}

    /**
     * @param EventRequest $request
     * @return JsonResponse
     */
    public function store(EventRequest $request): JsonResponse
    {
        return match ($request->validated('type')) {
            'deposit'  => $this->handleDeposit($request),
            'withdraw' => $this->handleWithdraw($request),
            'transfer' => $this->handleTransfer($request),
        };
    }

    /**
     * @param EventRequest $request
     * @return JsonResponse
     */
    private function handleDeposit(EventRequest $request): JsonResponse
    {
        $account = $this->depositUseCase->execute(
            (string) $request->validated('destination'),
            (float) $request->validated('amount'),
        );

        return $this->created([
            'destination' => ['id' => $account->getId(), 'balance' => $account->getBalance()],
        ]);
    }

    /**
     * @param EventRequest $request
     * @return JsonResponse
     */
    private function handleWithdraw(EventRequest $request): JsonResponse
    {
        try {
            $account = $this->withdrawUseCase->execute(
                (string) $request->validated('origin'),
                (float) $request->validated('amount'),
            );

            return $this->created([
                'origin' => ['id' => $account->getId(), 'balance' => $account->getBalance()],
            ]);
        } catch (AccountNotFoundException) {
            return $this->notFound();
        } catch (InsufficientFundsException) {
            return $this->insufficientFunds();
        }
    }

    /**
     * @param EventRequest $request
     * @return JsonResponse
     */
    private function handleTransfer(EventRequest $request): JsonResponse
    {
        try {
            [$origin, $destination] = $this->transferUseCase->execute(
                (string) $request->validated('origin'),
                (string) $request->validated('destination'),
                (float) $request->validated('amount'),
            );

            return $this->created([
                'origin'      => ['id' => $origin->getId(), 'balance' => $origin->getBalance()],
                'destination' => ['id' => $destination->getId(), 'balance' => $destination->getBalance()],
            ]);
        } catch (AccountNotFoundException) {
            return $this->notFound();
        } catch (InsufficientFundsException) {
            return $this->insufficientFunds();
        }
    }
}
