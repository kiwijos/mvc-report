<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route("/", name: "index")]
    public function index()
    {
        return $this->render('index.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about()
    {
        return $this->render('about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report()
    {
        return $this->render('report.html.twig');
    }

    #[Route("/work", name: "work")]
    public function work()
    {
        return $this->render('work.html.twig');
    }
}
