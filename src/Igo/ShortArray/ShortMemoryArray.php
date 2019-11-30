<?php

namespace Igo\ShortArray;

use Igo\ArrayInterface;
use Igo\IntArray\IntMemoryArray;

class ShortMemoryArray extends IntMemoryArray implements ArrayInterface
{
    public function __construct(&$reader, $count)
    {
        $this->array = $reader->getShortArray($count);
    }

    public function get($idx)
    {
        return parent::get($idx);
    }
}
