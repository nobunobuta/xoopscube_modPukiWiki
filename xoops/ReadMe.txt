PukiWikiModをXOOPSに組み込むHackです。

PukiWikiModの作者、nao-ponさんのHackをベースにしています。
XOOPSのサニタイザを使っているモジュールで PukiWiki書式が使用できるようになります。
XOOPSの従来のBBcodeやSmilyもそのままで使えるはずですが、一つのコンテンツの中で、
変に併用すると予期しない結果になる場合も起こり得ますのでご注意下さい。

このHackは、XOOPS2.0.7のファイルをベースにしています。

[導入方法］
１．XOOPS_ROOT_PATH/class/ 以下に modPukiWiki をディレクトリごと配置して下さい。
２．class/module.textsanitizer.phpをXOOPS_ROOT_PATH/class/以下の物と置き換えて下さい。
３．modPukiWiki/sample.cssの中身を使用しているテーマのスタイルシートにコピーして下さい。

参考）
class/nao-pon-hack/module.textsanitizer.php
に、nao-ponさんのハックを載せておきます。詳しくは、
http://hypweb.net/xoops/modules/pukiwiki/1324.html
を参照して下さい。

