<?php

namespace App\Modules\Banking\Core\UseCases;

use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;

class ResetUseCase
{
    public function __construct(private readonly AccountRepositoryInterface $repository) {}

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->repository->reset();
    }
}
