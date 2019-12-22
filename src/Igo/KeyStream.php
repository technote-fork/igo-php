<?php

namespace Igo;

/**
 * Class KeyStream
 * @package Igo
 */
class KeyStream
{
    private $key;
    private $cur;

    /**
     * KeyStream constructor.
     *
     * @param $key
     * @param  int  $start
     */
    public function __construct($key, $start = 0)
    {
        $this->key = $key;
        $this->cur = $start;
    }

    /**
     * @param $prefix
     * @param $beg
     * @param $len
     *
     * @return bool
     */
    public function startsWith($prefix, $beg, $len)
    {
        if (count($this->key) - $this->cur < $len) {
            return false;
        }

        for ($i = 0; $i < $len; $i++) {
            if ($this->key[$this->cur + $i] !== $prefix[$beg + $i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int
     */
    public function read()
    {
        return $this->eos() ? 0 : $this->key[$this->cur++];
    }

    /**
     * @return bool
     */
    public function eos()
    {
        return $this->cur === count($this->key);
    }
}
