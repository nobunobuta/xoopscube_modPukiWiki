<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
// *0.5: URL が存在しない場合、画像を表示しない。
//			 Thanks to reimy.
//	 GNU/GPL にしたがって配布する。
//

/////////////////////////////////////////////////
// AmazonアソシエイトID
if (!defined('MOD_PUKI_ISBN_AMAZON_ASE_ID')) define('MOD_PUKI_ISBN_AMAZON_ASE_ID','nobunobuxoops-22');
// amazon 商品情報問合せ URI(dev-t はマニュアルのディフォルト値)
if (!defined('MOD_PUKI_ISBN_AMAZON_XML'))
	define('MOD_PUKI_ISBN_AMAZON_XML','http://xml.amazon.co.jp/onca/xml3?t=webservices-20&dev-t=GTYDRES564THU&type=lite&page=1&f=xml&locale=jp&AsinSearch=');
// amazon shop URI (_ISBN_ に商品IDがセットされる)
if (!defined('MOD_PUKI_ISBN_AMAZON_SHOP'))
	define('MOD_PUKI_ISBN_AMAZON_SHOP','http://www.amazon.co.jp/exec/obidos/ASIN/_ISBN_/ref=nosim/'.MOD_PUKI_ISBN_AMAZON_ASE_ID);
// amazon UsedShop URI (_ISBN_ に商品IDがセットされる)
if (!defined('MOD_PUKI_ISBN_AMAZON_USED'))
	define('MOD_PUKI_ISBN_AMAZON_USED','http://www.amazon.co.jp/exec/obidos/tg/detail/offer-listing/-/_ISBN_/all/ref='.MOD_PUKI_ISBN_AMAZON_ASE_ID);

/////////////////////////////////////////////////
// expire 画像キャッシュを何日で削除するか
if (!defined('MOD_PUKI_ISBN_AMAZON_EXPIRE_IMG')) define('MOD_PUKI_ISBN_AMAZON_EXPIRE_IMG',10);
// expire タイトルキャッシュを何日で削除するか
if (!defined('MOD_PUKI_ISBN_AMAZON_EXPIRE_TIT')) define('MOD_PUKI_ISBN_AMAZON_EXPIRE_TIT',1);


function plugin_isbn_convert() {
	if (func_num_args() < 1 or func_num_args() > 3) {
		return false;
	}
	$aryargs = func_get_args();
	$isbn = htmlspecialchars($aryargs[0]);	// for XSS
	$isbn = str_replace("-","",$isbn);
	$title = '';
	$header = '';
	$align = "right"; //規定値
	$listprice ='';
	$usedprice ='';
	$img_size = "M";
	switch (func_num_args())
	{
		case 4:
			if (strtolower($aryargs[3]) == 'left') $align = "left";
			elseif (strtolower($aryargs[3]) == 'clear') $align = "clear";
			elseif (strtolower($aryargs[3]) == 'header' || $aryargs[3] == 'h') $header = "header";
			elseif (strtolower($aryargs[3]) == 'info') $header = "info";
			elseif (strtolower($aryargs[3]) == 'img' || $aryargs[3] == 'image') $title = "image";
			elseif (strtolower($aryargs[3]) == 'small' || $aryargs[3] == 's') $img_size = "T";
			else $title = htmlspecialchars($aryargs[2]);
		case 3:
			if (strtolower($aryargs[2]) == 'left') $align = "left";
			elseif (strtolower($aryargs[2]) == 'clear') $align = "clear";
			elseif (strtolower($aryargs[2]) == 'header' || $aryargs[2] == 'h') $header = "header";
			elseif (strtolower($aryargs[2]) == 'info') $header = "info";
			elseif (strtolower($aryargs[2]) == 'img' || $aryargs[2] == 'image') $title = "image";
			elseif (strtolower($aryargs[2]) == 'small' || $aryargs[2] == 's') $img_size = "T";
			else $title = htmlspecialchars($aryargs[2]);
		case 2:
			if (strtolower($aryargs[1]) == 'left') $align = "left";
			elseif (strtolower($aryargs[1]) == 'clear') $align = "clear";
			elseif (strtolower($aryargs[1]) == 'header' || $aryargs[1] == 'h') $header = "header";
			elseif (strtolower($aryargs[1]) == 'info') $header = "info";
			elseif (strtolower($aryargs[1]) == 'img' || $aryargs[1] == 'image') $title = "image";
			elseif (strtolower($aryargs[1]) == 'small' || $aryargs[1] == 's') $img_size = "T";
			else $title = htmlspecialchars($aryargs[1]);
		case 1:
			if (strtolower($aryargs[0]) == 'clear') 
			{
				$align = "clear";
				$isbn = "";
				$alt = '';
				$h_title = '';
				$price = '';
			}
	}
	if ($isbn)
	{
		$tmpary = plugin_isbn_get_isbn_title($isbn);
		$alt = plugin_isbn_get_caption($tmpary);
		if ($tmpary[2]) $price = "<div style=\"text-align:right;\">価格: $tmpary[2]円</div>";
		$off = 0;
		$_price = (int) trim(str_replace(",","",$tmpary[2]));
		$_listprice = (int) trim(str_replace(",","",$tmpary[8]));
		if ($_price && $_listprice && ($_price != $_listprice))
		{
			$off = 100 - (($_price/$_listprice) * 100);
			$price = "<div style=\"text-align:right;\">価格: $tmpary[8]円 ⇒ $tmpary[2]円<br />".(int)$off."% Off</div>";
		}
		
		if ($title != '') {			// タイトル指定か自動取得か
			$h_title = $title;
		} else {					// タイトル自動取得
			$title = "[ $tmpary[1] ]<br />$tmpary[0]";
			$h_title = "$tmpary[0]";
		}
	}
	if ($header != "info")
		return plugin_isbn_print_isbn_img($isbn, $align, $alt, $title, $h_title, $price, $header,$listprice,$usedprice,$img_size);
	else
	{
		return plugin_isbn_get_info($tmpary,$isbn);
	}
}

