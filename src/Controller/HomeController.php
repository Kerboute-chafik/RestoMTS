<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $restos;
    public function __construct(RestaurantRepository $restos)
    {
        $this->restos = $restos;

    }
    /**
     * @Route("/home", name="app_home")
     */
    public function index(): Response
    {
        $restos = $this->restos->findAll();
        return $this->render('home/index.html.twig', [
            'restos' => $restos,
        ]);
    }
}
