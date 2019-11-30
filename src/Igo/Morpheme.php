<?php

namespace Igo;

/**
 * 形態素クラス.
 */
class Morpheme
{
    /**
     * @var string 形態素の表層形
     */
    public $surface;
    /**
     * @var array 形態素の素性
     */
    public $feature;
    /**
     * @var int テキスト内での形態素の出現開始位置
     */
    public $start;

    public function __construct(string $surface, string $feature, $start)
    {
        $this->surface = $surface;
        $this->feature = explode(',', $feature);
        $this->start = (int) $start;
    }
}
