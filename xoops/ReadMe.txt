PukiWikiModをXOOPSに組み込むHackです。
環境や用途によって、４種類を用意しています。

TYPE1: XOOPSのサニタイザを使っているモジュールでTextArea内に入力したテキストを
　　　 PukiWiki書式として扱います。

TYPE2: XOOPSのサニタイザを使っているモジュールでTextArea内に入力したテキストで
　　　 [wiki][/wiki]で囲んだ文字列部分をPukiWiki書式として扱います。

それぞれに、一長一短がありますが特徴や導入方法については、各ディレクトリ内の
ReadMe.txtを参照して下さい。

このHackは、XOOPS2.0.7.3のファイルをベースにしています。

TYPE1_ML 及び TYPE2_ML は、XOOPS MultiLanguages Hackを適用している場合に対応した
HACKファイルです。

参考）
contrib/nao-pon hack/module.textsanitizer.php
に、nao-ponさんのハックを載せておきます。詳しくは、
http://hypweb.net/xoops/modules/pukiwiki/1324.html
を参照して下さい。

contrib/PukiWikiHelper
XOOPSのTextArea入力コントロールにPukiWiki用の書式ヘルパーを追加するハックです。
