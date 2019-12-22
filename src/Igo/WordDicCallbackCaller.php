<?php

namespace Igo;

/**
 * Class WordDicCallbackCaller
 * @package Igo
 */
class WordDicCallbackCaller
{
    /** @var MakeLattice $fn */
    private $fn;
    private $wd;

    /**
     * WordDicCallbackCaller constructor.
     *
     * @param $wd
     * @param $fn
     */
    public function __construct($wd, $fn)
    {
        $this->wd = $wd;
        $this->fn = $fn;
    }

    /**
     * @param $start
     * @param $offset
     * @param $trieId
     */
    public function call($start, $offset, $trieId)
    {
        $end = $this->wd->indices[$trieId + 1];
        for ($i = $this->wd->indices[$trieId]; $i < $end; $i++) {
            $this->fn->call(new ViterbiNode($i, $start, $offset, $this->wd->costs->get($i), $this->wd->leftIds->get($i), $this->wd->rightIds->get($i), false));
        }
    }
}