function plugin_isbn_inline() {
	list($isbn,$option) = func_get_args();
	$isbn = htmlspecialchars($isbn); // for XSS
	$isbn = str_replace("-","",$isbn);
	$tmpary = array();
	$tmpary = plugin_isbn_get_isbn_title($isbn);
	if ($tmpary[2]) $price = "<div style=\"text-align:right;\">$tmpary[2]円</div>";
	$title = "$tmpary[0]";
	$text = htmlspecialchars($option);
	$alt = plugin_isbn_get_caption($tmpary);
	$amazon_a = '<a href="'.str_replace('_ISBN_',$isbn,MOD_PUKI_ISBN_AMAZON_SHOP).'" target="_blank" title="'.$alt.'">';
	if ($option != 'img'){
		if ($option) $title = $text;
		return $amazon_a . $title . '</a>';
	} else {
		$url = plugin_isbn_cache_image_fetch($isbn);
		return $amazon_a.'<img src="'.$url.'" alt="'.$alt.'" /></a>';
	}
}

function plugin_isbn_get_caption($data)
{
	$off = "";
	$_price = (int) trim(str_replace(",","",$data[2]));
	$_listprice = (int) trim(str_replace(",","",$data[8]));
	if ($_price && $_listprice && ($_price != $_listprice))
	{
		$off = (int)(100 - (($_price/$_listprice) * 100));
		$off = " ({$off}% Off)";
	}

	//改行文字セット IE は "&#13;&#10;"
	$br = (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))? "&#13;&#10;" : " ";

	$alt = "[ $data[1] ]{$br}$data[0]";
	if ($data[8]) $alt .= "{$br}価格: $data[8]円";
	if ($data[2]) $alt .= "{$br}Amazon: $data[2]円$off";
	if ($data[9]) $alt .= "{$br}USED: $data[9]円〜";
	if ($data[3]) $alt .= "{$br}著者: $data[3]";
	if ($data[4]) $alt .= "{$br}アーティスト: $data[4]";
	if ($data[5]) $alt .= "{$br}発売日: $data[5]";
	if ($data[6]) $alt .= "{$br}発売元: $data[6]";
	if ($data[7]) $alt .= "{$br}発送状況: $data[7]";
	return $alt;
}

