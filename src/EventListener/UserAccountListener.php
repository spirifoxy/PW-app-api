<?php


namespace App\EventListener;


use App\Entity\UserAccount;
use App\Service\UserService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostPersist;

class UserAccountListener
{
    private $userService;

    public function __construct(UserService $userService)
    {
        if ($userService) {
            $this->userService = $userService;
        }
    }

    public function prePersist(UserAccount $account) {
        $date = new \DateTime();
        $account->setCreatedAt($date);
        $account->setUpdatedAt($date);

        $this->userService->initializeBalance($account->getUser());
    }

    public function preUpdate(UserAccount $account) {
        $account->setUpdatedAt(new \DateTime());
    }

}