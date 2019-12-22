<?php

namespace Igo\ShortArray;

use Igo\ArrayInterface;
use Igo\IntArray\IntDynamicArray;

/**
 * Class ShortDynamicArray
 * @package Igo\ShortArray
 */
class ShortDynamicArray extends IntDynamicArray implements ArrayInterface
{
    /**
     * @param $idx
     *
     * @return mixed
     */
    public function get($idx)
    {
        fseek($this->fp, $this->start + ($idx * 2));
        $data = unpack("s*", fread($this->fp, 2));

        return $data[1];
    }
}
