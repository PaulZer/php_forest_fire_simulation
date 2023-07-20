<?php

namespace Paulzer\PhpForestFireSimulation\Entity\ForestCell\State;

use Paulzer\PhpForestFireSimulation\Entity\ForestCell\ForestCell;

class AliveForestCell extends ForestCellState
{

    public function __construct(ForestCell $forestCell)
    {
        parent::__construct($forestCell);
    }

    public function ignite(): void
    {
        $this->forestCell->changeState(new OnFireForestCell($this->forestCell));
    }

    public function reduceToAshes(): void
    {
        // Do nothing, because this forest cell is alive and must be ignited before turning into ashes
    }

    public function getStateStr(): string
    {
        return 'alive';
    }
}