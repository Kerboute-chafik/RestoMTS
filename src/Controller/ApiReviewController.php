<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class ApiReviewController extends AbstractController
{
    private $resto;
    private $review;
    private $user;
    public function __construct(RestaurantRepository $resto,ReviewRepository $review,UserRepository $user)
    {
        $this->resto = $resto;
        $this->review = $review;
        $this->user = $user;
    }

    /**
     * @Route("/api/review", name="app_api_review_store", methods={"POST"})
     */
    public function sotre(Request $request,Security $security,EntityManagerInterface $em,SerializerInterface $serializer): Response
    {
       $request = $this->transformJsonBody($request);
        $message = $request->get('message');
        $note = $request->get('note');
        $restaurant = $request->get('restaurant');
        $user = $request->get('user');
        $header = $this->getBearerToken();

         $review = new Review();
        $review->setMessage($message);
        $review->setNote($note);
        $review->setRestaurant($this->resto->findOneBy(['id' => (int)$restaurant]));
        $review->setUser($this->user->findOneBy(['email' => $user]));
        $review->setCreatedAt(new \DateTimeImmutable());

      $em->persist($review);
      $em->flush();
      return $this->json($review,201,[],['groups' => 'review:read']);
    }

    /**
     * @Route("/api/review", name="app_api_review_delete", methods={"DELETE"})
     */
    public function delete(Request $request,EntityManagerInterface $entityManager,ReviewRepository $reviewRepository): Response
    {
        $request = $this->transformJsonBody($request);
        $review = $this->review->findOneBy(['id' => (int)$request->get('id')]);

       $entityManager->remove($review);
            $entityManager->flush();


        return new JsonResponse('deleted with success');
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
    /**
     * get access token from header
     * */
    function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    /**
     * Get header Authorization
     * */
    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}
