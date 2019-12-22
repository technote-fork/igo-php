<?php

namespace Igo;

use Exception;

/**
 * Class WordDic
 * @package Igo
 */
class WordDic
{
    private $trie;
    private $data;
    public $indices;

    public $costs; // consts[単語ID] = 単語のコスト
    public $leftIds; // leftIds[単語ID] = 単語の左文脈ID
    public $rightIds; // rightIds[単語ID] = 単語の右文脈ID
    public $dataOffsets; // dataOffsets[単語ID] = 単語の素性データの開始位置

    /**
     * WordDic constructor.
     *
     * @param $dataDir
     *
     * @throws Exception
     */
    public function __construct($dataDir)
    {
        $this->trie    = new Searcher($dataDir."/word2id");
        $this->data    = FileMappedInputStream::getFileString($dataDir."/word.dat");
        $this->indices = FileMappedInputStream::getFileIntArray($dataDir."/word.ary.idx");

        $fmis              = new FileMappedInputStream($dataDir."/word.inf");
        $wordCount         = $fmis->size() / (4 + 2 + 2 + 2);
        $this->dataOffsets = $fmis->getIntArrayInstance($wordCount); //単語の素性データの開始位置
        $this->leftIds     = $fmis->getShortArrayInstance($wordCount); //単語の左文脈ID
        $this->rightIds    = $fmis->getShortArrayInstance($wordCount); //単語の右文脈ID
        $this->costs       = $fmis->getShortArrayInstance($wordCount); //単語のコスト
        $fmis->close();
    }

    /**
     * @param $text
     * @param $start
     * @param  MakeLattice  $fn
     */
    public function search($text, $start, $fn)
    {
        $this->trie->eachCommonPrefix($text, $start, new WordDicCallbackCaller($this, $fn));
    }

    /**
     * @param $trieId
     * @param $start
     * @param $wordLength
     * @param $isSpace
     * @param  MakeLattice  $fn
     */
    public function searchFromTrieId($trieId, $start, $wordLength, $isSpace, $fn)
    {
        $end = $this->indices[$trieId + 1];
        for ($i = $this->indices[$trieId]; $i < $end; $i++) {
            $fn->call(new ViterbiNode($i, $start, $wordLength, $this->costs->get($i), $this->leftIds->get($i), $this->rightIds->get($i), $isSpace));
        }
    }

    /**
     * @param $wordId
     *
     * @return false|string
     */
    public function wordData($wordId)
    {
        return substr($this->data, ($this->dataOffsets->get($wordId)) << 1, ($this->dataOffsets->get($wordId + 1) - $this->dataOffsets->get($wordId)) << 1);
    }
}
