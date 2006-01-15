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
//		$url = "http://weather.is.kochi-u.ac.jp/FE/00Latest.jpg";
		$url = "http://www.jwa.or.jp/sat/images/sat-japan.jpg";
		$alt = "�����ն��ֳ�����";
		$picid = "pic";
		$args[0] = $args[1];
		$size[0] = 640;
		$size[1] = 480;
	} else {
		$url = "http://www.jma.go.jp/jp/g3/images/observe/";
		$alt = "����ģȯɽŷ����";
		$picid = "";
		$size[0] = 528;
		$size[1] = 512;
		$url .= date("ymd",time()).date("H",floor(time()/(3600*3)-1)*3600*3).'.png';
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
		$result = PukiWikiFunc::http_request($target);
		if ($result['rc'] == 200) {
			$data = $result['data'];
			plugin_tenki_cache_image_save($data, $filename);
			$size = @getimagesize($filename);
			$url = MOD_PUKI_UPLOAD_URL.$id."_tenki.gif";
		} else {
			$url = '';
			$size = false;
		}
	} else {
		$url = MOD_PUKI_UPLOAD_URL.$id."_tenki.gif";
		$size = @getimagesize($filename);
	}
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
