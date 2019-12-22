<?php

namespace Igo\IntArray;

use Igo\ArrayInterface;

/**
 * Class IntDynamicArray
 * @package Igo\IntArray
 */
class IntDynamicArray implements ArrayInterface
{
    protected $start;
    protected $fileName;
    protected $fp;

    /**
     * IntDynamicArray constructor.
     *
     * @param $fileName
     * @param $start
     */
    public function __construct($fileName, $start)
    {
        $this->fileName = $fileName;
        $this->start    = $start;
        $this->fp       = fopen($this->fileName, "rb");
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        fclose($this->fp);
    }

    /**
     * @param $idx
     *
     * @return mixed
     */
    public function get($idx)
    {
        fseek($this->fp, $this->start + ($idx * 4));
        $data = unpack("l*", fread($this->fp, 4));

        return $data[1];
    }
}
