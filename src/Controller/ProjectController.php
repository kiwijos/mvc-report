<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game\Location;
use App\Repository\Game\LocationRepository;

class ProjectController extends AbstractController
{
    #[Route('/proj', name: 'proj')]
    public function index(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/about/database', name: 'proj_about_database')]
    public function aboutDatabase(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/api', name: 'proj_api')]
    public function jsonApi(LocationRepository $locationRepository): Response
    {
        $locations = $locationRepository->findAll();

        return $this->render('proj/api.html.twig', ['locations' => $locations]);
    }
}
