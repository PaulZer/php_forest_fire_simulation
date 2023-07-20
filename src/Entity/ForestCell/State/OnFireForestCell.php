<?php

namespace Paulzer\PhpForestFireSimulation\Entity\ForestCell\State;

use Paulzer\PhpForestFireSimulation\Entity\ForestCell\ForestCell;

class OnFireForestCell extends ForestCellState
{
    public function __construct(ForestCell $forestCell)
    {
        parent::__construct($forestCell);
    }

    public function ignite(): void
    {
        // Do nothing, because this forest cell is already on fire
    }

    public function reduceToAshes(): void
    {
        $this->forestCell->changeState(new OnAshesForestCell($this->forestCell));
    }

    public function getStateStr(): string
    {
        return 'on_fire';
    }
}