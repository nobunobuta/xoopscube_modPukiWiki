<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
//	 GNU/GPL にしたがって配布する。
//
// Modified for modPukiWiki by nobunobu
//   2004/08/10 : キャッシュファイルの保存先とURL取得の方法を変更

function plugin_tenki_inline()
{
	$args = array();
	$args = func_get_args();
	//echo $args[0].":".$args[1];
	if ($args[0] == "pic") {
		$url = "http://weather.is.kochi-u.ac.jp/FE/00Latest.jpg";
		$alt = "日本付近赤外画像";
		$picid = "pic";
		$args[0] = $args[1];
		$size[0] = 640;
		$size[1] = 480;
	} else {
		$url = "http://www.jma.go.jp/JMA_HP/jp/g3/latest/SPAS-GG.gif";
		$alt = "気象庁発表天気図";
		$picid = "";
		$size[0] = 528;
		$size[1] = 512;
	}
	if ($args[0] == "now?") $args[0] = "";
	if (!defined('MOD_PUKI_UPLOAD_DIR'))  $args[0] = ""; //画像のキャッシュは使用出来ない。
	if ($args[0]){
		$id = $args[0].$picid;
		$id = str_replace(" ","",$id);
		$id = PukiWikiFunc::encode($id);
		$img_arg = plugin_tenki_cache_image_fetch($url, $id);
		$url = $img_arg[0];
		$size = $img_arg[1];
//	} else {
//		$size = @getimagesize($url);
//		if ($size[0] < 1) return false;
	}

	$v_width = 264;
	$v_height = floor(($v_width * $size[1])/ $size[0]);
	$body = "<a href=\"$url\"><img src=\"$url\" width=\"$v_width\" height=\"$v_height\" alt=\"$alt\" title=\"$alt\" /></a>\n";
	return $body;
}
// 画像キャッシュがあるか調べる
function plugin_tenki_cache_image_fetch($target, $id) {
	$filename = MOD_PUKI_UPLOAD_DIR.$id."_tenki.gif";
	if (!is_readable($filename)) {
		$file = fopen($target, "rb"); // たぶん size 取得よりこちらが原始的だからやや速い
		if (! $file) {
			fclose($file);
			$url = MOD_PUKI_NOIMAGE;
		} else {
			// リモートファイルのパケット有効後対策
			// http://search.net-newbie.com/php/function.fread.html
			$contents = "";
			do {
				$data = fread($file, 8192);
				if (strlen($data) == 0) {
					break;
				}
				$contents .= $data;
			} while(true);
			
			fclose ($file);
			
			$data = $contents;
			unset ($contents);
			$size = @getimagesize($target); // あったら、size を取得、通常は1が返るが念のため0の場合も(reimy)
			if ($size[0] <= 1)
				$url = MOD_PUKI_NOIMAGE;
			else
				$url = MOD_PUKI_UPLOAD_URL.$id."_tenki.gif";
		}
		plugin_tenki_cache_image_save($data, $filename);
	} else {
		$url = MOD_PUKI_UPLOAD_URL.$id."_tenki.gif";
	}
	$size = @getimagesize($filename);
	
	return array($url,$size);
}
// 画像キャッシュを保存
function plugin_tenki_cache_image_save($data, $filename) {
	$fp = fopen($filename, "wb");
	fwrite($fp, $data);
	fclose($fp);

	return $filename;
}
?>
