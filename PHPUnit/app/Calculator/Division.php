<?php

namespace App\Calculator;

use App\Calculator\Api\OperationInterface;
use App\Calculator\Exceptions\NoOperandException;
use App\Calculator\AbstractClasses\OperationAbstract;

/**
 * Class Division
 * @package App\Calculator
 */
class Division extends OperationAbstract implements OperationInterface
{
    /**
     * @return mixed
     * @throws NoOperandException
     */
    public function calculate()
    {
        if (empty($this->operands)) {
            throw new NoOperandException;
        }

        return array_reduce(array_filter($this->operands), function ($a, $b) {
            if ($a !== null && $b !== null) {
                return $a / $b;
            }
            return $b;
        }, null);
    }
}
