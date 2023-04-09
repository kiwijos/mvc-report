<?php

namespace App\Controller;
use App\Card\DeckOfCards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card_index")]
    public function index(SessionInterface $session): Response
    {
        $session->set('currentDeck', $session->get('currentDeck', new DeckOfCards()));
    
        return $this->render('card/index.html.twig');
    }

    #[Route("/card/deck", name: "show_ordered")]
    public function showOrdered(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $session->set('currentDeck', $deck);

        $data = [
            "deck" => $deck->getString(),
        ];

        return $this->render('card/test/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "show_shuffled")]
    public function showShuffled(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
    
        $deck->shuffleCards();

        $session->set('currentDeck', $deck); // Save shuffled deck
    
        $data = [
            "deck" => $deck->getString(),
        ];

        return $this->render('card/test/deck.html.twig', $data);
    }

    #[Route("/card/deck/draw/{number<\d+>}", name: "card_draw_many")]
    public function cardDrawMany(SessionInterface $session, int $number): Response
    {
        $deck = $session->get('currentDeck', new DeckOfCards());

        $draw = array_map('strval', $deck->draw($number));
    
        $session->set('currentDeck', $deck); // Save updated deck

        $data = [
            "draw"  => $draw,
            "count" => $deck->getCount(), 
        ];

        return $this->render('card/test/draw.html.twig', $data);
    }

    #[Route("/card/deck/draw", name: "card_draw")]
    public function cardDraw(SessionInterface $session): Response
    {
        $deck = $session->get('currentDeck', new DeckOfCards());

        $draw = array_map('strval', $deck->draw(1));

        $session->set('currentDeck', $deck); // Save updated deck
    
        $data = [
            "draw"  => $draw,
            "count" => $deck->getCount(), 
        ];

        return $this->render('card/test/draw.html.twig', $data);
    }

    #[Route("card/deck/deal/{players<\d+>}/{cards<\d+>}", name: "card_deal")]
    public function cardDeal(SessionInterface $session, int $players, int $cards): Response
    {
        $deck = $session->get('currentDeck', new DeckOfCards());
    
        $deal = [];

        for ($i = 0; $i < $players; $i++) {
            $deal[] = array_map('strval', $deck->draw($cards));
        }

        $session->set('currentDeck', $deck); // Save updated deck
    
        $data = [
            "deal"  => $deal,
            "count" => $deck->getCount(), 
        ];

        return $this->render('card/test/deal.html.twig', $data);
    }
}
