<?php

namespace App\Controller;
use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\DeckOfCards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card_index")]
    public function index(): Response
    {
        return $this->render('card/index.html.twig');
    }

    #[Route("/card/deck", name: "show_ordered")]
    public function showOrdered(): Response
    {
        $deck = new DeckOfCards();

        foreach (range(0, 3) as $suite) {
            foreach (range(0, 12) as $rank) {
                $deck->addCard(new CardGraphic($suite, $rank));
            }
        }
    
        $data = [
            "deck" => $deck->getString(),
        ];
        return $this->render('card/deck.html.twig', $data);
    }
}
