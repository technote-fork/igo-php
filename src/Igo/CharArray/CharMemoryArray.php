<?php

namespace Igo\CharArray;

use Igo\ArrayInterface;
use Igo\IntArray\IntMemoryArray;

class CharMemoryArray extends IntMemoryArray implements ArrayInterface
{
    public function __construct(&$reader, $count)
    {
        $this->array = $reader->getCharArray($count);
    }

    public function get($idx)
    {
        return parent::get($idx);
    }
}
