<?php

namespace Igo\Tests;

use Exception;
use Igo\Tagger;
use PHPUnit\Framework\TestCase;

class IgoTest extends TestCase
{
    /** @var Tagger $igo */
    private static $igo;

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass()
    {
        self::$igo = new Tagger(['dict_dir' => __DIR__.'/../jdic']);
    }

    public function testParse()
    {
        $result = self::$igo->parse('すもももももももものうち');
        $this->assertCount(7, $result);
        $this->assertEquals('すもも', $result[0]->surface);
        $this->assertEquals('名詞,一般,*,*,*,*,すもも,スモモ,スモモ', $result[0]->feature);
        $this->assertEquals(0, $result[0]->start);
        $this->assertEquals('も', $result[1]->surface);
        $this->assertEquals('もも', $result[2]->surface);
        $this->assertEquals('も', $result[3]->surface);
        $this->assertEquals('もも', $result[4]->surface);
        $this->assertEquals('の', $result[5]->surface);
        $this->assertEquals('うち', $result[6]->surface);
        $this->assertEquals('名詞,非自立,副詞可能,*,*,*,うち,ウチ,ウチ', $result[6]->feature);
        $this->assertEquals(10, $result[6]->start);
    }

    public function testWakati()
    {
        $result = self::$igo->wakati('にわにはにわのにわとりがいる');
        $this->assertSame('に わに はにわ の にわとり が いる', implode(' ', $result));
    }

    public function testText()
    {
        $result = self::$igo->wakati(file_get_contents(__DIR__.'/yoshinoya.txt'));
        $this->assertSame(explode("\n", file_get_contents(__DIR__.'/yoshinoya_wakati.txt')), $result);
    }
}
