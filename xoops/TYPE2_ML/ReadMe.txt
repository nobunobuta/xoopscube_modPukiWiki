*****************************************************************************
** modPukiWiki XOOPS2 Hack TYPE 2
**
*****************************************************************************
PukiWiki1.4.xをベースにしたレンダリングエンジンPukiWikiModをXOOPSに組み込むHackです。
このHackは、XOOPS2.0.7.3に XOOPS Multilanguages Hackを適用した場合に使用して下さい。
http://dev.xoops.org/modules/xfmod/project/?multilanguage

XOOPSのサニタイザを使っているモジュールで PukiWiki書式が使用できるようになります。

こちらのHackでは、入力したテキスト中で[wiki][/wiki]で囲んだ部分が変換の対象となります。
Wikiで各部分を明示的に指示しなくてはいけないという煩雑さはありますが、XOOPSの従来の
BBcodeやSmilyとの親和性も良く、HTMLが有効になっている場合にも、PukiWikiと競合すること
なく、PukiWiki書式を利用出来ます。
PukiWikiエンジンが使われる部分も限られるためシステムの負荷も軽減出来ると思います。

このHackは、XOOPS2.0.7.3のファイルをベースにしています。

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

５．お好みに応じて、class/modPukiWiki/xoops_2.dist.phpを編集して、modPukiWikiのカストマイズを
　　行って下さい。
　　************************************** ご注意 **************************************
　　このファイルを直接編集頂いても良いですが、バージョンアップ時に上書きされるのを防ぐために
　　このファイル(xoops_2.dist.php)をxoops_2.phpにコピーしてから、カストマイズする事をおすすめします。
