<?php

namespace App\Calculator;

use App\Calculator\Api\OperationInterface;

/**
 * Class Calculator
 * @package App\Calculator
 */
class Calculator implements OperationInterface
{
    /**
     * @var array
     */
    protected $operations = [];

    /**
     * @param OperationInterface $operation
     */
    public function setOperation(OperationInterface $operation): void
    {
        $this->operations[] = $operation;
    }

    /**
     * @param array $data
     */
    public function setOperations(array $data): void
    {
        $filterOperations = array_filter($data, function ($operation) {
            return $operation instanceof OperationInterface;
        });

        $this->operations = array_merge($this->operations, $filterOperations);
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @return array
     */
    public function calculate()
    {
        if (count($this->operations) > 1) {
            return array_map(function ($operand) {
                return $operand->calculate();
            }, $this->operations);

        }
        return $this->operations[0]->calculate();
    }
}
