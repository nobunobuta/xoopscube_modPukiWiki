<?php
/////////////////////////////////////////////////
//スタイルclassのプレフィックス
	PukiWikiConfig::setParam('style_prefix','modPukiWP_');
/////////////////////////////////////////////////
// AutoLinkを有効にする場合は、AutoLink対象となる
// ページ名の最短バイト数を指定
// AutoLinkを無効にする場合は0
//	PukiWikiConfig::setParam('autolink',3);
/////////////////////////////////////////////////
// 拡張テーブル書式を使用する
//	PukiWikiConfig::setParam('ExtTable',true);
/////////////////////////////////////////////////
// レンダリングキャッシュを有効にする
//	PukiWikiConfig::setParam('use_cache',1);
/////////////////////////////////////////////////
// PukiWikiModへのリンクを静的URL形式にする
//	PukiWikiConfig::setParam('use_static_url',1);
/////////////////////////////////////////////////
// InterWikiNameの定義サンプル
//	PukiWikiConfig::addInterWiki('[http://www.google.co.jp/search?ie=utf8&oe=utf8&q=$1&lr=lang_ja&hl=ja Google] utf8');
//	PukiWikiConfig::addInterWiki('['.XOOPS_URL.'/modules/wordpress/index.php? WordPress]');
?>
