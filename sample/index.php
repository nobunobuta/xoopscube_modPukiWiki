<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=EUC-JP" />
<meta http-equiv="content-language" content="ja" />
<title>modPukiWikiサンプル</title>
<!-- サンプルのスタイルシートを読み込む。場所は環境によって設定 -->
<link rel="stylesheet" type="text/css" media="all" href="../modpukiwiki/sample.css" />
</head>
<body>
<?php
/*
 * アップロードファイル関連(#ref #isbn等の画像キャッシュ用に使用)
 *    (これは、本来 modpukiwiki/PukiWiki.ini.phpに定義すべき内容です。
 *     ここでは、当ファイルのディレクトリにattachという書込可能なディレクトリが
 *     ある事を前提にしています）
 *
 * XOOPS環境下では、uploadsディレクトリに自動設定
 * WordPress環境下では、Fileアップロード関連の設定を参照して自動設定
 */
//アップロードファイルのディレクトリ （最後は "/" で終わる事）
if (!defined('MOD_PUKI_UPLOAD_DIR')) define('MOD_PUKI_UPLOAD_DIR','./attach/');

//アップロードファイルのURL （最後は "/" で終わる事）
if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL','./attach/');
//この部分は環境によって変更して下さい。
//modPukiWiki本体のインクルード
require (dirname(__FILE__).'/../modPukiWiki/PukiWiki.php') ;
//PukiWikiのサンプルソース
$text = <<< EOD
***ヘッディング
 *ヘッディング１
 文章１
 **ヘッディング２
 文章２
 ***ヘッディング３
 文章３
*ヘッディング１
文章１
**ヘッディング２
文章２
***ヘッディング３
文章３
***リンク
 [[ホームページ:http://www.kowa.org]]
[[ホームページ:http://www.kowa.org]]

***テーブル
 |a|a|b|
 |100|300|300|

|a|a|b|
|100|300|300|
***リスト
 -asdsad
 --dsadasd
-asdsad
--dsadasd
***番号付きリスト
 +test
 ++test2
 ++test3
 +++testtest
 ++test4
 +test2
 ++test5
 ---ttttt
 ++test4
+test
++test2
++test3
+++testtest
++test4
+test2
++test5
---ttttt
++test4
***区切り線
 ----
----
***強調
 ''aaaa''
''aaaa''
***インラインプラグイン
 &color(RED){TEST};~
 &font(RED,15){TEST};
&color(RED){TEST};~
&font(RED,15){TEST};
***PukiWIkiMod ISBNプラグインのテスト
 #isbn(4839912653,left)
 #isbn(B0000CAVZK,left)
 #isbn(B00008HC56,left)
 #isbn(clear)
#isbn(4839912653,left)
#isbn(B0000CAVZK,left)
#isbn(B00008HC56,left)
#isbn(clear)
~
***ShowRSSプラグインのテスト
 #showrss(http://www.kowa.org/modules/wordpress/wp-rdf.php, anntenna)
#showrss(http://www.kowa.org/modules/wordpress/wp-rdf.php, antenna)
***画像表示、ファイル添付
 &ref(http://www.kowa.org/modules/wordpress/attach/1086419759.jpeg,w:200);~
&ref(http://www.kowa.org/modules/wordpress/attach/1086419759.jpeg,w:200);~
***InterWikiも実装
 -[[のぶのぶの検索:Google:のぶのぶ]]
-[[のぶのぶの検索>Google:のぶのぶ]]
EOD;

//PukiWikiRenderインスタンス生成
$render = new PukiWikiRender;
//InterWikiNameの設定
PukiWikiConfig::addInterWiki('[http://www.google.co.jp/search?ie=utf8&oe=utf8&q=$1&lr=lang_ja&hl=ja Google] utf8');
//レンダリングして表示
echo $render->transform($text);
?>
</body>
</html>
