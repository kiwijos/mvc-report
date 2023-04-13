<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'number' => $number
        ];

        return $this->render('lucky.html.twig', $data);
    }

    #[Route("/api/quote", name: "quote")]
    public function jsonQuote(): JsonResponse
    {
        $quotes = [
            0 => "The early bird might get the worm, but the late bird gets to sleep in and have pancakes for breakfast.",
            1 => "I have always said that the key to happiness is a good pair of fuzzy socks and a tub of ice cream.",
            2 => "Always remember, when in doubt, hug a tree and it will give you the answer to any question you may have."
        ];

        $randKey = array_rand($quotes, 1);

        $data = [
            'quote' => $quotes[$randKey],
            'date' => date('l jS \of F Y G:i:s'),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
