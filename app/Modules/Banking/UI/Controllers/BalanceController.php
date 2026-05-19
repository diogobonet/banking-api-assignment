<?php

namespace App\Modules\Banking\UI\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banking\Core\UseCases\GetBalanceUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __construct(private readonly GetBalanceUseCase $getBalanceUseCase) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $accountId = $request->query('account_id');

        if ($accountId === null) {
            return response()->json(0, 404);
        }

        $account = $this->getBalanceUseCase->execute((string) $accountId);

        if ($account === null) {
            return response()->json(0, 404);
        }

        return response()->json($account->getBalance());
    }
}
