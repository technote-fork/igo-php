<?php

namespace Igo;

use Exception;

/**
 * 形態素の連接コスト表を扱うクラス
 * Class Matrix
 * @package Igo
 */
class Matrix
{
    private $leftSize;
    private $rightSize;
    private $matrix;

    /**
     * Matrix constructor.
     *
     * @param $dataDir
     *
     * @throws Exception
     */
    public function __construct($dataDir)
    {
        $fmis            = new FileMappedInputStream($dataDir."/matrix.bin");
        $this->leftSize  = $fmis->getInt();
        $this->rightSize = $fmis->getInt();
        $this->matrix    = $fmis->getShortArrayInstance($this->leftSize * $this->rightSize);
        $fmis->close();
    }

    /**
     * 形態素同士の連接コストを求める
     *
     * @param $leftId
     * @param $rightId
     *
     * @return mixed
     */
    public function linkCost($leftId, $rightId)
    {
        return $this->matrix->get($rightId * $this->leftSize + $leftId);
    }
}