function plugin_isbn_get_info($data,$isbn)
{
	$alt = plugin_isbn_get_caption($data);
	$amazon_a = '<a href="'.str_replace('_ISBN_',$isbn,MOD_PUKI_ISBN_AMAZON_SHOP).'" target="_blank" title="'.$alt.'">';
	$amazon_s1 = "<a href=\"http://www.amazon.co.jp/exec/obidos/external-search/?mode=blended&amp;keyword=";
	$amazon_s2 = "&amp;tag=".MOD_PUKI_ISBN_AMAZON_ASE_ID."&amp;encoding-string-jp=%93%FA%96%7B%8C%EA&amp;Go.x=14&amp;Go.y=5\" target=\"_blank\" alt=\"Amazon Serach\" title=\"Amazon Serach\">";
	if ($data[3])
	{
		$artists = array();
		foreach(split(", ",$data[3]) as $tmp)
		{
			$artists[] = $amazon_s1 . plugin_isbn_jp_enc($tmp,"sjis") . $amazon_s2 . $tmp . "</a>";
		}
		$data[3] = join(", ",$artists);
	}
	if ($data[4])
	{
		$artists = array();
		foreach(split(", ",$data[4]) as $tmp)
		{
			$artists[] = $amazon_s1 . plugin_isbn_jp_enc($tmp,"sjis") . $amazon_s2 . $tmp . "</a>";
		}
		$data[4] = join(", ",$artists);
	}
	if ($data[6])
		$data[6] = $amazon_s1 . plugin_isbn_jp_enc($data[6],"sjis") . $amazon_s2 . $data[6] . "</a>";
	
	$off = "";
	$_price = (int) trim(str_replace(",","",$data[2]));
	$_listprice = (int) trim(str_replace(",","",$data[8]));
	if ($_price && $_listprice && ($_price != $_listprice))
	{
		$off = (int)(100 - (($_price/$_listprice) * 100));
		$off = " ({$off}% Off)";
	}
	if ($data[9])
		$data[9] = '<a href="'.str_replace('_ISBN_',$isbn,MOD_PUKI_ISBN_AMAZON_USED).'" target="_blank" alt="Amazon Used Serach" title="Amazon Used Serach">'.$data[9].'円〜</a>';

	$td_title_style = " style=\"text-align:right;\" nowrap=\"true\"";
	$ret = "<div><table style=\"width:auto;\">";
	if ($data[1]) $ret .= "<tr><td$td_title_style>カテゴリー: </td><td style=\"text-align:left;\">$data[1]</td></tr>";
	if ($data[0]) $ret .= "<tr><td$td_title_style>タイトル: </td><td style=\"text-align:left;\">{$amazon_a}$data[0]</a></td></tr>";
	if ($data[8]) $ret .= "<tr><td$td_title_style>価格: </td><td style=\"text-align:left;\">$data[8]円</td></tr>";
	if ($data[2]) $ret .= "<tr><td$td_title_style>Amazon価格: </td><td style=\"text-align:left;\">$data[2]円$off</td></tr>";
	if ($data[9]) $ret .= "<tr><td$td_title_style>USED価格: </td><td style=\"text-align:left;\">$data[9]</td></tr>";
	if ($data[3]) $ret .= "<tr><td$td_title_style>著者: </td><td style=\"text-align:left;\">$data[3]</td></tr>";
	if ($data[4]) $ret .= "<tr><td$td_title_style>アーティスト: </td><td style=\"text-align:left;\">$data[4]</td></tr>";
	if ($data[5]) $ret .= "<tr><td$td_title_style>発売日: </td><td style=\"text-align:left;\">$data[5]</td></tr>";
	if ($data[6]) $ret .= "<tr><td$td_title_style>発売元: </td><td style=\"text-align:left;\">$data[6]</td></tr>";
	if ($data[7]) $ret .= "<tr><td$td_title_style>発送状況: </td><td style=\"text-align:left;\">$data[7]</td></tr>";
	$ret .= "</table></div>";
	return $ret;
}

