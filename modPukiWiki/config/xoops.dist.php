<?php
//XOOPS固有の設定(以下の２つは変えない事を推奨
/////////////////////////////////////////////////
// 改行を反映する(改行を<br />に置換する)
	PukiWikiConfig::setParam('line_break',1);
/////////////////////////////////////////////////
// URLの自動リンク生成はmodPukiWiki側では行わない
	PukiWikiConfig::setParam('autourllink',0);
//
//カストマイズ可能な代表的な設定例
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
	PukiWikiConfig::setParam('use_static_url',1);
?>
