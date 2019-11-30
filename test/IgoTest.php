<?php

use Igo\Tagger;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class IgoTest extends TestCase
{
    protected function setUp(): void
    {
        $this->igo = new Tagger();
    }

    public function testWakati()
    {
        $result = $this->igo->wakati('にわにはにわのにわとりがいる');

        $this->assertSame('に わに はにわ の にわとり が いる', implode(' ', $result));
    }
}
