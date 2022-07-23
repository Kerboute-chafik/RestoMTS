<?php

namespace App\Controller\Admin;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {

        return $this->render('Admin/index.html.twig', [

        ]);
    }
}
