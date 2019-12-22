<?php

namespace Igo;

use Exception;

/**
 * Class Searcher
 * @package Igo
 */
class Searcher
{
    private $keySetSize;
    private $base;
    private $chck;
    private $begs;
    private $lens;
    private $tail;

    /**
     * Searcher constructor.
     *
     * @param $filePath
     *
     * @throws Exception
     */
    public function __construct($filePath)
    {
        $fmis = new FileMappedInputStream($filePath);

        $nodeSz = $fmis->getInt();
        $tindSz = $fmis->getInt();
        $tailSz = $fmis->getInt();

        $this->keySetSize = $tindSz;
        $this->begs       = $fmis->getIntArrayInstance($tindSz);
        $this->base       = $fmis->getIntArrayInstance($nodeSz);
        $this->lens       = $fmis->getShortArrayInstance($tindSz);
        $this->chck       = $fmis->getCharArrayInstance($nodeSz);
        $this->tail       = array_values(unpack("S*", $fmis->getString($tailSz)));

        $fmis->close();
    }

    /**
     * @return mixed
     */
    public function size()
    {
        return $this->keySetSize;
    }

    /**
     * @param $id
     *
     * @return float|int
     */
    public static function getId($id)
    {
        return $id * -1 - 1;
    }

    /**
     * @param $key
     * @param $start
     * @param  WordDicCallbackCaller  $fn
     */
    public function eachCommonPrefix($key, $start, $fn)
    {
        $node   = $this->base->get(0);
        $offset = 0;
        $in     = new KeyStream($key, $start);

        for ($code = $in->read(); true; $code = $in->read(), $offset++) {
            $terminalIdx = $node;
            if ($this->chck->get($terminalIdx) === 0) {
                $fn->call($start, $offset, self::getId($this->base->get($terminalIdx)));
                if ($code === 0) {
                    return;
                }
            }

            $idx  = $node + $code;
            $node = $this->base->get($idx);
            if ($this->chck->get($idx) === $code) {
                if ($node >= 0) {
                    continue;
                } else {
                    $this->callIfKeyIncluding($in, $node, $start, $offset, $fn);
                }
            }

            return;
        }
    }

    /**
     * @param  KeyStream  $in
     * @param $node
     * @param $start
     * @param $offset
     * @param  WordDicCallbackCaller  $fn
     */
    private function callIfKeyIncluding($in, $node, $start, $offset, $fn)
    {
        $id = self::getId($node);
        if ($in->startsWith($this->tail, $this->begs->get($id), $this->lens->get($id))) {
            $fn->call($start, $offset + $this->lens->get($id) + 1, $id);
        }
    }
}
