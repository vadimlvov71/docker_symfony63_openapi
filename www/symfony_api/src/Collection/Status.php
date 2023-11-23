<?php

namespace Collection;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Status
{
    /** @var string[] */
    private array $status = [];

    public function add(string $string): void
    {
        $this->status[] = $string;
    }
    
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->status);
    }
}