<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login_check", name="login")
     */
    public function login(Request $request)
    {
    }

    /**
     * @Route("/api/register", name="register")
     * @Method("POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $username = $request->request->get('username');
        $password = $request->request->get('password');

            $user = new User();
            $user->setName($name);
            $user->setUsername($username);
            $user->setPassword($encoder->encodePassword($user, $password));

            $errors = $validator->validate($user);
            if (count($errors) > 0) {

                $errorsString = (string)$errors;
                return new Response($errorsString);
            }

        $em->persist($user);
        $em->flush();
        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }


    /**
     * @Route("/api/login_check", name="login_check")
     * @Method("POST")
     */
    public function loginCheck()
    {
    }



    /**
     * @Route("/api", name="api")
     */
    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
}