<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWikiの初期設定パラメータ(default)

//各PukiWikiのパターン定義
	$_settings['WikiName'] = '(?:[A-Z][a-z]+){2,}(?!\w)';
	$_settings['BracketName'] = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
	$_settings['InterWikiName'] = "(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])";
	$_settings['NotePattern'] = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// レンダリングキャッシュを有効にする
	$_settings['use_cache'] = 0;
/////////////////////////////////////////////////
// 改行を反映する(改行を<br />に置換する)
	$_settings['line_break'] = 0;
/////////////////////////////////////////////////
// <pre>の行頭スペースをひとつ取り除く
	$_settings['preformat_ltrim'] = 1;
/////////////////////////////////////////////////
// 拡張テーブル書式を使用する
	$_settings['ExtTable'] = false;
/////////////////////////////////////////////////
// 見出し行に固有のアンカーを自動挿入する
	$_settings['fixed_heading_anchor'] = 0;
	$_settings['_symbol_anchor'] = '&dagger;';
	$_settings['_symbol_noexists'] = '?';
/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
	$_settings['top'] = "";
/////////////////////////////////////////////////
// リンク表示をコンパクトにする
	$_settings['link_compact'] = 0;

/////////////////////////////////////////////////
// AutoLinkを有効にする場合は、AutoLink対象となる
// ページ名の最短バイト数を指定
// AutoLinkを無効にする場合は0
	$_settings['autolink'] = 0;
/////////////////////////////////////////////////
// URL文字列を自動的にリンク変換する場合は1
	$_settings['autourllink'] = 1;
/////////////////////////////////////////////////
// WikiNameを *無効に* する場合は1
	$_settings['nowikiname'] = 1;
/////////////////////////////////////////////////
// 日付フォーマット
	$_settings['date_format'] = 'Y-m-d';
/////////////////////////////////////////////////
// 時刻フォーマット
	$_settings['time_format'] = 'H:i:s';
/////////////////////////////////////////////////
// 曜日配列
	$_msg_week = array('日','月','火','水','木','金','土');
//	$_msg_week = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	$_settings['$weeklabels'] = $_msg_week;

//スタイルclassのプレフィックス
	$_settings['style_prefix'] = 'modPuki_';

//リスト表示のスタイル
	$_settings['_ul_left_margin'] = 0;   // リストと画面左端との間隔(px)
	$_settings['_ul_margin'] = 16;       // リストの階層間の間隔(px)
	$_settings['_ol_left_margin'] = 0;   // リストと画面左端との間隔(px)
	$_settings['_ol_margin'] = 16;       // リストの階層間の間隔(px)
	$_settings['_dl_left_margin'] = 0;   // リストと画面左端との間隔(px)
	$_settings['_dl_margin'] = 16;        // リストの階層間の間隔(px)
	$_settings['_list_pad_str'] = ' class="'.$_settings['style_prefix'].'list%d" style="padding-left:%dpx;margin-left:%dpx"';

/////////////////////////////////////////////////
// 水平線のタグ
	$_settings['hr'] = '<hr class="'.$_settings['style_prefix'].'full_hr" />';
/////////////////////////////////////////////////
// 文末の注釈の直前に表示するタグ
	$_settings['note_hr'] = '<hr class="'.$_settings['style_prefix'].'note_hr" />';

/////////////////////////////////////////////////
// HTTPリクエストにプロキシサーバを使用する
	$_settings['use_proxy'] = 0;
// proxy ホスト
	$_settings['proxy_host'] = 'proxy.xxx.yyy.zzz';
// proxy ポート番号
	$_settings['proxy_port'] = 8080;
// proxyのBasic認証が必要な場合に1
	$_settings['need_proxy_auth'] = 0;
// proxyのBasic認証用ID,PW
	$_settings['proxy_auth_user'] = 'foo';
	$_settings['proxy_auth_pass'] = 'foo_password';
// プロキシサーバを使用しないホストのリスト
	$_settings['no_proxy'] = array(
	'localhost',        // localhost
	'127.0.0.0/8',      // loopback
	'10.0.0.0/8',     // private class A
	'172.16.0.0/12',  // private class B
	'192.168.0.0/16', // private class C
	//'no-proxy.com',
	);

//置き換えルール
	$_entity_pattern = trim(join('',file(MOD_PUKI_CONFIG_DIR.'entities.dat')));

	$_rules = array(
		"COLOR\(([^\(\)]*)\){([^}]*)}"	=> '<span style="color:$1">$2</span>',
		"SIZE\(([^\(\)]*)\){([^}]*)}"	=> '<span style="font-size:$1px">$2</span>',
		"COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)"	=> '<span style="color:$1">$2</span>',
		"SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)"	=> '<span class="'.PukiWikiConfig::getParam('style_prefix').'size$1">$2</span>',
		"%%%(?!%)((?:(?!%%%).)*)%%%"	=> '<ins>$1</ins>',
		"%%(?!%)((?:(?!%%).)*)%%"	=> '<del>$1</del>',
		"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
		"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
		"__((?:(?!__).)*)__" => '<u>\\1</u>',
		'&amp;br;'	=> '<br />',
		"\r"=>"<br />\n", /* 行末にチルダは改行 */
		'^#contents$'=>'<del>#contents</del>',
		'&amp;(#[0-9]+|#x[0-9a-f]+|'.$_entity_pattern.');'=>'&$1;',
	);
?>
