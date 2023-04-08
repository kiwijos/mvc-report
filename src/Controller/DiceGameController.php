<?php

namespace App\Controller;
use App\Dice\Dice;
use App\Dice\DiceGraphic;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiceGameController extends AbstractController
{
    #[Route("/game/pig", name: "pig_index")]
    public function index(): Response
    {
        return $this->render('pig/index.html.twig');
    }

    #[Route("/game/pig/test/roll_many/{num<\d+>}", name: "test_roll_num_dice")]
    public function testRollNumDice(int $num): Response
    {
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
    
}
