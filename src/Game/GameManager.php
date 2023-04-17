<?php

namespace App\Game;

use App\Card\DeckOfCards;

class GameManager
{
    private $deck;
    private $player;
    private $banker;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffleCards();
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
        $keepHitting = true;
        while ($keepHitting === true) {
            $this->banker->recieve($this->deck->draw(1)[0]);
            $keepHitting = $this->banker->keepHitting();
        }
    }

    public function getState(): array
    {
        $state = [
            'playerPoints' => $this->player->getPoints(),
            'playerCards' => $this->player->getCards(),
            'bankerPoints' => $this->banker->getPoints(),
            'bankerCards' => $this->banker->getCards(),
            'cardCount' => $this->deck->getCount(),
        ];

        return $state;
    }
}