<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param $amount
     * @param null $operation
     * @return Transaction
     */
    public function createTransaction($user, $amount, $operation = null)
    {
        $this->assertTransactionAllowed($user, $amount);

        $account = $user->getUserAccount();
//        $transaction = new Transaction($account, $amount);
        if (!$operation) {
            $operation = new Operation();
        }
//        $transaction->setOperation($operation);

//        $account->addTransaction($transaction);
        $account->addTransaction($amount, $operation);

//        return $transaction;
    }

    /**
     * @param User $user
     * @param int $amount
     */
    private function assertTransactionAllowed($user, $amount)
    {
        $account = $user->getUserAccount();
        if ($account->getStatus() == $account::STATUS_BANNED) {
            throw new \Exception("You are not allowed to perform any operations");
        }

        if ($account->getBalance() + $amount < 0) {
            throw new \Exception("You don't have enough PW to do that");
        }
    }

    public function sendMoney(User $userFrom, User $userTo, int $amount) {

        $this->em->beginTransaction();
        try{
            $operation = new Operation();

            $this->createTransaction($userFrom,$amount * -1, $operation);
            $this->createTransaction($userTo, $amount, $operation);

            $this->em->persist($operation);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollBack();
            throw $e;
        }
        return true;
    }


}