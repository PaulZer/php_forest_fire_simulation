<?php

namespace Paulzer\PhpForestFireSimulation\Entity;

use Paulzer\PhpForestFireSimulation\Entity\ForestCell\ForestCell;
use Paulzer\PhpForestFireSimulation\Entity\ForestCell\State\AliveForestCell;
use Paulzer\PhpForestFireSimulation\Service\SimulationParameters;

class ForestGrid
{
    private int $width;
    private int $height;
    private array $onFireCellsCoordinates = [];
    private array $forestCells = [];

    private int $nbOnAshesCells = 0;

    public function __construct(SimulationParameters $simulationParameters)
    {
        $this->width = $simulationParameters->getForestGridWidth();
        $this->height = $simulationParameters->getForestGridHeight();

        $this->onFireCellsCoordinates = $simulationParameters->getInitialFireCoordinates();

        $this->initialize();
    }

    private function initialize(): void
    {
        foreach(range(1, $this->width) as $x) {
            foreach (range(1, $this->height) as $y) {
                $forestCell = new ForestCell($x, $y);
                if (in_array(['x' => $x, 'y' => $y], $this->onFireCellsCoordinates)) {
                    $forestCell->ignite();
                }

                $this->forestCells[] = $forestCell;
            }
        }
    }

    public function iterate(float $propagationProbability): bool
    {
        foreach ($this->onFireCellsCoordinates as $forestCellCoord) {
            $forestCell = $this->getForestCell($forestCellCoord['x'], $forestCellCoord['y']);

            $neighbours = $this->getNeighbours($forestCell);
            $this->propagateFire($neighbours, $propagationProbability);

            $forestCell->reduceToAshes();

            $this->removeCellFromOnFireCells($forestCell);
            $this->nbOnAshesCells++;
        }

        return true;
    }

    public function hasOnFireCells(): bool
    {
        if(empty($this->onFireCellsCoordinates)){
            return false;
        }

        return true;
    }

    private function removeCellFromOnFireCells(ForestCell $forestCell): void
    {
        $key = array_search([
            'x' => $forestCell->getX(),
            'y' => $forestCell->getY()
        ], $this->onFireCellsCoordinates);

        if($key !== false){
            unset($this->onFireCellsCoordinates[$key]);
        }
    }

    public function getForestCells(): array
    {
        return $this->forestCells;
    }

    public function getForestCell(int $x, int $y): ForestCell
    {
        return $this->forestCells[$this->getForestCellIndex($x, $y)];
    }

    private function getNeighbours(ForestCell $forestCell): array
    {
        $neighbours = [];

        $x = $forestCell->getX();
        $y = $forestCell->getY();

        if ($x > 1) {
            $neighbours[] = $this->getForestCell($x - 1, $y);
        }

        if ($x < $this->width) {
            $neighbours[] = $this->getForestCell($x + 1, $y);
        }

        if ($y > 1) {
            $neighbours[] = $this->getForestCell($x, $y - 1);
        }

        if ($y < $this->height) {
            $neighbours[] = $this->getForestCell($x, $y + 1);
        }

        return $neighbours;
    }

    public function propagateFire(array $neighbours, float $probability): void
    {
        foreach($neighbours as $neighbour){

            /* @var $neighbour ForestCell */
            if($neighbour->getState() instanceof AliveForestCell){
                if(rand(0, 100)/100 <= $probability){
                    $neighbour->ignite();
                    $this->onFireCellsCoordinates[] = [
                        'x' => $neighbour->getX(),
                        'y' => $neighbour->getY()
                    ];
                }
            }
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getOnFireCellsCoordinates(): array
    {
        return $this->onFireCellsCoordinates;
    }

    public function getPercentageBurn(): float
    {
        return round((count($this->onFireCellsCoordinates) + $this->nbOnAshesCells)/($this->width * $this->height)*100, 2);
    }

    public function getForestCellIndex(int $x, int $y): int
    {
        return ($x - 1) * $this->height + $y - 1;
    }
}