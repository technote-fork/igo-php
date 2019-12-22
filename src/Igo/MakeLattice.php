<?php

namespace Igo;

/**
 * Class MakeLattice
 * @package Igo
 */
class MakeLattice
{
    /** @var Tagger $tagger */
    private $tagger;
    private $nodesAry;
    private $idx;
    private $prevs;
    private $empty = true;

    /**
     * MakeLattice constructor.
     *
     * @param $tagger
     * @param $nodesAry
     */
    public function __construct($tagger, &$nodesAry)
    {
        $this->tagger   = $tagger;
        $this->nodesAry = &$nodesAry;
    }

    /**
     * @param $idx
     */
    public function set($idx)
    {
        $this->idx   = $idx;
        $this->prevs = $this->nodesAry[$idx];
        $this->empty = true;
    }

    /**
     * @param $vn
     */
    public function call($vn)
    {
        $this->empty = false;

        if ($vn->isSpace) {
            $this->nodesAry[$this->idx + $vn->length] = $this->prevs;
        } else {
            $this->nodesAry[$this->idx + $vn->length][] = $this->tagger->setMincostNode($vn, $this->prevs);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->empty;
    }
}
