<?php

namespace App\Game;

class BettingManager
{
    /**
     * @var int  $playerCoins Player's remaining coins.
     * @var int  $bankerCoins Banker's remaining coins.
     * @var int  $stake       Coins currently at stake.
     * @var int  $step        Minimum bet step.
     * @var bool $betting     As true if betting in on, otherwise false.
     * @var bool $hasBet      As true if there is an active bet, otherwise false.
     * @var bool $gameOver    As true if either the player or banker are out of coins, otherwise false.
     */
    private int $playerCoins = 0;
    private int $bankerCoins = 0;
    private int $stake = 0;
    private int $step = 0;
    private bool $betting = false;
    private bool $hasBet = false;
    private bool $gameOver = false;

    /**
     * Create new betting manager object.
     *
     * @param int $max  Coins go give player and banker to start with.
     * @param int $step Minimum step when betting.
     */
    public function __construct(int $max = 100, int $step = 5)
    {
        $this->playerCoins = $this->bankerCoins = $max;
        $this->step = $step;
    }

    /** @param bool $value As true if betting is on, otherwise false. */
    public function setBetting(bool $value): void
    {
        $this->betting = $value;
    }

    /** @return bool As true if betting is on, otherwise false. */
    public function isBetting(): bool
    {
        return $this->betting;
    }

    /**
     * Player places a bet and banker tries to match it.
     *
     * @param int $bet Amount of coins to bet. This becomes the stake.
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

    /** Give player the stake */
    public function playerWinsStake(): void
    {
        $this->playerCoins += $this->stake;
        $this->stake = 0;
        $this->hasBet = false;
        $this->gameOver = $this->bankerCoins <= 0;
    }

    /** Give banker the stake */
    public function bankerWinsStake(): void
    {
        $this->bankerCoins += $this->stake;
        $this->stake = 0;
        $this->hasBet = false;
        $this->gameOver = $this->playerCoins <= 0;
    }

    /**
     * Create and array of values to use as tick labels for range input.
     *
     * @return int[] As values for the different betting steps.
     */
    private function getInputRangeTicks(): array
    {
        return range($this->step, $this->playerCoins, $this->step);
    }

    /** @return mixed[] $sate As the current betting state. */
    public function getState(): array
    {
        $state = [
            'playerCoins' => $this->playerCoins,
            'bankerCoins' => $this->bankerCoins,
            'stake'       => $this->stake,
            'step'        => $this->step,
            'hasBet'      => $this->hasBet,
            'ticks'       => $this->getInputRangeTicks(),
            'betting'     => $this->betting,
            'gameOver'    => $this->gameOver,
        ];

        return $state;
    }
}
