<?php

namespace Igo;

use Exception;

/**
 * 未知語の検索を行うクラス
 * Class Unknown
 * @package Igo
 */
class Unknown
{
    private $category; // 文字カテゴリ管理クラス
    private $spaceId; // 文字カテゴリがSPACEの文字のID

    /**
     * Unknown constructor.
     *
     * @param $dataDir
     *
     * @throws Exception
     */
    public function __construct($dataDir)
    {
        $this->category = new CharCategory($dataDir);
        $this->spaceId  = $this->category->category(32)->id; // NOTE: ' 'の文字カテゴリはSPACEに予約されている
    }

    /**
     * @param $text
     * @param $start
     * @param  WordDic  $wdic
     * @param  MakeLattice  $fn
     */
    public function search($text, $start, $wdic, $fn)
    {
        $ch = $text[$start];
        $ct = $this->category->category($ch);

        if ($fn->isEmpty() === false && $ct->invoke === false) {
            return;
        }

        $isSpace = $ct->id === $this->spaceId;
        $limit   = min(count($text), $ct->length + $start);
        $idx     = $start;
        for (; $idx < $limit; $idx++) {
            $wdic->searchFromTrieId($ct->id, $start, ($idx - $start) + 1, $isSpace, $fn);
            if ($idx + 1 !== $limit && $this->category->isCompatible($ch, $text[$idx + 1]) === false) {
                return;
            }
        }

        if ($ct->group && $idx < count($text)) {
            $limit = count($text);
            for (; $idx < $limit; $idx++) {
                if ($this->category->isCompatible($ch, $text[$idx]) === false) {
                    $wdic->searchFromTrieId($ct->id, $start, $idx - $start, $isSpace, $fn);

                    return;
                }
            }
            $wdic->searchFromTrieId($ct->id, $start, count($text) - $start, $isSpace, $fn);
        }
    }
}
