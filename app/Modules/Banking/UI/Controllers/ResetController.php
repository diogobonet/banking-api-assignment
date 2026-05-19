<?php

namespace App\Modules\Banking\UI\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banking\Core\UseCases\ResetUseCase;
use Illuminate\Http\Response;

class ResetController extends Controller
{
    public function __construct(private readonly ResetUseCase $resetUseCase) {}

    /**
     * @return Response
     */
    public function reset(): Response
    {
        $this->resetUseCase->execute();

        return response('OK', 200);
    }
}
