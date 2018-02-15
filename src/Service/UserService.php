<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\UserAccount;

class UserService
{
    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @param User $user
     */
    public function initializeBalance($user) {
        $this->transactionService->createTransaction($user,UserAccount::INITIAL_BALANCE_VALUE);
    }

}