<?php

namespace Paulzer\PhpForestFireSimulation\Entity\ForestCell\State;

use Paulzer\PhpForestFireSimulation\Entity\ForestCell\ForestCell;

abstract class ForestCellState
{
    protected ForestCell $forestCell;

    public function __construct(ForestCell $forestCell)
    {
        $this->forestCell = $forestCell;
    }

    abstract public function ignite(): void;

    abstract public function reduceToAshes(): void;

    abstract public function getStateStr(): string;
}