function plugin_isbn_print_isbn_img($isbn, $align, $alt, $title, $h_title, $price, $header="",$listprice,$usedprice,$img_size)
{
	$amazon_a = '<a href="'.str_replace('_ISBN_',$isbn,MOD_PUKI_ISBN_AMAZON_SHOP).'" target="_blank" title="'.$alt.'">';
	if ($align == 'clear') {			// 改行挿入
		return '<div style="clear:both"></div>';
	}

	if (! ($url = plugin_isbn_cache_image_fetch($isbn, MOD_PUKI_UPLOAD_DIR, true, $img_size))) return false;

	if ($title == 'image') {				// タイトルがなければ、画像のみ表示
		return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 {$amazon_a}<img src="$url" alt="$alt" /></a>
</div>
EOD;
	} else {					// 通常表示
		 $img_size = GetImageSize($url);
		if (substr($isbn,0,1) == "B"){
				$code = "ASIN: ".$isbn;
		} else {
				$code = "ISBN: ".substr($isbn,0,1)."-".substr($isbn,1,3)."-".substr($isbn,4,5)."-".substr($isbn,9,1);
		}
		 if ($header != "header"){
return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em;text-align:center">
 {$amazon_a}<img src="$url" alt="$alt" /></a><br/>
 <table style="width:{$img_size[0]}px;border:0"><tr>
	<td style="text-align:left">{$amazon_a}$title</a></td>
 </tr></table>
</div>
EOD;
		} else {
return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em;text-align:center">
 {$amazon_a}<img src="$url" alt="$alt" /></a></div>
<h4 id="{$isid}" class="isbn_head">{$amazon_a}{$h_title}</a></h4>
<div style="text-align:right;">{$code}</div>
$listprice
$price
$usedprice
EOD;
		}
	}
}

function plugin_isbn_get_isbn_title($isbn,$check=true) {
	$nocache = $nocachable = 0;
	$title = '';
	$url = MOD_PUKI_ISBN_AMAZON_XML.$isbn;
	if (file_exists(MOD_PUKI_UPLOAD_DIR) === false or is_writable(MOD_PUKI_UPLOAD_DIR) === false) {
		$nocachable = 1;							// キャッシュ不可の場合
	}
	if ($title = plugin_isbn_cache_fetch($isbn, MOD_PUKI_UPLOAD_DIR, $check)) {
		list($title,$category,$price,$author,$artist,$releasedate,$manufacturer,$availability,$listprice,$usedprice) = $title;
	} else {
		$nocache = 1;				// キャッシュ見つからず
		$body = implode('', file($url));		// しかたないので取りにいく
		$body = mb_convert_encoding($body,MOD_PUKI_SOURCE_ENCODING,"UTF-8");
		$category = (preg_match("/<Catalog>(.+)<\/Catalog>/",$body,$data))? trim($data[1]) : "";
		$title = (preg_match("/<ProductName>(.+)<\/ProductName>/",$body,$data))? trim($data[1]) : "";
		$price = (preg_match("/<OurPrice>(.+)<\/OurPrice>/",$body,$data))? trim($data[1]) : "";
		$author = (preg_match_all("/<Author>(.+)<\/Author>/",$body,$data))? join(", ",$data[1]) : "";
		$artist = (preg_match_all("/<Artist>(.+)<\/Artist>/",$body,$data))? join(', ',$data[1]) : "";
		$releasedate = (preg_match("/<ReleaseDate>(.+)<\/ReleaseDate>/",$body,$data))? trim($data[1]) : "";
		$manufacturer = (preg_match("/<Manufacturer>(.+)<\/Manufacturer>/",$body,$data))? trim($data[1]) : "";
		$availability = (preg_match("/<Availability>(.+)<\/Availability>/",$body,$data))? trim($data[1]) : "";
		$listprice = (preg_match("/<ListPrice>(.+)<\/ListPrice>/",$body,$data))? trim($data[1]) : "";
		$usedprice = (preg_match("/<UsedPrice>(.+)<\/UsedPrice>/",$body,$data))? trim($data[1]) : "";
		
		$price = preg_replace("/[￥\s]+/","",$price);
		$listprice = preg_replace("/[￥\s]+/","",$listprice);
		$usedprice = preg_replace("/[￥\s]+/","",$usedprice);
	}
	if ($title != '') {				// タイトルがあれば、できるだけキャッシュに保存
		if ($nocache == 1 and $nocachable != 1) {
			plugin_isbn_cache_save("$title<>$category<>$price<>$author<>$artist<>$releasedate<>$manufacturer<>$availability<>$listprice<>$usedprice", $isbn, MOD_PUKI_UPLOAD_DIR);
		}
	} else {					// しかたない場合 ISBN:xxxxxxxx 形式のタイトル
		if ($check)
			return plugin_isbn_get_isbn_title($isbn,false);
		else
			$title = 'ISBN:' . $isbn;
	}
	$tmpary = array($title,$category,$price,$author,$artist,$releasedate,$manufacturer,$availability,$listprice,$usedprice);
	return $tmpary;
}

