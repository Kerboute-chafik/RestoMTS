<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Guard\ApiLoginAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AuthController extends ApiLoginAuthenticator
{
    private $user;
    private $encoder;
    public function __construct(UserRepository $user,UserPasswordEncoderInterface $encoder)
    {
        $this->user = $user;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');
        $first = $request->get('firstname');
        $last = $request->get('lastname');

        if (empty($username) || empty($password) || empty($email)){
            return $this->respondValidationError("Invalid Username or Password or Email");
        }

        $user = new User($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setFirstName($first);
        $user->setLastName($last);
        $user->setUserNames($username);
        $em->persist($user);
        $em->flush();
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUserIdentifier()));
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('username', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    /**
     * @param Security $security
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserPasswordHasherInterface $userPasswordHasher,Request $request,Security $security, JWTTokenManagerInterface $JWTManager)
    {
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $user  = $this->user->findOneBy(['email' => $username]);
        if($user){
            if($userPasswordHasher->isPasswordValid($user, $password))
            {
                return new JsonResponse(['token' => $JWTManager->create($user)]);
            }
            else{
                return new JsonResponse('invalid credentials');
            }
        }
        else{
            return new JsonResponse('invalid credentials');
        }

    }

}