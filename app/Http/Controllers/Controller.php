<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * @return JsonResponse
     */
    protected function notFound(): JsonResponse
    {
        return response()->json(0, 404);
    }

    /**
     * @return JsonResponse
     */
    protected function insufficientFunds(): JsonResponse
    {
        return response()->json(0, 422);
    }

    /**
     * @param array<string, mixed> $data
     * @return JsonResponse
     */
    protected function created(array $data): JsonResponse
    {
        return response()->json($data, 201);
    }
}
