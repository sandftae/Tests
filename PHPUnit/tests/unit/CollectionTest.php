<?php

use \PHPUnit\Framework\TestCase;

/**
 * Class CollectionTest
 */
class CollectionTest extends TestCase
{
    /**
     * @var \App\Support\Collection
     */
    protected $collection;

    public function setUp(): void
    {
        $this->collection = new \App\Support\Collection;
    }

    /** @test */
    public function emptyInstantiatedCollectionReturnsNoItems(): void
    {
        $this->assertEmpty($this->collection->get());
    }

    /** @test */
    public function countIsCorrectForItemsPassedIn(): void
    {
        $this->collection->set(
            [
                'one', 'two', 'three'
            ]
        );

        $this->assertEquals(3, $this->collection->count());
    }

    /** @test */
    public function itemsReturnedMatchItemsPassedIn(): void
    {
        $this->collection->set(
            [
                'one', 'two', 'three'
            ]
        );

        $this->assertCount(3, $this->collection->get());
        $this->assertEquals($this->collection->get()[0], 'one');
        $this->assertEquals($this->collection->get()[1], 'two');
    }

    /** @test */
    public function collectionIsInstanceOfIteratorAggregate(): void
    {
        $this->collection->set(
            [
                'one', 'two', 'three'
            ]
        );

        $this->assertInstanceOf(IteratorAggregate::class, $this->collection);
    }

    /** @test */
    public function collectionCanBeIterated(): void
    {
        $this->collection->set(
            [
                'one', 'two', 'three'
            ]
        );

        $itemsCollection = $this->collection->get();

        $items = [];

        foreach ($itemsCollection as $item) {
            $items[] = $item;
        }

        $this->assertCount(3, $items);
        $this->assertInstanceOf(ArrayIterator::class, $this->collection->getIterator());
    }

    /** @test */
    public function collectionCanBeMergedWitchAnotherCollection(): void
    {
        // the values ​​of the first instance list of an collection
        $this->collection->set(
            [
                'one', 'two',
            ]
        );
        $collectionOne = clone $this->collection;


        # the values ​​of the second instance list of an collection
        $this->collection->set(
            [
                'three', 'four', 'five'
            ]
        );
        $collectionTwo = clone $this->collection;

        $collectionOne->merge($collectionTwo);

        $this->assertCount(5, $collectionOne->get());
        $this->assertEquals(5, $collectionOne->count());
    }

    /** @test */
    public function canAddToExistingCollection():void
    {
        $this->collection->set(
            [
                'one', 'two',
            ]
        );

        $this->collection->add(['three']);

        $this->assertEquals(3, $this->collection->count());
        $this->assertCount(3, $this->collection->get());
    }

    /** @test */
    public function returnsJsonEncodedItems():void
    {
        $this->collection->set(
            [
                ['username' => 'alex'],
                ['username' => 'billy']
            ]
        );

        $this->assertInternalType('string', $this->collection->toJson());

        $this->assertEquals(
            '[{"username":"alex"},{"username":"billy"}]',
            $this->collection->toJson()
        );
    }

    /** @test */
    public function jsonEncodingACollectionObjectReturnsJson(): void
    {
        $this->collection->set(
            [
                ['username' => 'alex'],
                ['username' => 'billy']
            ]
        );

        $encoded = json_encode($this->collection->get());

        $this->assertInternalType('string', $encoded);
        $this->assertEquals(
            '[{"username":"alex"},{"username":"billy"}]',
            $encoded
        );
    }
}
