<?php

namespace Igo;

use Exception;
use Igo\CharArray\CharDynamicArray;
use Igo\CharArray\CharMemoryArray;
use Igo\IntArray\IntDynamicArray;
use Igo\IntArray\IntMemoryArray;
use Igo\ShortArray\ShortDynamicArray;
use Igo\ShortArray\ShortMemoryArray;

/**
 * Class FileMappedInputStream
 * @package Igo
 */
class FileMappedInputStream
{
    private $cur;
    private $file;
    private $fileName;

    /**
     * FileMappedInputStream constructor.
     *
     * @param  string  $fileName
     *
     * @throws Exception
     */
    public function __construct($fileName)
    {
        $this->cur  = 0;
        $this->file = fopen($fileName, "rb");
        if (! $this->file) {
            throw new Exception("dictionary reading failed.");
        }
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getInt()
    {
        $this->cur += 4;
        $data      = unpack("l*", fread($this->file, 4));

        return $data[1];
    }

    /**
     * @param  int  $count
     *
     * @return array
     */
    public function getIntArray($count)
    {
        $this->cur += ($count * 4);

        return array_values(unpack("l*", fread($this->file, $count * 4)));
    }

    /**
     * @param  int  $count
     *
     * @return IntDynamicArray|IntMemoryArray
     */
    public function getIntArrayInstance($count)
    {
        if (Tagger::$reduce) {
            $idx = new IntDynamicArray($this->fileName, $this->cur);
            fseek($this->file, $this->cur + $count * 4);
            $this->cur += ($count * 4);
        } else {
            $idx = new IntMemoryArray($this, $count);
        }

        return $idx;
    }

    /**
     * @param  string  $fileName
     *
     * @return array
     * @throws Exception
     */
    public static function getFileIntArray($fileName)
    {
        $fmis  = new FileMappedInputStream($fileName);
        $array = $fmis->getIntArray($fmis->size() / 4);
        $fmis->close();

        return $array;
    }

    /**
     * @param $count
     *
     * @return array
     */
    public function getShortArray($count)
    {
        $this->cur += ($count * 2);

        return array_values(unpack("s*", fread($this->file, $count * 2)));
    }

    /**
     * @param  int  $count
     *
     * @return ShortDynamicArray|ShortMemoryArray
     */
    public function getShortArrayInstance($count)
    {
        if (Tagger::$reduce) {
            $short = new ShortDynamicArray($this->fileName, $this->cur);
            fseek($this->file, $this->cur + $count * 2);
            $this->cur += ($count * 2);
        } else {
            $short = new ShortMemoryArray($this, $count);
        }

        return $short;
    }

    /**
     * @param  int  $count
     *
     * @return CharDynamicArray|CharMemoryArray
     */
    public function getCharArrayInstance($count)
    {
        if (Tagger::$reduce) {
            $char = new CharDynamicArray($this->fileName, $this->cur);
            fseek($this->file, $this->cur + $count * 2);
            $this->cur += ($count * 2);
        } else {
            $char = new CharMemoryArray($this, $count);
        }

        return $char;
    }

    /**
     * @param  int  $count
     *
     * @return array
     */
    public function getCharArray($count)
    {
        $this->cur += ($count * 2);

        return array_values(unpack("S*", fread($this->file, $count * 2)));
    }

    /**
     * @param  int  $count
     *
     * @return false|string
     */
    public function getString($count)
    {
        return fread($this->file, $count * 2);
    }

    /**
     * @param  string  $fileName
     *
     * @return false|string
     * @throws Exception
     */
    public static function getFileString($fileName)
    {
        $fmis = new FileMappedInputStream($fileName);
        $str  = $fmis->getString($fmis->size() / 2);
        $fmis->close();

        return $str;
    }

    /**
     * @return false|int
     */
    public function size()
    {
        return filesize($this->fileName);
    }

    /**
     * @return bool
     */
    public function close()
    {
        return fclose($this->file);
    }
}
