<?php
// $Id$
/*
Last-Update:2002-10-29 rev.33

*�ץ饰���� ref
�ڡ�����ź�դ��줿�ե������Ÿ������

*Usage
 #ref(filename[,Page][[,{Left|Center|Right}]|[,{Wrap|Nowrap}]|[,Around]]{}[,comments])

*�ѥ�᡼��
-filename~
 ź�եե�����̾�����뤤��URL
-Page~
 WikiName�ޤ���BracketName����ꤹ��ȡ����Υڡ�����ź�եե�����򻲾Ȥ���
-Left|Center|Right~
 ���ΰ��ֹ�碌
-Wrap|Nowrap~
 �ơ��֥륿���ǰϤ�/�Ϥޤʤ�
-Around~
 �ƥ����Ȥβ�����
-nocache~
 URL�����ե�����(�����ե�����)�򥭥�å��夷�ʤ�
-w:�ԥ������
-h:�ԥ������
-����%
 �����ե�����Υ��������ꡣ
 w: h: �ɤ��餫�λ���ǽĲ�����Ψ���ݤäƥꥵ������
 %����ǡ�����Υѡ�����Ȥ�ɽ����
-t:�����ȥ�
 �����Υ��åץƥ����Ȥ����

*/

// Modified for modPukiWiki by nobunobu
//   2004/08/10 : PukiWikiź�եե�����ϰ����ʤ��Τ�URL���ȤΤߤ˽���

// GD�ΥС�����󥻥å�(ɬ���Ķ��˹�碌��)
//if (!defined('_WIKI_GD_VERSION')) define('_WIKI_GD_VERSION',1); // Ver 1
if (!defined('_WIKI_GD_VERSION')) define('_WIKI_GD_VERSION',2); // Ver 2

// file icon image
if (!defined('REF_FILE_ICON')) define('REF_FILE_ICON','<img src="'.MOD_PUKI_UPLOAD_URL.'file.gif" alt="file" width="20" height="20" />');

// default alignment
if (!defined('REF_DEFAULT_ALIGN')) define('REF_DEFAULT_ALIGN','left'); // 'left','center','right'

// force wrap on default
if (!defined('REF_WRAP_TABLE')) define('REF_WRAP_TABLE',FALSE); // TRUE,FALSE

function plugin_ref_inline() {

	//�����
	$ret = '';

	//���顼�����å�
	if (!func_num_args()) return 'no argument(s).';

	//ź�եե�����̾�����
	$args = func_get_args();
	$name = array_shift($args);

	//�ѥ�᡼���Ѵ�
	$params = array('_args'=>array(),'nocache'=>FALSE,'_size'=>FALSE,'_w'=>0,'_h'=>0,'_%'=>'');
	
	foreach($args as $val){
		if ($val == 'nocache') {
			$params['nocache'] = TRUE;
			continue;
		}
		$params['_args'][] = $val;
	}

	$rets = plugin_ref_body($name,$args,$params);
	if (!empty($rets['_error'])) {
		$ret = $rets['_error'];
	} else {
		$ret = $rets['_body'];
	}
	unset($name,$args,$params,$rets);

	return $ret;
}

function plugin_ref_convert() {

	//�����
	$ret = '';

	//���顼�����å�
	if (!func_num_args()) return 'no argument(s).';

	//ź�եե�����̾�����
	$args = func_get_args();
	$name = array_shift($args);

	//�ѥ�᡼���Ѵ�
	$params = array('left'=>FALSE,'center'=>FALSE,'right'=>FALSE,'wrap'=>FALSE,'nowrap'=>FALSE,'around'=>FALSE,'_args'=>array(),'_done'=>FALSE,'nocache'=>FALSE,'_size'=>FALSE,'_w'=>0,'_h'=>0,'_%'=>'');
	array_walk($args, 'ref_check_arg', &$params);

	$rets = plugin_ref_body($name,$args,$params);
	if ($rets['_error']) {
		$ret = $rets['_error'];
	} else {
		$ret = $rets['_body'];
	}

	//���饤�����Ƚ��
	if ($params['right'])
		$align = 'right';
	else if ($params['left'])
		$align = 'left';
	else if ($params['center'])
		$align = 'center';
	else
		$align = REF_DEFAULT_ALIGN;

	if ((REF_WRAP_TABLE and !$params['nowrap']) or $params['wrap']) {
		$ret = wrap_table($ret, $align, $params['around']);
	}
	$ret = wrap_div($ret, $align, $params['around']);
	unset($name,$args,$params,$rets);

	return $ret;
}

