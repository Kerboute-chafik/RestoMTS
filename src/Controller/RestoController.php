<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RestoController extends AbstractController
{
    private $resto;
    private $review;
    private $em;
    private $security;

    public function __construct(RestaurantRepository $resto,EntityManagerInterface $em,Security $security,ReviewRepository $review)
    {
        $this->resto = $resto;
        $this->review = $review;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/resto/{id}/show", name="restaurant_show")
     */
    public function show(Restaurant $resto): Response
    {
        $note = $this->review->avgNote($resto);
        $reviews = $this->review->findBy(['restaurant' => $resto]) ;

        return $this->render('resto/show.html.twig', [
            'resto' => $resto,
            'note' => $note,
            'reviews' => $reviews
        ]);
    }

    /**
     * @Route("/resto/{id}/review", name="restaurant_add_review")
     */
    public function addReview(Request $request,Restaurant $resto): Response
    {
         $user = $this->security->getUser();
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
         if ($user){

             if ($form->isSubmitted()) {

                 $user = $this->security->getUser();
                 $review->setUser($user);
                 $review->setCreatedAt(new \DateTimeImmutable());
                 $review->setMessage($request->get('review')['message']);
                 $review->setNote($request->get('review')['note']);
                 $review->setRestaurant($resto);
                 $this->em->persist($review);
                 $this->em->flush();

                 return $this->redirectToRoute('restaurant_show', ['id' => $resto->getId()], Response::HTTP_SEE_OTHER);
             }
         }
       else{
           return $this->redirectToRoute('app_login', []);
       }
       return $this->render('resto/review.html.twig',[
           'resto' => $resto,

       ]);
    }
}
