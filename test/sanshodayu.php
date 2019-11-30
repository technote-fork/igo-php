<?php

require_once '../vendor/autoload.php';

use Igo\Tagger;

$encode = 'UTF-8';
ini_set('memory_limit', '1073741824'); //1024^3

$text = file_get_contents('./yoshinoya.txt');

$igo = new Tagger(['reduce_mode'  => false]);

$bench = new benchmark();
$bench->start();
$result = $igo->parse($text);
$bench->end();
print_r('score: '.$bench->score);
print_r("\n");
$fp = fopen('./php-igo.result', 'w');
foreach ($result as $res) {
    $buf = '';
    $buf .= $res->surface;
    $buf .= ',';
    $buf .= implode(',', $res->feature);
    $buf .= ',';
    $buf .= $res->start;
    $buf .= "\r\n";
    fwrite($fp, $buf);
}
fclose($fp);
echo memory_get_peak_usage(), "\n";

class benchmark
{
    public $start;
    public $end;
    public $score;

    public function start()
    {
        $this->start = $this->_now();
    }

    public function end()
    {
        $this->end = $this->_now();
        $this->score = round($this->end - $this->start, 5);
    }

    public function _now()
    {
        list($msec, $sec) = explode(' ', microtime());

        return (float) $msec + (float) $sec;
    }
}
