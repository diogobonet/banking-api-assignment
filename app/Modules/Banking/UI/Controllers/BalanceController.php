<?php

namespace App\Modules\Banking\UI\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banking\Core\UseCases\GetBalanceUseCase;
use App\Modules\Banking\UI\Requests\BalanceRequest;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function __construct(private readonly GetBalanceUseCase $getBalanceUseCase) {}

    /**
     * @param BalanceRequest $request
     * @return JsonResponse
     */
    public function show(BalanceRequest $request): JsonResponse
    {
        $account = $this->getBalanceUseCase->execute($request->validated('account_id'));

        if ($account === null) {
            return $this->notFound();
        }

        return response()->json($account->getBalance());
    }
}
