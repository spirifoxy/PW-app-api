<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TransactionService;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends FOSRestController
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/user/transactions", name="get_user_transactions")
     * @Rest\View()
     */
    public function getUserTransactions(TransactionService $transactionService)
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $transactions = $this->getDoctrine()->getRepository('App:Transaction')->findAllForUser($currentUser);

        $view = $this->view($transactions, 200);

        $context = new Context();
        $context->setGroups(array('users_select_list'));
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Route("/api/users/select", name="get_users_for_select")
     * @Rest\View()
     */
    public function getUsersForSelectAction()
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $users = $this->getDoctrine()->getRepository('App:User')->findAllExcept($currentUser->getId());

        $view = $this->view($users, 200);

        $context = new Context();
        $context->setGroups(array('users_select_list'));
        $view->setContext($context);

        return $this->handleView($view);
    }


    /**
     * @Route("/api/user/sendMoney", name="send_money")
     * @Method("POST")
     */
    public function sendMoneyAction(Request $request, TransactionService $transactionService)
    {
        $userToId = $request->get('userId');
        $amount = $request->get('amount');

        $userFrom = $this->tokenStorage->getToken()->getUser();
        $userTo = $this->getDoctrine()->getRepository('App:User')->findOneBy(array('id' => $userToId));

        if ($amount < 1) {
            throw new \Exception('The transfer amount is incorrect');
        }
        if (!($userFrom instanceof User)) {
            throw new \Exception('Server error');
        }
        if (!$userTo) {
            throw new \Exception('Choose a user from the list');
        }

        try {
            $transactionService->sendMoney($userFrom, $userTo, $amount);
        } catch (\Exception $e) {
            throw $e;
        }

        return true;

    }
}
