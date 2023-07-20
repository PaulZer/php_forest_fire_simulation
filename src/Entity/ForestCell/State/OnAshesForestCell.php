<?php

namespace Paulzer\PhpForestFireSimulation\Entity\ForestCell\State;

class OnAshesForestCell extends ForestCellState
{

    public function ignite(): void
    {
        // Do nothing, because this forest cell is already on ashes
    }

    public function reduceToAshes(): void
    {
        // Do nothing, because this forest cell is already on ashes
    }

    public function getStateStr(): string
    {
        return 'on_ashes';
    }
}