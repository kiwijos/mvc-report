<?php

namespace App\Game;

use App\Game\ReceiveTrait;
use App\Game\PassInfoTrait;
use App\Game\BankerInterface;
use App\Game\ReceiverInterface;

class MediumBanker implements BankerInterface, ReceiverInterface
{
    use ReceiveTrait;

    use PassInfoTrait;

    /**
     * From a simple representation of a full deck,
     * remove cards matching the ones that the banker has draw this turn,
     * as well as any cards drawn in previous turns (suit does not matter).
     * 
     * @return int[] $deck Deck of values that the banker assumes are left.
     */
    private function getCardsLeft(): array
    {
        /** @var int[] $deck Simple representation of a full deck using plain numbers */
        $deck = [];
        foreach (range(1, 13) as $value) {
            $temp = array_fill(0, 4, $value);
            $deck = array_merge($deck, $temp);
        }

        /** @var Card[] $cardsToRemove All cards drawn by the banker this turn or removed in previous turns. */
        $cardsToRemove = array_merge($this->cards, $this->removedCards);

        // Remove from deck
        foreach ($cardsToRemove as $card) {
            if(($key = array_search($card->getRank(), $deck)) !== false) {
                unset($deck[$key]);
            }
        }

        return $deck;
    }

    /**
     * Returns whether or not the banker should keep hitting another card.
     * This banker does not assume anything about the cards drawn by the player this turn.
     * 
     * @return bool As true if the risk of bursting is less than O.5, otherwise false.
     */
    public function keepHitting(): bool
    {
        /** @var int $margin Number of points the banker can score before bursting. */
        $margin = 21 - $this->points;

        if ($margin > 13) {
            return true;    // No card will put the banker over 21, return early
        }

        /** @var int[] $cardsLeft Values of all cards that the bankers assumes are left. */
        $cardsLeft = $this->getCardsLeft();

        /** @var int[] $burstCards Values of all cards that will make the banker burst. */
        $burstCards = array_filter($cardsLeft, function($value) use ($margin){
            return $value > $margin;
        });
        
        /** @var int $burstRisk Risk of drawing over 21. */
        $burstRisk = count($burstCards) / count($cardsLeft);

        return $burstRisk <= 0.5;
    }
}
