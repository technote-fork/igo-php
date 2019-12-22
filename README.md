# Igo-php - PHP による形態素解析プログラム

[![CI Status](https://github.com/technote-fork/igo-php/workflows/CI/badge.svg)](https://github.com/technote-fork/igo-php/actions)
[![codecov](https://codecov.io/gh/technote-fork/igo-php/branch/master/graph/badge.svg)](https://codecov.io/gh/technote-fork/igo-php)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-fork/igo-php/badge)](https://www.codefactor.io/repository/github/technote-fork/igo-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/technote-fork/igo-php/blob/master/LICENSE)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [1, 概要](#1-%E6%A6%82%E8%A6%81)
- [2, インストール方法](#2-%E3%82%A4%E3%83%B3%E3%82%B9%E3%83%88%E3%83%BC%E3%83%AB%E6%96%B9%E6%B3%95)
- [3. 辞書の作成方法](#3-%E8%BE%9E%E6%9B%B8%E3%81%AE%E4%BD%9C%E6%88%90%E6%96%B9%E6%B3%95)
- [4. 使用方法](#4-%E4%BD%BF%E7%94%A8%E6%96%B9%E6%B3%95)
  - [a. 分かち書き](#a-%E5%88%86%E3%81%8B%E3%81%A1%E6%9B%B8%E3%81%8D)
  - [b. 形態素解析](#b-%E5%BD%A2%E6%85%8B%E7%B4%A0%E8%A7%A3%E6%9E%90)
  - [5. REDUCE モード](#5-reduce-%E3%83%A2%E3%83%BC%E3%83%89)
- [6. バイトオーダーについて](#6-%E3%83%90%E3%82%A4%E3%83%88%E3%82%AA%E3%83%BC%E3%83%80%E3%83%BC%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6)
- [7. ライセンス](#7-%E3%83%A9%E3%82%A4%E3%82%BB%E3%83%B3%E3%82%B9)
- [8. 連絡先](#8-%E9%80%A3%E7%B5%A1%E5%85%88)
- [9. 参考リンク](#9-%E5%8F%82%E8%80%83%E3%83%AA%E3%83%B3%E3%82%AF)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## 1, 概要

これは「Igo - Java 形態素解析器」の PHP による実装です。Igo は、MeCab 由来の辞書フォーマットを用い、ほぼ MeCab と同様の結果を提供する形態素解析プログラムで。この Igo-php は、Igo と同様の形態素解析と分かち書きの機能を提供します。

## 2, インストール方法

composer で以下のコマンドを実行します。

```shell
$ composer technote-fork/igo-php
```

## 3. 辞書の作成方法

Igo-php 自体は、辞書生成の機能を提供しません。従って、辞書生成に当たっては、本家の Igo を使用します。

これについての詳細は、
https://igo.osdn.jp/index.html#usage
をご覧いただくとして、簡単な手順のみを以下に示します。

- MeCab プロジェクトが配布している(もしくはそれと互換性のある)辞書を入手する。
  - https://sf.net/projects/mecab/files/mecab-ipadic/2.7.0-20070801/
  - https://osdn.jp/projects/mecab/releases/?package_id=3701
  - https://osdn.jp/projects/naist-jdic/releases/?package_id=7240
  - https://osdn.jp/projects/unidic/releases/
  - https://github.com/neologd/mecab-ipadic-neologd/releases
- Igo 本体を https://osdn.jp/projects/igo/releases/ から入手する。

これらを用いて

```shell
$ java -cp igo-0.4.5.jar net.reduls.igo.bin.BuildDic ipadic mecab-ipadic-2.7.0-20070801 EUC-JP
```

この手順でカレントディレクトリに生成された ipadic/ 以下が辞書本体です。
これを、適当なディレクトリにコピーして使用してください。

## 4. 使用方法

### a. 分かち書き

```php
<?php
require 'vendor/autoload.php';

$igo = new Igo\Tagger();
$result = $igo->wakati('すもももももももものうち');
print_r($result);
```

### b. 形態素解析

```php
<?php
require 'vendor/autoload.php';

$igo = new Igo\Tagger(['dict_dir' => '/home/user/jdic']);
$result = $igo->parse('すもももももももものうち');
print_r($result);
```

単体で使用する場合と同様に、指定した文字列から適切なエンコードが判定出来ないような場合は、Igo クラスのコンストラクト時の output_encoding の値を変更し、出力エンコードを明示的に指定することで回避できます。

```php
<?php
require 'vendor/autoload.php';

$igo = new Igo\Tagger([
    'dict_dir'        => '/home/user/jdic',
    'output_encoding' => 'Shift_JIS'
]);
```

デフォルトでは UTF-8 を優先して認識します。

### 5. REDUCE モード

これは、実行時の使用メモリを調整するためのものです。
既定では REDUCE モード TRUE で動作します。この状態で動作する時、Igo-php は、解析時に辞書ファイルに対してダイレクトアクセスします。OFF にすると、コンストラクタの実行時に、辞書を内部メモリに貯め込みます。解析処理自体は若干早くなりますが、使用メモリは増えます。比較的大量のテキストデータをバッチ処理で処理しなければならないような場合は、OFF にするとよいでしょう（しかし、このようなケースであれば、本家の Igo を使う方が、圧倒的に高速です）。
REDUCE モードを OFF にするには、以下のようにコンストラクト時に、reduce_mode を追記します。

```php
<?php
require 'vendor/autoload.php';

$igo = new Igo\Tagger([
    'dict_dir'    => '/home/user/jdic',
    'reduce_mode' => false
]);
```

なお、REDUCE モードを FALSE で使用する場合、memory_limit パラメータを適切に設定するなどの考慮が必要です。

```php
<?php
ini_set('memory_limit', '1073741824'); //1024^3
```

## 6. バイトオーダーについて

バイナリ辞書のバイトオーダーは、辞書を作成した環境によります。ビッグエンディアンな環境で生成された辞書はリトルエンディアンのプラットフォームでは使用できません。逆もまた然り。
Igo-php は、デフォルトでリトルエンディアン用の設定になっています。
Intel 系のプラットフォームであれば、このままでよいはずです。ビッグエンディアンのプラットフォームで利用する場合は、コンストラクト時に little_endian の値を変更してみてください。

```php
<?php
require 'vendor/autoload.php';

$igo = new Igo\Tagger(array(
    'dict_dir'      => '/home/user/jdic',
    'little_endian' => true //true->Little endian, false->Big endian
));
```

## 7. ライセンス

MIT ライセンスで配布いたします。
詳しくは同梱の COPYING ファイルを参照のこと。なお、使用する辞書のライセンスに関しては、辞書配布元のそれに準ずることとします。

## 8. 連絡先

- igo-php-devel@lists.osdn.jp
- technote.space@gmail.com

## 9. 参考リンク

「Igo - Java 形態素解析器」 https://igo.osdn.jp/
