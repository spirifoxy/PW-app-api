<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends FOSRestController
{

    private $lexikJwtAuthentication;

    public function __construct(AuthenticationSuccessHandler $lexikJwtAuthentication)
    {
        $this->lexikJwtAuthentication = $lexikJwtAuthentication;
    }

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


        if ($this->getDoctrine()->getRepository('App:User')->isUserExists($username)){
            throw new BadRequestHttpException(sprintf('User with email %s already exists', $username));
        }

        $user = new User();
        $user->setName($name);
        $user->setUsername($username);
        $user->setPlainPassword($password);

        /** @var ConstraintViolation[] $errors */
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($errors[0]->getMessage());
        }

        $user->setPassword($encoder->encodePassword($user, $password));

        $em->persist($user);
        $em->flush();

        return $this->lexikJwtAuthentication->handleAuthenticationSuccess($user);
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