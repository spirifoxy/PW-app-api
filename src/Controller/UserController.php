<?php

namespace App\Controller;

use App\Service\TransactionService;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends FOSRestController
{

    /**
     * @Route("/api/users/select", name="get_users_for_select")
     * @Rest\View()
     */
    public function getUsersForSelectAction()
    {

        $users = $this->getDoctrine()->getRepository('App:User')->findBy([], [ 'name' => 'ASC' ]);

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

        try {
            $transactionService->sendMoney($request->get('userId'), $request->get('amount'));
        } catch (\Exception $e) {
            throw $e;
        }

        return true;

    }
}
