<?php

namespace App\Calculator\Api;

/**
 * Interface OperationInterface
 * @package App\Calculator\Api
 */
interface OperationInterface
{
    /**
     * @return mixed
     */
    public function calculate();
}
