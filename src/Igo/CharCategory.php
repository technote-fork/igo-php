<?php

namespace Igo;

use Exception;

/**
 * Class CharCategory
 * @package Igo
 */
class CharCategory
{
    private $categories;
    private $char2id;
    private $eqlMasks;

    /**
     * CharCategory constructor.
     *
     * @param $dataDir
     *
     * @throws Exception
     */
    public function __construct($dataDir)
    {
        $this->categories = $this->readCategories($dataDir);

        $fmis           = new FileMappedInputStream($dataDir."/code2category");
        $this->char2id  = $fmis->getIntArrayInstance($fmis->size() / 4 / 2);
        $this->eqlMasks = $fmis->getIntArrayInstance($fmis->size() / 4 / 2);
        $fmis->close();
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function category($code)
    {
        return $this->categories[$this->char2id->get($code)];
    }

    /**
     * @param $code1
     * @param $code2
     *
     * @return bool
     */
    public function isCompatible($code1, $code2)
    {
        return ($this->eqlMasks->get($code1) & $this->eqlMasks->get($code2)) != 0;
    }

    /**
     * @param $dataDir
     *
     * @return array
     * @throws Exception
     */
    private function readCategories($dataDir)
    {
        $data = FileMappedInputStream::getFileIntArray($dataDir."/char.category");
        $size = count($data) / 4;

        $ary = [];
        for ($i = 0; $i < $size; $i++) {
            $ary[$i] = new Category($data[$i * 4], $data[$i * 4 + 1], $data[$i * 4 + 2] === 1, $data[$i * 4 + 3] === 1);
        }

        return $ary;
    }
}
