<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/admin/review")
 */
class ReviewController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em,Security $security)
    {
        $this->em = $em;
        $this->security = $security;

    }
    /**
     * @Route("/", name="app_review_index", methods={"GET"})
     */
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findBy(['user' => $this->security->getUser()]),
        ]);
    }

    /**
     * @Route("/new", name="app_review_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReviewRepository $reviewRepository): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->security->getUser();
            $review->setUser($user);
            $review->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($review);
            $this->em->flush();

            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_review_show", methods={"GET"})
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_review_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review, true);

            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_review_delete", methods={"POST"})
     */
    public function delete(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
        }

        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
