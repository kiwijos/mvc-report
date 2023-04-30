<?php

namespace App\Game;

use App\Card\DeckOfCards;
use App\Card\Card;

class GameManager
{
    /** @var DeckOfCards $deck */
    private $deck;

    /** @var ReceiverInterface $player */
    private $player;

    /** @var BankerInterface $banker */
    private $banker;

    /** @var bool $assistanceMode As true if assistance mode is turned on, otherwise false. */
    private bool $assistanceMode = false;

    /** @var Card[] $removedCards All cards drawn in previous turns. */
    private array $removedCards = [];

    /** @var int $hasWon As 0 if no one has won, 1 if player has won or -1 if banker has won. */
    private int $hasWon = 0;

    /** @return int As 0 if no one has won, 1 if player has one or -1 if banker has won. */
    public function getHasWon(): int
    {
        return $this->hasWon;
    }

    /** @return bool As true if assistance mode is turned on, otherwise false. */
    public function hasAssistanceMode(): bool
    {
        return $this->assistanceMode;
    }

    /**
     * Move drawn cards to array of removed cards before also resetting player and banker.
     */
    public function reset(): void
    {
        /** @var Card[] $cardsToRemove Cards drawn by the player and banker this turn */
        $cardsToRemove = array_merge($this->player->getCards(), $this->banker->getCards());

        $this->removedCards = array_merge($this->removedCards, $cardsToRemove);

        // Reset player and banker points and cards
        $this->player->reset();
        $this->banker->reset();

        $this->hasWon = 0;
    }

    /** @param bool $value As true if mode should be turned on, otherwise false. */
    public function setAssistanceMode(bool $value): void
    {
        $this->assistanceMode = $value;
    }

    /** @param DeckOfCards $deck */
    public function setDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    /** @param ReceiverInterface $player */
    public function setPlayer(ReceiverInterface $player): void
    {
        $this->player = $player;
    }

    /** @param BankerInterface $banker */
    public function setBanker(BankerInterface $banker): void
    {
        $this->banker = $banker;
    }

    /**
     * Get the risk of the player scoring over 21 (bursting).
     *
     * @return float As the risk of bursting on drawing another card.
     */
    public function getBurstRisk(): float
    {
        /** @var int $margin Number of points the player can score before bursting. */
        $margin = 21 - $this->player->getPoints();

        if ($margin > 13) {
            return 0;         // No card will put he player over 21, return early
        }

        /** @var int[] $burstCards Values of all cards that will make the player burst. */
        $burstCards = array_filter($this->deck->getValues(), function ($value) use ($margin) {
            return $value > $margin;
        });

        /** @var float $burstRisk Risk of drawing over 21. */
        $burstRisk = round(count($burstCards) / $this->deck->getCount(), 2);

        return $burstRisk;
    }

    /**
     * Deal player a card.
     */
    public function dealPlayer(): void
    {
        $this->player->receive($this->deck->draw(1)[0]);
        $this->refillEmptyDeck();
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

            $this->refillEmptyDeck();
        }
    }

    /**
     * Method of informing banker about current game state.
     */
    private function informBanker(): void
    {
        $this->banker->passInfo($this->removedCards, $this->player->getPoints());
    }

    /** @return mixed[] As the current game state. */
    public function getState(): array
    {
        $state = [
            'playerPoints' => $this->player->getPoints(),
            'playerCards'  => $this->player->getCards(),
            'bankerPoints' => $this->banker->getPoints(),
            'bankerCards'  => $this->banker->getCards(),
            'cardCount'    => $this->deck->getCount(),
            'assistance'   => $this->assistanceMode ? $this->getBurstRisk() * 100 : null,
            'hasWon'       => $this->hasWon,
        ];

        return $state;
    }

    /**
     * Update who (if anyone) has won by checking their respective win conditions.
     */
    public function updateHasWonStatus(): void
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

    /**
     * Fill deck with removed cards if empty.
     */
    private function refillEmptyDeck(): void
    {
        if ($this->deck->getCount() > 0) {
            return;     // Deck is not empty, do nothing and return early
        }

        // Add removed cards back in the deck
        foreach ($this->removedCards as $card) {
            $this->deck->addCard($card);
        }

        // Shuffle deck
        $this->deck->shuffleCards();

        // The removed cards are back in the deck, inform banker about it
        $this->removedCards = [];
        $this->informBanker();
    }
}
