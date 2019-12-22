<?php

namespace Igo\CharArray;

use Igo\ArrayInterface;
use Igo\IntArray\IntMemoryArray;
use Igo\FileMappedInputStream;

/**
 * Class CharMemoryArray
 * @package Igo\CharArray
 */
class CharMemoryArray extends IntMemoryArray implements ArrayInterface
{
    /**
     * CharMemoryArray constructor.
     *
     * @param  FileMappedInputStream  $reader
     * @param $count
     */
    public function __construct(&$reader, $count)
    {
        parent::__construct($reader, $count);
        $this->array = $reader->getCharArray($count);
    }

    /**
     * @param $idx
     *
     * @return mixed
     */
    public function get($idx)
    {
        return parent::get($idx);
    }
}
