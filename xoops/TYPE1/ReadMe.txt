*****************************************************************************
** modPukiWiki XOOPS2 Hack TYPE 1
**
*****************************************************************************
PukiWiki1.4.xをベースにしたレンダリングエンジンPukiWikiModをXOOPSに組み込むHackです。

PukiWikiModの作者、nao-ponさんのHackをベースにしています。
XOOPSのサニタイザを使っているモジュールで PukiWiki書式が使用できるようになります。

こちらのHackでは、入力したすべてのテキストがWiki変換の対象となります。
XOOPSの従来のBBcodeやSmilyもそのままで使えるはずですが、一つのコンテンツの中で、
変に併用すると予期しない結果になる場合も起こり得ますのでご注意下さい。
但し、HTMLが有効になっている場合には、PukiWikiと競合するのでPukiWiki書式は
使用出来ません。

このHackは、XOOPS2.0.7のファイルをベースにしています。

[導入方法］
１．このHackは以下のファイルを上書きしますので、導入前にバックアップをとって下さい。
　　・class/module.textsanitizer.php
　　・themes/default/style.css

２．XOOPS_ROOT_PATH/class/ 以下に modPukiWiki本体をディレクトリごと配置して下さい。
　　（*** 注意：XOOPS HACK専用モジュールをダウンロードされた方はこの作業は不用です）

３．XOOPS_ROOT_PATH 以下に htmlディレクトリ以下のファイルをディレクトリごと配置して下さい。

４．使用しているテーマのスタイルシートに、themes/default/modPukiWiki.css の内容を追加して下さい。
　　themes/default/style.cssと同様に、cssファイルの最下行に
	----------------------------------------------
	/* Add For modPukiWiki by nobunobu */
	@import url(modPukiWiki.css); 
	/* Add For modPukiWiki by nobunobu */
	----------------------------------------------
　　を追加して頂いても良いと思います。
　　（その前に、themes/default/modPukiWiki.cssを使用されているテーマディレクトリにコピーして下さい）

５．お好みに応じて、class/modPukiWiki/xoops.dist.phpを編集して、modPukiWikiのカストマイズを
　　行って下さい。
　　******************* ご注意 *******************
　　このファイルを直接編集頂いても良いですが、バージョンアップ時に上書きされるのを防ぐために
　　このファイル(xoops.dist.php)をxoops.phpにコピーしてから、カストマイズする事をおすすめします。
