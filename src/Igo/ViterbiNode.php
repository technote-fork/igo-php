<?php

namespace Igo;

/**
 * Viterbiアルゴリズムで使用されるノード
 * Class ViterbiNode
 * @package Igo
 */
class ViterbiNode
{
    public $cost; // 始点からノードまでの総コスト
    public $prev; // コスト最小の前方のノードへのリンク
    public $wordId; // 単語ID
    public $leftId; // 左文脈ID
    public $rightId; // 右文脈ID
    public $start; // 入力テキスト内での形態素の開始位置
    public $length; // 形態素の表層形の長さ(文字数)
    public $isSpace; // 形態素の文字種(文字カテゴリ)が空白文字かどうか

    /**
     * ViterbiNode constructor.
     *
     * @param $wid
     * @param $beg
     * @param $len
     * @param $wordCost
     * @param $leftId
     * @param $rightId
     * @param $space
     */
    public function __construct($wid, $beg, $len, $wordCost, $leftId, $rightId, $space)
    {
        $this->wordId  = $wid;
        $this->leftId  = $leftId;
        $this->rightId = $rightId;
        $this->length  = $len;
        $this->cost    = $wordCost;
        $this->isSpace = $space;
        $this->start   = $beg;
        $this->prev    = null;
    }

    /**
     * @return ViterbiNode
     */
    public static function makeBOSEOS()
    {
        return new ViterbiNode(0, 0, 0, 0, 0, 0, false);
    }
}
