<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
//	 GNU/GPL �ˤ������ä����ۤ��롣
//
// Modified for modPukiWiki by nobunobu
//   2004/08/10 : ����å���ե��������¸���URL��������ˡ���ѹ�

function plugin_tenki_inline()
{
	$args = array();
	$args = func_get_args();
	//echo $args[0].":".$args[1];
	if ($args[0] == "pic") {
		$url = "http://weather.is.kochi-u.ac.jp/FE/00Latest.jpg";
		$alt = "�����ն��ֳ�����";
		$picid = "pic";
		$args[0] = $args[1];
		$size[0] = 640;
		$size[1] = 480;
	} else {
		$url = "http://www.jma.go.jp/JMA_HP/jp/g3/latest/SPAS-GG.gif";
		$alt = "����ģȯɽŷ����";
		$picid = "";
		$size[0] = 528;
		$size[1] = 512;
	}
	if ($args[0] == "now?") $args[0] = "";
	if (!defined('MOD_PUKI_UPLOAD_DIR'))  $args[0] = ""; //�����Υ���å���ϻ��ѽ���ʤ���
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
// ��������å��夬���뤫Ĵ�٤�
function plugin_tenki_cache_image_fetch($target, $id) {
	$filename = MOD_PUKI_UPLOAD_DIR.$id."_tenki.gif";
	if (!is_readable($filename)) {
		$file = fopen($target, "rb"); // ���֤� size ������ꤳ���餬����Ū��������®��
		if (! $file) {
			fclose($file);
			$url = MOD_PUKI_NOIMAGE;
		} else {
			// ��⡼�ȥե�����Υѥ��å�ͭ�����к�
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
			$size = @getimagesize($target); // ���ä��顢size ��������̾��1���֤뤬ǰ�Τ���0�ξ���(reimy)
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
// ��������å������¸
function plugin_tenki_cache_image_save($data, $filename) {
	$fp = fopen($filename, "wb");
	fwrite($fp, $data);
	fclose($fp);

	return $filename;
}
?>
