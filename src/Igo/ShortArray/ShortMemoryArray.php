<?php

namespace Igo\ShortArray;

use Igo\ArrayInterface;
use Igo\IntArray\IntMemoryArray;
use Igo\FileMappedInputStream;

/**
 * Class ShortMemoryArray
 * @package Igo\ShortArray
 */
class ShortMemoryArray extends IntMemoryArray implements ArrayInterface
{
    /**
     * ShortMemoryArray constructor.
     *
     * @param  FileMappedInputStream  $reader
     * @param $count
     */
    public function __construct(&$reader, $count)
    {
        parent::__construct($reader, $count);
        $this->array = $reader->getShortArray($count);
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
