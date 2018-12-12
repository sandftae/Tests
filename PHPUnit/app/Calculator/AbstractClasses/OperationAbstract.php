<?php

namespace App\Calculator\AbstractClasses;

/**
 * Class OperationAbstract
 * @package App\Calculator\AbstractClasses
 */
class OperationAbstract
{
    /**
     * @var $operands
     */
    protected $operands;

    /**
     * @param array $data
     */
    public function setOperands(array $data):void
    {
        $this->operands = $data;
    }
}