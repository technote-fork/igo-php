<?php

require_once '../vendor/autoload.php';

use Igo\Tagger;

$igo = new Tagger();
$result = $igo->parse('すもももももももものうち');
print_r($result);
echo memory_get_peak_usage(), "\n";