//-----------------------------------------------------------------------------
// �������ɤ���
function is_picture($text) {
	//����å��������å�
	if (PukiWikiFunc::is_url($text))
	{
		$parse = parse_url($text);
		$name = $parse['host']."_".basename($parse['path']);
		$filename = MOD_PUKI_UPLOAD_DIR.PukiWikiFunc::encode($name);
		if (is_readable($filename))
			$text = $filename;
	}

	$size = @getimagesize($text);
	if ($size[2] > 0 && $size[2] < 4) {
		return true;
	} else {
		return false;
	}
}
// Flash���ɤ���
function plugin_ref_is_flash($text) {
	$filename = preg_replace("/.*\//","",$text);
	return preg_match("/.*\.swf/i",$filename);
}
// div�����
function wrap_div($text, $align, $around) {
	if ($around) {
		$style = ($align == 'right') ? 'float:right' : 'float:left';
	} else {
		$style = "text-align:$align";
	}
	return "<div class=\"".PukiWikiConfig::getParam('style_prefix')."img_margin\" style=\"$style\">$text</div>\n";
}
// �Ȥ����
// margin:auto Moz1=x(wrap,around�������ʤ�),op6=oNN6=x(wrap,around�������ʤ�)IE6=x(wrap,around�������ʤ�)
// margin:0px Moz1=x(wrap�Ǵ󤻤������ʤ�),op6=x(wrap�Ǵ󤻤������ʤ�),nn6=x(wrap�Ǵ󤻤������ʤ�),IE6=o
function wrap_table($text, $align, $around) {
	$margin = ($around ? '0px' : 'auto');
	$margin_align = ($align == 'center') ? '' : ";margin-$align:0px";
	return "<table class=\"".PukiWikiConfig::getParam('style_prefix')."style_table\" style=\"margin:$margin$margin_align\">\n<tr><td class=\"".PukiWikiConfig::getParam('style_prefix')."style_td\">\n$text\n</td></tr>\n</table>\n";
}
//���ץ�������Ϥ���
function ref_check_arg($val, $_key, &$params) {
	if ($val == '') { $params['_done'] = TRUE; return; }
	if (!$params['_done']) {
		foreach (array_keys($params) as $key) {
			if (strpos($key, strtolower($val)) === 0) {
				$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}
	$params['_args'][] = $val;
}

// BodyMake
function plugin_ref_body($name,$args,$params){
	
// $name���Ȥ˰ʲ����ѿ�������
// $url : URL
// $title :�����ȥ�
// $ext : ��ĥ��Ƚ����ʸ����
// $icon : ���������img����
// $size : �����ե�����ΤȤ�������
// $info : �����ե�����ʳ��Υե�����ξ���
//  ź�եե�����ΤȤ� : �ե�����κǽ��������ȥ�����
//  URL�ΤȤ� : URL���Τ��
	$ret = "";
	if (PukiWikiFunc::is_url($name)) { //URL
		$url = $ext = $info = htmlspecialchars($name);
		$icon = $size = '';
		$l_url = $url;
		$icon = REF_FILE_ICON;
		if (preg_match('/([^\/]+)$/', $name, $match)) { $ext = $match[1]; }
	}

	//�����ȥ�����
	if (!isset($title) or $title == '') { $title = $ext; }
	$title = htmlspecialchars($title);

	// �ե��������Ƚ��
	if (!isset($is_picture)) $is_picture = is_picture($url);
	if (!isset($is_flash)) $is_flash = plugin_ref_is_flash($url);
	if ($is_picture) { // ����
		$info = "";
		$width=$height=0;
		//URL�ξ�祭��å���Ƚ��
		if (PukiWikiFunc::is_url($url)){
			$parse = parse_url($url);
			$name = $parse['host']."_".basename($parse['path']);
			$filename = PukiWikiFunc::encode($name);
			if (!$params['nocache']){
				//����å��夹��
				$size = plugin_ref_cache_image_fetch($filename, &$url);
				$fsize = sprintf('%01.1f',round(filesize(MOD_PUKI_UPLOAD_DIR.$filename)/1000,1)).'KB';
				$l_url = $url;
			} else {
				//����å��夷�ʤ�
				$size = @getimagesize($url);
				$l_url = $url;
				$fsize = '?KB';
			}
			$org_w = $size[0];
			$org_h = $size[1];
		}
		foreach ($params['_args'] as $arg){
			if (preg_match("/^(m)?w:([0-9]+)$/i",$arg,$m)){
				$params['_size'] = TRUE;
				$params['_w'] = $m[2];
				$max_flg = $m[1];
			}
			if (preg_match("/^(m)?h:([0-9]+)$/i",$arg,$m)){
				$params['_size'] = TRUE;
				$params['_h'] = $m[2];
				$max_flg = $m[1];
			}
			if (preg_match("/^([0-9.]+)%$/i",$arg,$m)){
				$params['_%'] = $m[1];
			}
			if (preg_match("/^t:(.*)$/i",$arg,$m)){
				$m[1] = htmlspecialchars(str_replace("&amp;quot;","",$m[1]));
				if ($m[1]) $title = $m[1]."&#13;&#10;".$title;
			}
		}
		// ���ꤵ�줿����������Ѥ���
		if ($params['_size']) {
			if ($params['_w'] > 0 && $params['_h'] > 0 && !$max_flg){
				$width = $params['_w'];
				$height = $params['_h'];
			} else {
				$_w = $params['_w'] ? $org_w / $params['_w'] : 0;
				$_h = $params['_h'] ? $org_h / $params['_h'] : 0;
				$zoom = max($_w,$_h);
				if ($zoom) {
					if (!$max_flg || ($zoom >= 1 && $max_flg)){
						$width = floor($org_w / $zoom);
						$height = floor($org_h / $zoom);
					}
				}
			}
		}
		if ($params['_%']) {
			$width = floor($org_w * $params['_%'] / 100);
			$height = floor($org_h * $params['_%'] / 100);
		}
		if ($org_w && $width && $org_h && $height){
			$zoom = floor(max($width/$org_w,$height/$org_h)*100);
		}
		$title .= "&#13;&#10;SIZE:{$org_w}x{$org_h}($fsize)";
		// &amp;���Ѵ�
		$title = str_replace("&amp;","&",$title);
		//IE�ʳ��ϲ���ʸ���򥹥ڡ������Ѵ�
		if ( !strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) $title = str_replace("&#13;&#10;"," ",$title);
		
		if ($width && $height) {
			$s_file_base = PukiWikiFunc::encode($zoom."%".$name);
			$s_file = MOD_PUKI_UPLOAD_DIR.$s_file_base;
			if (!file_exists($s_file) && ($zoom < 90) && (!$params['nocache'])) {
				//����ͥ������
				$url = plugin_ref_make_thumb($url,$s_file_base,$width,$height,$org_w,$org_h);
			} else {
				if (file_exists($s_file)) {
					//����ͥ��뤬����Х���ͥ���򻲾�
					$url = MOD_PUKI_UPLOAD_URL.$s_file_base;
				}
			}
			$info = "width=\"$width\" height=\"$height\" ";
			$ret .= "<a href=\"$l_url\" title=\"$title\"><img src=\"$url\" alt=\"$title\" title=\"$title\" $info/></a>";
		} else {
			if ($org_w and $org_h) $info = "width=\"$org_w\" height=\"$org_h\" ";
			if (!$params['nocache'])
				$ret .= "<img src=\"$url\" alt=\"$title\" title=\"$title\" $info/>";
			else
				$ret .= "<img src=\"$url\" alt=\"$title\" title=\"$title\" $info/>";
		}
	} else if ($is_flash) { //	Flash�ե�����
		//�����
		$params['_qp']  =
		$params['_q']   =
		$params['_pp']  =
		$params['_p']   =
		$params['_lp']  =
		$params['_l']   =
		$params['_w']   =
		$params['_h']   =
		$params['_a']   =
		$params['_bp']  =
		$params['_b']   =
		$params['_scp'] =
		$params['_sc']  =
		$params['_sap'] =
		$params['_sa']  =
		$params['_mp']  =
		$params['_m']   =
		$params['_wmp'] = "";
		
		foreach ($params['_args'] as $arg){
			if (preg_match("/^q(?:uality)?:((auto)?(high|low|best|medium))$/i",$arg,$m)){
				$params['_qp'] = "<param name=\"quality\" value=\"{$m[1]}\">";
				$params['_q']  = " quality=\"{$m[1]}\"";
			}
			if (preg_match("/^p(?:lay)?:(true|false)$/i",$arg,$m)){
				$params['_pp'] = "<param name=\"play\" value=\"{$m[1]}\">";
				$params['_p']  = " play=\"{$m[1]}\"";
			}
			if (preg_match("/^l(?:oop)?:(true|false)$/i",$arg,$m)){
				$params['_lp'] = "<param name=\"loop\" value=\"{$m[1]}\">";
				$params['_l']  = " loop=\"{$m[1]}\"";
			}
			if (preg_match("/^w(?:idth)?:([0-9]+)$/i",$arg,$m)){
				$params['_w'] = " width=".$m[1];
			}
			if (preg_match("/^h(?:eight)?:([0-9]+)$/i",$arg,$m)){
				$params['_h'] = " height=".$m[1];
			}
			if (preg_match("/^a(?:lign)?:(l|r|t|b)$/i",$arg,$m)){
				$params['_a'] = " align=\"{$m[1]}\"";
			}
			if (preg_match("/^b(?:gcolor)?:#?([abcdef\d]{6,6})$/i",$arg,$m)){
				$params['_bp'] = "<param name=\"bgcolor\" value=\"{$m[1]}\">";
				$params['_b']  = " bgcolor=\"#{$m[1]}\"";
			}
			if (preg_match("/^sc(?:ale)?:(showall|noborder|exactfit|noscale)$/i",$arg,$m)){
				$params['_scp'] = "<param name=\"scale\" value=\"{$m[1]}\">";
				$params['_sc']  = " scale=\"{$m[1]}\"";
			}
			if (preg_match("/^sa(?:lign)?:(l|r|t|b|tl|tr|bl|br)$/i",$arg,$m)){
				$params['_sap'] = "<param name=\"salign\" value=\"{$m[1]}\">";
				$params['_sa']  = " salign=\"{$m[1]}\"";
			}
			if (preg_match("/^m(?:enu)?:(true|false)$/i",$arg,$m)){
				$params['_mp'] = "<param name=\"menu\" value=\"{$m[1]}\">";
				$params['_m']  = " menu=\"{$m[1]}\"";
			}
			if (preg_match("/^wm(?:ode)?:(window|opaque|transparent)$/i",$arg,$m)){
				$params['_wmp'] = "<param name=\"wmode\" value=\"{$m[1]}\">";
			}
		}
		$ret .= <<<_HTML_
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,79,0"{$params['_w']}{$params['_h']}{$params['_a']}>
<param name="movie" value="{$file}">
{$params['_qp']}{$params['_lp']}{$params['_pp']}{$params['_scp']}{$params['_sap']}{$params['_mp']}{$params['_wmp']}
<embed src="{$file}" pluginspage="http://www.macromedia.com/jp/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"{$params['_w']}{$params['_h']}{$params['_a']}{$params['_p']}{$params['_l']}{$params['_q']}{$params['_b']}{$params['_sc']}{$params['_sa']}{$params['_m']}>
</embed>
</object>
_HTML_;

	} else { // �̾�ե�����
		foreach ($params['_args'] as $arg){
			if (preg_match("/^t:(.*)$/i",$arg,$m)){
				$m[1] = htmlspecialchars(str_replace("&amp;quot;","",$m[1]));
				if ($m[1]) $info = $m[1]."&#13;&#10;".$info;
			}
		}
		// &amp;���Ѵ�
		$info = str_replace("&amp;","&",$info);
		//IE�ʳ��ϲ���ʸ���򥹥ڡ������Ѵ�
		if ( !strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) $info = str_replace("&#13;&#10;"," ",$info);

		$ret .= "<a href=\"$l_url\" title=\"$info\">$icon$title</a>";
	}
	$rets['_body'] = $ret;
	return $rets;
}

// ��������å��夬���뤫Ĵ�٤�
function plugin_ref_cache_image_fetch($id, &$url) {
	$filename = MOD_PUKI_UPLOAD_DIR.$id;
	if (!is_readable($filename)) {
		$file = fopen($url, "rb"); // ���֤� size ������ꤳ���餬����Ū��������®��
		if (! $file) {
			fclose($file);
			$url = MOD_PUKI_NOIMAGE;
			$size = @getimagesize($url);
		} else {
			// ��⡼�ȥե�����Υѥ��å�ͭ�����к�
			// http://search.net-newbie.com/php/function.fread.html
			//$data = fread($file, 2000000); 
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
			
			$size = @getimagesize($url); // ���ä��顢size ��������̾��1���֤뤬ǰ�Τ���0�ξ���(reimy)
			if ($size[0] <= 1){
				$url = MOD_PUKI_NOIMAGE;
				$size = @getimagesize($url);
			}else{
				$url = MOD_PUKI_UPLOAD_URL.$id;
			}
		}
		plugin_ref_cache_image_save($data, $filename);
	} else {
		$url = MOD_PUKI_UPLOAD_URL.$id;
		$size = @getimagesize($filename);
	}
	
	return $size;
	
}
// ��������å������¸
function plugin_ref_cache_image_save($data, $filename) {
	$fp = fopen($filename, "wb");
	fwrite($fp, $data);
	fclose($fp);
	return $filename;
}
// ����ͥ�����������
function plugin_ref_make_thumb($url,$s_file_base,$width,$height,$org_w,$org_h) {
	$s_file = MOD_PUKI_UPLOAD_DIR.$s_file_base;
	// GD fuction �Υ����å�
	if (!function_exists("ImageCreate")) return $url;//GD�򥵥ݡ��Ȥ��Ƥ��ʤ�

	$gifread = '';
	if (_WIKI_GD_VERSION == 2) {
		$imagecreate = "ImageCreateTrueColor";
		$imageresize = "ImageCopyResampled";
	}else {
		$imagecreate = "ImageCreate";
		$imageresize = "ImageCopyResized";
	}
	if (function_exists ("ImageCreateFromGif")) {
		$gifread = "on";
	}

	$size = @GetImageSize($url);

	$dst_im = $imagecreate($width,$height);
	switch($size[2]):
		case "1": //gif����
			if ($gifread == "on"){
				$src_im = ImageCreateFromGif($url);
				$imageresize ($dst_im,$src_im,0,0,0,0,$width,$height,$size[0],$size[1]);
				ImageJpeg($dst_im,$s_file);
				$url = MOD_PUKI_UPLOAD_URL.$s_file_base;;
			}
			break;
		case "2": //jpg����
			$src_im = ImageCreateFromJpeg($url);
			$imageresize ($dst_im,$src_im,0,0,0,0,$width,$height,$size[0],$size[1]);
			ImageJpeg($dst_im,$s_file);
			$url = MOD_PUKI_UPLOAD_URL.$s_file_base;;
			break;
		case "3": //png����
			$src_im = ImageCreateFromPng($url);
			$imageresize ($dst_im,$src_im,0,0,0,0,$width,$height,$size[0],$size[1]);
			ImageJpeg($dst_im,$s_file);
			$url = MOD_PUKI_UPLOAD_URL.$s_file_base;;
			break;
		default:
			break;
	endswitch;
	return $url;
}
?>
