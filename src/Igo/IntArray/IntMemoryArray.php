<?php

namespace Igo\IntArray;

use Igo\ArrayInterface;

class IntMemoryArray implements ArrayInterface
{
    protected $array;

    public function __construct(&$reader, $count)
    {
        $this->array = $reader->getIntArray($count);
    }

    public function get($idx)
    {
        return $this->array[$idx];
    }
}
