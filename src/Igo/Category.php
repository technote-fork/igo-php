<?php

namespace Igo;

/**
 * Class Category
 * @package Igo
 */
class Category
{
    public $id;
    public $length;
    public $invoke;
    public $group;

    /**
     * Category constructor.
     *
     * @param $id
     * @param $length
     * @param $invoke
     * @param $group
     */
    public function __construct($id, $length, $invoke, $group)
    {
        $this->id     = $id;
        $this->length = $length;
        $this->invoke = $invoke;
        $this->group  = $group;
    }
}
