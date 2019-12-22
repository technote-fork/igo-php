<?php

namespace Igo\IntArray;

use Igo\ArrayInterface;
use Igo\FileMappedInputStream;

class IntMemoryArray implements ArrayInterface
{
    protected $array;

    /**
     * IntMemoryArray constructor.
     *
     * @param  FileMappedInputStream  $reader
     * @param $count
     */
    public function __construct(&$reader, $count)
    {
        $this->array = $reader->getIntArray($count);
    }

    public function get($idx)
    {
        return $this->array[$idx];
    }
}
