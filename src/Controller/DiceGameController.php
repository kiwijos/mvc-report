<?php

namespace App\Controller;
use App\Dice\Dice;
use App\Dice\DiceGraphic;
use App\Dice\DiceHand;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DiceGameController extends AbstractController
{
    #[Route("/game/pig", name: "pig_index")]
    public function index(): Response
    {
        return $this->render('pig/index.html.twig');
    }

    #[Route("/game/pig/init", name: "pig_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('pig/init.html.twig');
    }

    #[Route("/game/pig/init", name: "pig_init_post", methods: ['POST'])]
    public function initCallback(Request $request, SessionInterface $session): Response
    {
        $numDice = $request->request->get('num_dice');

        // Create hand object with given number of dice
        $hand = new DiceHand();
        for ($i = 1; $i <= $numDice; $i++) {
            $hand->add(new DiceGraphic());
        }
        $hand->roll();

        // Set up session variables
        $session->set("pig_dicehand", $hand);
        $session->set("pig_dice", $numDice);
        $session->set("pig_round", 0);
        $session->set("pig_total", 0);

        return $this->redirectToRoute('pig_play');
    }

    #[Route("/game/pig/play", name: "pig_play", methods: ['GET'])]
    public function play(SessionInterface $session): Response
    {
        $hand = $session->get("pig_dicehand");

        $data = [
            "pigDice" => $session->get("pig_dice"),
            "pigRound" => $session->get("pig_round"),
            "pigTotal" => $session->get("pig_total"),
            "diceValues" => $hand->getString(),
        ];

        return $this->render('pig/play.html.twig', $data);
    }

    #[Route("/game/pig/roll", name: "pig_roll", methods: ['POST'])]
    public function roll(): Response
    {
        // Logic to roll the dice

        return $this->redirectToRoute('pig_play');
    }

    #[Route("/game/pig/save", name: "pig_save", methods: ['POST'])]
    public function save(): Response
    {
        // Logic to save the round

        return $this->redirectToRoute('pig_init_get');
    }

    #[Route("/game/pig/test/roll_many/{num<\d+>}", name: "test_roll_num_dice")]
    public function testRollNumDice(int $num): Response
    {
        if ($num > 99) {
            throw new \Exception("Can not roll more than 99 dice!");
        }

        $die = new DiceGraphic();

        $rolls = [];

        for ($i = 0; $i < $num; $i++) {
            $die->roll();
            array_push($rolls, $die->getAsString());
        }

        $data = [
            "numDice" => $num,
            "diceRoll" => $rolls,
        ];

        return $this->render('pig/test/roll_many.html.twig', $data);
    }

    #[Route("/game/pig/test/roll", name: "test_roll_dice")]
    public function testRollDice(): Response
    {
        $die = new Dice();

        $data = [
            "dice" => $die->roll(),
            "diceString" => $die->getAsString(),
        ];

        return $this->render('pig/test/roll.html.twig', $data);
    }
    
    #[Route("/game/pig/test/dicehand/{num<\d+>}", name: "test_dicehand")]
    public function testDiceHand(int $num): Response
    {
        if ($num > 99) {
            throw new \Exception("Can not roll more than 99 dice!");
        }

        $hand = new DiceHand();
        for ($i = 1; $i <= $num; $i++) {
            if ($i % 2 === 1) {
                $hand->add(new DiceGraphic());
            } else {
                $hand->add(new Dice());
            }
        }

        $hand->roll();

        $data = [
            "numDice" => $hand->getNumberDice(),
            "diceRoll" => $hand->getString(),
        ];

        return $this->render('pig/test/dicehand.html.twig', $data);
    }
}