// キャッシュがあるか調べる
function plugin_isbn_cache_fetch($target, $dir, $check=true) {
	$filename = $dir.PukiWikiFunc::encode("ISBN".$target.".dat");
	
	if (!is_readable($filename))
		return "";
	elseif($check && MOD_PUKI_ISBN_AMAZON_EXPIRE_TIT * 3600 * 24 < time() - filemtime($filename))
		return "";

	if (!($fp = @fopen($filename, "r"))) return "";
	$title = fread($fp, 4096);
	fclose($fp);
	if (strlen($title) > 0) {
		return explode("<>",$title);
	}
	return "";
}

// 画像キャッシュがあるか調べる
function plugin_isbn_cache_image_fetch($target, $dir, $check=true, $img_size="M") {
	$filename = MOD_PUKI_UPLOAD_DIR.PukiWikiFunc::encode("ISBN".$target.$img_size.".jpg");

	if (!is_readable($filename) || (is_readable($filename) && $check && MOD_PUKI_ISBN_AMAZON_EXPIRE_IMG * 3600 * 24 < time() - filemtime($filename))) {
		$url = "http://images-jp.amazon.com/images/P/" . strtoupper($target) . ".09.".$img_size."ZZZZZZZ.jpg";
		if (!PukiWikiFunc::is_url($url)) return false; // URL 形式チェック
		$size = @getimagesize($url);
		if ($size[0] <= 1) {
			$url = "http://images-jp.amazon.com/images/P/" . strtoupper($target) . ".01.".$img_size."ZZZZZZZ.jpg";
			$size = @getimagesize($url);
			if ($size[0] <= 1) { 
				$url = $noimage = ($img_size == "M") ? MOD_PUKI_NOIMAGE : MOD_PUKI_NOIMAGE_S;
			}
		}
		if ($url != $noimage){
			$file = fopen($url, "rb");
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
			$url = $filename;
		} else {
			// キャッシュを NOIMAGE のコピーとする
			$file = fopen($url, "rb");
			if (! $file) return false;
			$data = fread($file, 100000); 
			fclose ($file);
		}
		plugin_isbn_cache_image_save($data, $target.$img_size, MOD_PUKI_UPLOAD_DIR);
		return MOD_PUKI_UPLOAD_URL.PukiWikiFunc::encode("ISBN".$target.$img_size.".jpg");;
	} else
		return MOD_PUKI_UPLOAD_URL.PukiWikiFunc::encode("ISBN".$target.$img_size.".jpg");;
}

// キャッシュを保存
function plugin_isbn_cache_save($data, $target, $dir) {
	$filename = $dir.PukiWikiFunc::encode("ISBN".$target.".dat");
	$fp = fopen($filename, "w");
	fwrite($fp, $data);
	fclose($fp);
	return $filename;
}

// 画像キャッシュを保存
function plugin_isbn_cache_image_save($data, $target, $dir) {
	
	$filename = $dir.PukiWikiFunc::encode("ISBN".$target.".jpg");

	$fp = fopen($filename, "wb");
	fwrite($fp, $data);
	fclose($fp);

	return $filename;
}

// 文字列をURLエンコード
function plugin_isbn_jp_enc($word,$mode){
	switch( $mode ){
		case "sjis" : return rawurlencode(mb_convert_encoding($word, "SJIS", "EUC-JP"));
		case "euc" : return rawurlencode($word);
		case "utf8" : return rawurlencode(mb_convert_encoding($word, "UTF-8", "EUC-JP"));
	}
	return true;
}
?>
