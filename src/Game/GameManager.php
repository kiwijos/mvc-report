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

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffleCards();
    }

    public function cleanRound(): void
    {
        $cardsToRemove = array_merge($player->getCards(), $banker->getCards());
        $this->removedCards = array_merge($removedCards, $cardsToRemove);

        $this->gameOver = false;
    }

    public function setAssistanceMode(bool $value): void
    {
        $this->assistanceMode = $value;
    }

    /** @return Player As the current player */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /** Set current player */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /** @return BankerInterface As the current banker */
    public function getBanker(): BankerInterface
    {
        return $this->banker;
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
        $this->player->recieve($this->deck->draw(1)[0]);
    }

    /**
     * Deal banker cards until they decide to stop hitting.
     */
    public function dealBanker(): void
    {
        $this->informBanker();

        $keepHitting = true;
        while ($keepHitting === true) {
            $this->banker->recieve($this->deck->draw(1)[0]);
            $keepHitting = $this->banker->keepHitting();
        }
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
            'assistance' => $this->assistanceMode ? $this->getBurstRisk() * 100 : '',
        ];

        return $state;
    }
}
