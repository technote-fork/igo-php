<?php

namespace Igo;

use Exception;

/**
 * Class Tagger
 * @package Igo
 */
class Tagger
{
    public static $reduce = false;
    public static $dicEnc;
    private $wdc;
    private $unk;
    private $mtx;
    private $enc;

    /**
     * デフォルトの設定項目
     */
    public $options = [
        // 辞書ディレクトリ
        'dict_dir'        => null,
        // 配列として出力
        'return_as_array' => false,
        // 実行時の使用メモリを調整
        'reduce_mode'     => true,
        // バイトオーダー
        'little_endian'   => true,
        // mbstringの判定優先順位
        'md_detect_order' => 'ASCII,JIS,UTF-8,EUC-JP,SJIS',
        // 出力エンコード
        'output_encoding' => 'UTF-8',
    ];

    /**
     * Tagger constructor.
     *
     * バイナリ辞書を読み込んで、形態素解析器のインスタンスを作成する
     *
     * @param  array  $options  設定項目
     *
     * @throws Exception
     */
    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);

        if (empty($this->options['dict_dir']) || ! is_dir($this->options['dict_dir'])) {
            throw new Exception('Dictionary directory has not defined or not readable.');
        }

        self::$reduce = $this->options['reduce_mode'];
        self::$dicEnc = ($this->options['little_endian']) ? 'UTF-16LE' : 'UTF-16BE';

        $this->wdc = new WordDic($this->options['dict_dir']);
        $this->unk = new Unknown($this->options['dict_dir']);
        $this->mtx = new Matrix($this->options['dict_dir']);
    }

    private function getEnc()
    {
        return $this->options['output_encoding'] !== null ? $this->options['output_encoding'] : $this->enc;
    }

    /**
     * 形態素解析を行う
     *
     * @param  string  $text  解析対象テキスト
     * @param  array  $result  解析結果の形態素が追加されるリスト
     *
     * @return array 解析結果の形態素リスト. {@code parse(text,result)=result}
     */
    public function parse($text, $result = [])
    {
        $this->enc = mb_detect_encoding($text, $this->options['md_detect_order']);
        $utf16     = mb_convert_encoding($text, self::$dicEnc, $this->enc);
        $source    = array_values(unpack("S*", $utf16));

        for ($vn = $this->parseImpl($source); $vn != null; $vn = $vn->prev) {
            $surface = mb_convert_encoding(substr($utf16, $vn->start << 1, $vn->length << 1), $this->getEnc(), self::$dicEnc);
            $feature = mb_convert_encoding($this->wdc->wordData($vn->wordId), $this->getEnc(), self::$dicEnc);
            if (! $this->options['return_as_array']) {
                $result[] = new Morpheme($surface, $feature, $vn->start);
            } else {
                $result[] = ["surface" => $surface, "feature" => $feature, "start" => $vn->start];
            }
        }

        return $result;
    }

    /**
     * 分かち書きを行う
     *
     * @param  string  $text  分かち書きされるテキスト
     * @param  array  $result  分かち書き結果の文字列が追加されるリスト
     *
     * @return array 分かち書きされた文字列のリスト.
     */
    public function wakati($text, $result = [])
    {
        $this->enc = mb_detect_encoding($text, $this->options['md_detect_order']);
        $utf16     = mb_convert_encoding($text, self::$dicEnc, $this->enc);
        $source    = array_values(unpack("S*", $utf16));

        for ($vn = $this->parseImpl($source); $vn != null; $vn = $vn->prev) {
            $result[] = mb_convert_encoding(substr($utf16, $vn->start << 1, $vn->length << 1), $this->getEnc(), self::$dicEnc);
        }

        return $result;
    }

    /**
     * @param $text
     *
     * @return ViterbiNode|null
     */
    private function parseImpl($text)
    {
        $len      = count($text);
        $nodesAry = [[ViterbiNode::makeBOSEOS()]];
        for ($idx = 1; $idx <= $len; $idx++) {
            $nodesAry[$idx] = [];
        }

        $fn = new MakeLattice($this, $nodesAry);
        for ($idx = 0; $idx < $len; $idx++) {
            if (! empty($nodesAry[$idx])) {
                $fn->set($idx);
                $this->wdc->search($text, $idx, $fn); // 単語辞書から形態素を検索
                $this->unk->search($text, $idx, $this->wdc, $fn); // 未知語辞書から形態素を検索
                unset($nodesAry[$idx]);
            }
        }

        /** @var ViterbiNode $cur */
        $cur = $this->setMincostNode(ViterbiNode::makeBOSEOS(), $nodesAry[$len])->prev;

        // reverse
        $head = null;
        while ($cur->prev !== null) {
            $tmp       = $cur->prev;
            $cur->prev = $head;
            $head      = $cur;
            $cur       = $tmp;
        }

        return $head;
    }

    /**
     * @param  ViterbiNode  $vn
     * @param $prevs
     *
     * @return ViterbiNode
     */
    public function setMincostNode($vn, $prevs)
    {
        $first   = $vn->prev = $prevs[0];
        $minCost = $first->cost + $this->mtx->linkCost($first->rightId, $vn->leftId);

        for ($idx = 1, $count = count($prevs); $idx < $count; $idx++) {
            $prev = $prevs[$idx];
            $cost = $prev->cost + $this->mtx->linkCost($prev->rightId, $vn->leftId);
            if ($cost < $minCost) {
                $minCost  = $cost;
                $vn->prev = $prev;
            }
        }

        $vn->cost += $minCost;

        return $vn;
    }
}
