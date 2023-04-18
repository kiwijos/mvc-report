<?php

namespace App\Game;

use App\Card\DeckOfCards;

class GameManager
{
    private $deck;
    private $player;
    private $banker;
    private $assistanceMode = true;
    private $removedCards = [];

    /** @var int $hasWon As 0 if no one has won, 1 if player has won or -1 if banker has won */
    private int $hasWon = 0;

    public function reset(): void
    {
        // Move cards dealt to array of removed cards
        $cardsToRemove = array_merge($this->player->getCards(), $this->banker->getCards());
        $this->removedCards = array_merge($this->removedCards, $cardsToRemove);

        // Reset player and banker points and cards
        $this->player->reset();
        $this->banker->reset();

        $this->hasWon = 0;
    }

    public function setAssistanceMode(bool $value): void
    {
        $this->assistanceMode = $value;
    }

    public function setDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    } 

    /** Set current player */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /** Set current banker */
    public function setBanker(BankerInterface $banker): void
    {
        $this->banker = $banker;
    }

    /**
     * Get the risk of the player scoring over 21 (bursting).
     * 
     * @return int As the risk of bursting on drawing another card.
     */
    public function getBurstRisk(): float
    {
        /** @var int $margin Number of points the player can score before bursting. */
        $margin = 21 - $this->player->getPoints();

        /** @var int[] $burstCards Values of all cards that will make the player burst. */
        $burstCards = array_filter($this->deck->getValues(), function($value) use ($margin) {
            return $value > $margin;
        });

        /** @var int $burstRisk */
        $burstRisk = round(count($burstCards) / $this->deck->getCount(), 2);

        return $burstRisk;
    }

    /**
     * Deal player a card.
     */
    public function dealPlayer(): void
    {
        $this->player->receive($this->deck->draw(1)[0]);

        $this->updateHasWonStatus();
    }

    /**
     * Deal banker cards until they decide to stop hitting.
     */
    public function dealBanker(): void
    {
        $this->informBanker();

        $keepHitting = true;
        while ($keepHitting === true) {
            $this->banker->receive($this->deck->draw(1)[0]);
            $keepHitting = $this->banker->keepHitting();
        }

        $this->updateHasWonStatus();
    }

    /**
     * Method of informing banker about current game state.
     */
    private function informBanker(): void
    {
        $this->banker->passInfo($this->removedCards, $this->player->getPoints());
    }

    public function getState(): array
    {
        $state = [
            'playerPoints' => $this->player->getPoints(),
            'playerCards' => $this->player->getCards(),
            'bankerPoints' => $this->banker->getPoints(),
            'bankerCards' => $this->banker->getCards(),
            'cardCount' => $this->deck->getCount(),
            'assistance' => $this->assistanceMode ? $this->getBurstRisk() * 100 : null,
            'hasWon' => $this->hasWon,
        ];

        return $state;
    }

    /**
     * Update who (if anyone) has won by checking their respective win conditions.
     */
    private function updateHasWonStatus(): void
    {
        $playerPoints = $this->player->getPoints();
        $bankerPoints = $this->banker->getPoints();

        if ($playerPoints === 21 or $bankerPoints > 21) {
            $this->hasWon = 1;
            return;
        } 
        
        if ($playerPoints > 21 or $bankerPoints >= $playerPoints) {
            $this->hasWon = -1;
            return;
        }

        if ($bankerPoints > 0 and $playerPoints > $bankerPoints) {
            $this->hasWon = 1;
            return;
        }

        $this->hasWon = 0;
    }
}
