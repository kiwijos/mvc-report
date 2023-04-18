<?php

namespace App\Game;

class BettingManager
{
    private int $playerCoins = 0;
    private int $bankerCoins = 0;
    private int $stake = 0;
    private int $step = 0;
    private bool $betting = false;
    private bool $hasBet = false;
    private bool $gameOver = false;

    public function __construct(int $max = 100, int $step = 5)
    {
        $this->playerCoins = $this->bankerCoins = $max;
        $this->step = $step;
    }

    public function setBetting(bool $value): void
    {
        $this->betting = $value;
    }

    public function getBetting(): bool
    {
        return $this->betting;
    }

    /**
     * @param int $bet
     * 
     * @return bool As true if bet was successfully made, otherwise false.
     */
    public function placeBet(int $bet): bool
    {
        if ($bet > $this->playerCoins or $bet < $this->step) {
            $this->hasBet = false;

            return $this->hasBet;
        }

        $this->playerCoins -= $bet;
        $this->stake += $bet;

        if ($this->bankerCoins - $bet < 0) {
            $this->stake += $this->bankerCoins;
            $this->bankerCoins = 0;
        } else {
            $this->stake += $bet;
            $this->bankerCoins -= $bet;
        }

        $this->hasBet = true;

        return $this->hasBet;
    }

    public function playerWinsStake(): void
    {
        $this->playerCoins += $this->stake;
        $this->stake = 0;
        $this->hasBet = false;
        $this->gameOver = $this->bankerCoins <= 0;
    }

    public function bankerWinsStake(): void
    {
        $this->bankerCoins += $this->stake;
        $this->stake = 0;
        $this->hasBet = false;
        $this->gameOver = $this->playerCoins <= 0;
    }

    public function getState(): array
    {
        $state = [
            'playerCoins' => $this->playerCoins,
            'bankerCoins' => $this->bankerCoins,
            'stake'       => $this->stake,
            'step'        => $this->step,
            'hasBet'      => $this->hasBet,
            'ticks'       => range($this->step, $this->playerCoins, $this->step),
            'betting'     => $this->betting,
            'gameOver'    => $this->gameOver,
        ];

        return $state;
    }
}