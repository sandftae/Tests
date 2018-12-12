<?php

use \PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 */
class UserTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        $this->user = new \App\Models\User;
    }

    /**
     * @test
     */
    public function thatWeCanGetTheFirstName()
    {
        $this->user->setFirstName('Billy');
        $this->assertEquals($this->user->getFirstName(), 'Billy');
    }

    /** @test */
    public function thatWeCanGetTheLastName()
    {
        $this->user->setLastName('Garrett');
        $this->assertEquals($this->user->getLastName(), 'Garrett');
    }

    /** @test */
    public function fullNameIsReturned()
    {
        $this->user->setFirstName('Billy');
        $this->user->setLastName('Garrett');

        $this->assertEquals($this->user->getFullName(), "Billy Garrett");
    }

    /** @test */
    public function firstAndLastNameAreTrimmed()
    {
        $this->user->setFirstName('   Billy');
        $this->user->setLastName('      Garrett     ');

        $this->assertEquals($this->user->getFirstName(), 'Billy');
        $this->assertEquals($this->user->getLastName(), 'Garrett');
    }

    /** @test */
    public function emailAddressCanBeSet()
    {
        $this->user->setEmail('billy@codecourse.com');

        $this->assertEquals($this->user->getEmail(), 'billy@codecourse.com');
    }

    /** @test */
    public function emailVariablesContainCorrectValues()
    {
        $this->user->setFirstName('Billy');
        $this->user->setLastName('Garrett');
        $this->user->setEmail('billy@codecourse.com');

        $emailVariables = $this->user->getEmailVariables();

        $this->assertArrayHasKey('full_name', $emailVariables);
        $this->assertArrayHasKey('email', $emailVariables);

        $this->assertEquals($emailVariables['full_name'], 'Billy Garrett');
        $this->assertEquals($emailVariables['email'], 'billy@codecourse.com');
    }
}
