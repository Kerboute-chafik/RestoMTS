<?php

namespace App\Controller;

use App\Repository\CityRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $restos;
    private $city;
    public function __construct(RestaurantRepository $restos,CityRepository $city)
    {
        $this->restos = $restos;
        $this->city = $city;

    }
    /**
     * @Route("/home", name="app_home")
     */
    public function index(Request $request): Response
    {
        if ($request->get('city')) {
            $city = $this->city->findOneBy(['id' => $request->get('city')]);
            $restos = $this->restos->findByCity($city);
        } else {
            $restos = $this->restos->findLastInserted();
        }

        $cities = $this->city->findAll();
        return $this->render('home/index.html.twig', [
            'restos' => $restos,
            'cities' => $cities,
        ]);
    }
}
