<?php

use \PHPUnit\Framework\TestCase;

use App\Calculator\Addition;
use App\Calculator\Division;
use App\Calculator\Calculator;

/**
 * Class CalculatorTest
 */
class CalculatorTest extends TestCase
{
    /**
     * @var $addition
     */
    protected $addition;

    /**
     * @var $division
     */
    protected $division;

    /**
     * @var $calculator
     */
    protected $calculator;

    public function setUp()
    {
        $this->addition = new Addition;
        $this->division = new Division;
        $this->calculator = new Calculator;
    }

    /** @test */
    public function canSetSingleOperation()
    {
        $this->addition->setOperands([5, 10]);

        $this->calculator->setOperation($this->addition);

        $this->assertCount(1, $this->calculator->getOperations());
    }

    /** @test */
    public function canSetMultipleOperations()
    {
        $addition1 = new Addition;
        $addition1->setOperands([5, 10]);

        $addition2 = new Addition;
        $addition2->setOperands([2, 2]);

        $this->calculator->setOperations([$addition1, $addition2]);

        $this->assertCount(2, $this->calculator->getOperations());
    }

    /** @test */
    public function operationsAreIgnoredIfNotInstanceOfOperationInterface()
    {
        $this->addition->setOperands([5, 10]);

        $this->calculator->setOperations([$this->addition, 'cats', 'dogs']);

        $this->assertCount(1, $this->calculator->getOperations());
    }

    /** @test */
    public function canCalculateResult()
    {
        $this->addition->setOperands([10, 5]);

        $this->calculator->setOperation($this->addition);

        $this->assertEquals(15, $this->calculator->calculate());
    }

    /** @test */
    public function calculateMethodReturnsMultipleResults()
    {
        $this->addition->setOperands([5, 10]);
        $this->division->setOperands([50, 2]);
        $this->calculator->setOperations([$this->addition, $this->division]);

        $this->assertInternalType('array', $this->calculator->calculate());
        $this->assertEquals(15, $this->calculator->calculate()[0]);
        $this->assertEquals(25, $this->calculator->calculate()[1]);
    }

    /**
     * @test
     * @dataProvider addDataProvider
     *
     * @param int $a
     * @param int $b
     * @param int $except
     */
    public function dataProviderWorkCheck(int $a, int $b, int $except)
    {
        $this->addition->setOperands([$a, $b]);
        $this->calculator->setOperation($this->addition);
        $this->assertEquals($except, $this->calculator->calculate());
    }

    /**
     * @return array
     */
    public function addDataProvider(): array
    {
        return
            [
                [1, 2, 3],
                [-1, -1, -2],
                [10, 1, 11]   // Error
            ];
    }
}
