<?php
/*
Plugin Name: PukiWiki
Version: 0.3
Plugin URI: http://www.kowa.org/
Description:PukiWiki Render
Author: nobunobu
Author URI: http://www.kowa.org/
*/
function pukiwiki($text) {
	include (dirname(__FILE__).'/modPukiWiki/PukiWiki.php') ;

	$text = stripslashes($text);
	//<!--more-->への対応
	$text = preg_replace("/\s*<a href='(.*?)#more-(.*?)'>(.*?)<\/a>/","\n\nRIGHT:[[\\3:\\1#more-\\2]]",$text);
	$text = preg_replace("/\s*<a id=\"more-(.*?)\"><\/a>/","\n\n&aname(more-\\1);",$text);
	

	$render = &new PukiWikiRender;
//以下のオプションは必要に応じてコメントをはずして下さい。
//PukiWikiModやB-WikiへのAutoLinkを有効にする
//	PukiWikiConfig::setParam("autolink",3);

//PukiWikiレンダリングキャッシュを使用する
//	PukiWikiConfig::setParam("use_cache",1);

//PukiWikiModテーブル拡張書式を使用する
//	PukiWikiConfig::setParam("ExtTable",1);

//WordPress記事へのInterWikiNameを定義する
//	PukiWikiConfig::addInterWiki('['.XOOPS_URL.'/modules/wordpress/index.php? WordPress]');

	$retstr = $render->transform($text);
	unset($render);
	return $retstr;
}

function pukiwiki_com($text) {
	$text=preg_replace("/^\<strong\>(.*?)\<\/strong\>\n/","''\\1''~\n",$text);
	return pukiwiki($text);
}
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
remove_filter('the_content', 'convert_bbcode');
remove_filter('the_content', 'convert_gmcode');
remove_filter('the_content', 'convert_smilies');
remove_filter('the_content', 'convert_chars');

remove_filter('the_excerpt', 'wpautop');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('the_excerpt', 'convert_bbcode');
remove_filter('the_excerpt', 'convert_gmcode');
remove_filter('the_excerpt', 'convert_smilies');
remove_filter('the_excerpt', 'convert_chars');

remove_filter('comment_text', 'wpautop');
remove_filter('comment_text', 'wptexturize');
remove_filter('comment_text', 'wp_filter_kses');
remove_filter('comment_text', 'convert_bbcode');
remove_filter('comment_text', 'convert_gmcode');
remove_filter('comment_text', 'balanceTags');
remove_filter('comment_text', 'convert_smilies');
remove_filter('comment_text', 'convert_chars');

add_filter('the_content', 'pukiwiki', 6);
add_filter('the_excerpt', 'pukiwiki', 6);
add_filter('comment_text', 'pukiwiki_com', 6);
?>