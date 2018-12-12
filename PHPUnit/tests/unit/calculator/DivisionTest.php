<?php

use \PHPUnit\Framework\TestCase;
use App\Calculator\Division;

/**
 * Class DivisionTest
 */
class DivisionTest extends TestCase
{
    /**
     * @var $_division
     */
    protected $_division;

    public function setUp()
    {
        $this->_division = new Division;
    }

    /** @test */
    public function dividesGivenOperands()
    {
        $this->_division->setOperands([100, 0, 2]);

        $this->assertEquals(50, $this->_division->calculate());
    }

    /** @test */
    public function noOperandsGivenThrowsExceptionWhenCalculating()
    {
        $this->expectException(\App\Calculator\Exceptions\NoOperandException::class);

        $this->_division->calculate();
    }

    /** @test */
    public function removeDivisionByZeroOperands()
    {
        $this->_division->setOperands([10, 0, 0, 5, 0]);

        $this->assertEquals(2, $this->_division->calculate());
    }
}
