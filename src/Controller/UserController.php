<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserAccount;
use App\Service\TransactionService;
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
     * @Route("/api/user/current", name="get_current_user")
     * @Rest\View()
     */
    public function getCurrentUser()
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $view = $this->view($currentUser, 200);

        return $this->handleView($view);
    }

    /**
     * @Route("/api/user/balance", name="get_current_balance")
     * @Rest\View()
     */
    public function getCurrentBalance()
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $view = $this->view($currentUser->getUserAccount()->getBalance(), 200);

        if ($currentUser->getUserAccount()->getStatus() == $currentUser->getUserAccount()::STATUS_BANNED) {
            throw new \Exception('You have benn banned');
        }

        return $this->handleView($view);
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
        $amount = floatval($request->get('amount'));

        $userFrom = $this->tokenStorage->getToken()->getUser();
        $userTo = $this->getDoctrine()->getRepository('App:User')->findOneBy(array('id' => $userToId));

        /** @var UserAccount $account */
        $account = $userTo->getUserAccount();
        if ($account->getStatus() == $account::STATUS_BANNED) {
            throw new \Exception('This user was banned');
        }
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

        $view = $this->view(true, 200);
        return $this->handleView($view);

    }
}
