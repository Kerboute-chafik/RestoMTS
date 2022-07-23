<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestoController extends AbstractController
{
    private $resto;
    public function __construct(RestaurantRepository $resto)
    {
        $this->resto = $resto;
    }

    /**
     * @Route("/resto/{id}/show", name="restaurant_show")
     */
    public function show(Restaurant $resto): Response
    {
        return $this->render('resto/show.html.twig', [
            'resto' => $resto,
        ]);
    }
}
