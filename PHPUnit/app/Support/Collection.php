<?php

namespace App\Support;

use IteratorAggregate;
use ArrayIterator;
use JsonSerializable;

/**
 * Class Collection
 * @package App\Support
 */
class Collection implements IteratorAggregate, JsonSerializable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->items;
    }

    /**
     * @param array $data
     */
    public function set(array $data = []): void
    {
        $this->items = $data;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param Collection $collection
     */
    public function merge(Collection $collection):void
    {
        $this->add($collection->get());
    }

    /**
     * @param array $data
     */
    public function add(array $data):void
    {
        $this->items = array_merge($this->items, $data);
    }

    /**
     * @return string
     */
    public function toJson():string
    {
        return json_encode($this->get());
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
         return $this->items;
    }
}
