<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionService
{

    private $tokenStorage;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }


    public function sendMoney(int $toUserId, int $amount) {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if (!($user instanceof User)) {
            throw new BadRequestHttpException('Server error');
        }

        /** @var User $toUser */
        $toUser = $this->em->getRepository('App:User')->findOneBy(array('id' => $toUserId));
        if (!$toUser) {
            throw new BadRequestHttpException('Select user');
        }

        $this->em->beginTransaction();
        try{
            $operation = new Operation();
            $toUser->getUserAccount()->addTransaction($amount, $operation);
            $user->getUserAccount()->addTransaction($amount * -1, $operation);


            $this->em->persist($operation);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollBack();
            throw $e; //TODO check exceptions from addTransaction()
        }
        return true;
    }

}