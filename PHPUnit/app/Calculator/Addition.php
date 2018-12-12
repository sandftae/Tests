<?php

namespace App\Calculator;

use App\Calculator\Api\OperationInterface;
use App\Calculator\Exceptions\NoOperandException;
use App\Calculator\AbstractClasses\OperationAbstract;

/**
 * Class Addition
 * @package App\Calculator
 */
class Addition extends OperationAbstract implements OperationInterface
{
    /**
     * @return float|int|mixed
     * @throws NoOperandException
     */
    public function calculate()
    {
        if (empty($this->operands)) {
            throw new NoOperandException;
        }
        return array_sum($this->operands);
    }
}
