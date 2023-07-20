<?php

namespace Paulzer\PhpForestFireSimulation\Entity\ForestCell;

use Paulzer\PhpForestFireSimulation\Entity\ForestCell\State\AliveForestCell;
use Paulzer\PhpForestFireSimulation\Entity\ForestCell\State\ForestCellState;

class ForestCell
{
    protected int $x;
    protected int $y;

    protected ForestCellState $state;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;

        $this->state = new AliveForestCell($this);
    }

    public function changeState(ForestCellState $state): void
    {
        $this->state = $state;
    }

    public function ignite(): void
    {
        $this->state->ignite();
    }

    public function reduceToAshes(): void
    {
        $this->state->reduceToAshes();
    }

    public function getState(): ForestCellState
    {
        return $this->state;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}