<?php

use \PHPUnit\Framework\TestCase;

/**
 * Class AdditionTest
 */
class AdditionTest extends TestCase
{
    /**
     * @var $_addition
     */
    protected $_addition;

    public function setUp()
    {
        $this->_addition = new \App\Calculator\Addition;
    }

    /** @test */
    public function addsUpGivenOperands()
    {
        $this->_addition->setOperands([5, 10]);

        $this->assertEquals(15, $this->_addition->calculate());
    }

    /** @test */
    public function noOperandsGivenThrowsExceptionWhenCalculating()
    {
        $this->expectException(\App\Calculator\Exceptions\NoOperandException::class);

        $this->_addition->calculate();
    }
}
