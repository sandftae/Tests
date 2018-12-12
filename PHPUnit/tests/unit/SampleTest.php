<?php

use \PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /** @test */
    public function trueAssertsToTrue()
    {
        $this->assertTrue(true);
        $this->assertFalse(false);

    }
}
