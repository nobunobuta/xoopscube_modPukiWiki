<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //

/*
*  displayes xoopsCode buttons and target textarea to which xoopscodes are inserted
*  $textarea_id is a unique id of the target textarea
*/
function xoopsCodeTarea($textarea_id, $cols=60, $rows=15, $suffix=null)
{
	$hiddentext = isset($suffix) ? 'xoopsHiddenText'.trim($suffix) : 'xoopsHiddenText';
	//Hack for url, email ...., the anchor is for having a link on [_More...]
	echo "<a name='moresmiley'></a><img src='".XOOPS_URL."/images/url.gif' alt='url' onmouseover='style.cursor=\"hand\"' onclick='xoopsCodeUrl(\"$textarea_id\", \"".htmlspecialchars(_ENTERURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES)."\");'/>&nbsp;<img src='".XOOPS_URL."/images/email.gif' alt='email' onmouseover='style.cursor=\"hand\"' onclick='xoopsCodeEmail(\"$textarea_id\", \"".htmlspecialchars(_ENTEREMAIL, ENT_QUOTES)."\");' />&nbsp;<img src='".XOOPS_URL."/images/imgsrc.gif' alt='imgsrc' onmouseover='style.cursor=\"hand\"' onclick='xoopsCodeImg(\"$textarea_id\", \"".htmlspecialchars(_ENTERIMGURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES)."\", \"".htmlspecialchars(_IMGPOSRORL, ENT_QUOTES)."\", \"".htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES)."\");' />&nbsp;<img src='".XOOPS_URL."/images/image.gif' alt='image' onmouseover='style.cursor=\"hand\"' onclick='openWithSelfMain(\"".XOOPS_URL."/imagemanager.php?target=".$textarea_id."\",\"imgmanager\",400,430);' />&nbsp;<img src='".XOOPS_URL."/images/code.gif' alt='code' onmouseover='style.cursor=\"hand\"' onclick='xoopsCodeCode(\"$textarea_id\", \"".htmlspecialchars(_ENTERCODE, ENT_QUOTES)."\");' />&nbsp;<img src='".XOOPS_URL."/images/quote.gif' alt='quote' onmouseover='style.cursor=\"hand\"' onclick='xoopsCodeQuote(\"$textarea_id\");'/><br />\n";

	$sizearray = array("xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large");
	echo "<select id='".$textarea_id."Size' onchange='setVisible(\"xoopsHiddenText\");setElementSize(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='SIZE'>"._SIZE."</option>\n";
	foreach ( $sizearray as $size ) {
		echo "<option value='$size'>$size</option>\n";
	}
	echo "</select>\n";

	$fontarray = array("Arial", "Courier", "Georgia", "Helvetica", "Impact", "Verdana");
	echo "<select id='".$textarea_id."Font' onchange='setVisible(\"xoopsHiddenText\");setElementFont(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='FONT'>"._FONT."</option>\n";
	foreach ( $fontarray as $font ) {
		echo "<option value='$font'>$font</option>\n";
	}
	echo "</select>\n";

	$colorarray = array("00", "33", "66", "99", "CC", "FF");
	echo "<select id='".$textarea_id."Color' onchange='setVisible(\"xoopsHiddenText\");setElementColor(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='COLOR'>"._COLOR."</option>\n";
	foreach ( $colorarray as $color1 ) {
		foreach ( $colorarray as $color2 ) {
			foreach ( $colorarray as $color3 ) {
				echo "<option value='".$color1.$color2.$color3."' style='background-color:#".$color1.$color2.$color3.";color:#".$color1.$color2.$color3.";'>#".$color1.$color2.$color3."</option>\n";
			}
		}
	}
	echo "</select><span id='".$hiddentext."'>"._EXAMPLE."</span>\n";

	echo "<br />\n";

	// for PukiWiki helper.
	$s_url = XOOPS_URL."/include";
	$i_url = XOOPS_URL."/images/modPukiWiki";
	$f_url = XOOPS_URL."/uploads/modPukiWiki";
	$wiki_helper = <<<EOD

<map name="map_button">
<area shape="rect" coords="0,0,22,16" alt="URL" href="#" onClick="java script:modPukiWiki_linkPrompt('url'); return false;">
<area shape="rect" coords="24,0,40,16" alt="B" href="#" onClick="java script:modPukiWiki_tag('b'); return false;">
<area shape="rect" coords="43,0,59,16" alt="I" href="#" onClick="java script:modPukiWiki_tag('i'); return false;">
<area shape="rect" coords="62,0,79,16" alt="U" href="#" onClick="java script:modPukiWiki_tag('u'); return false;">
<area shape="rect" coords="81,0,103,16" alt="SIZE" href="#" onClick="java script:modPukiWiki_tag('size'); return false;">
</map>
<map name="map_color">
<area shape="rect" coords="0,0,8,8" alt="Black" href="#" onClick="java script:modPukiWiki_tag('Black'); return false;">
<area shape="rect" coords="8,0,16,8" alt="Maroon" href="#" onClick="java script:modPukiWiki_tag('Maroon'); return false;">
<area shape="rect" coords="16,0,24,8" alt="Green" href="#" onClick="java script:modPukiWiki_tag('Green'); return false;">
<area shape="rect" coords="24,0,32,8" alt="Olive" href="#" onClick="java script:modPukiWiki_tag('Olive'); return false;">
<area shape="rect" coords="32,0,40,8" alt="Navy" href="#" onClick="java script:modPukiWiki_tag('Navy'); return false;">
<area shape="rect" coords="40,0,48,8" alt="Purple" href="#" onClick="java script:modPukiWiki_tag('Purple'); return false;">
<area shape="rect" coords="48,0,55,8" alt="Teal" href="#" onClick="java script:modPukiWiki_tag('Teal'); return false;">
<area shape="rect" coords="56,0,64,8" alt="Gray" href="#" onClick="java script:modPukiWiki_tag('Gray'); return false;">
<area shape="rect" coords="0,8,8,16" alt="Silver" href="#" onClick="java script:modPukiWiki_tag('Silver'); return false;">
<area shape="rect" coords="8,8,16,16" alt="Red" href="#" onClick="java script:modPukiWiki_tag('Red'); return false;">
<area shape="rect" coords="16,8,24,16" alt="Lime" href="#" onClick="java script:modPukiWiki_tag('Lime'); return false;">
<area shape="rect" coords="24,8,32,16" alt="Yellow" href="#" onClick="java script:modPukiWiki_tag('Yellow'); return false;">
<area shape="rect" coords="32,8,40,16" alt="Blue" href="#" onClick="java script:modPukiWiki_tag('Blue'); return false;">
<area shape="rect" coords="40,8,48,16" alt="Fuchsia" href="#" onClick="java script:modPukiWiki_tag('Fuchsia'); return false;">
<area shape="rect" coords="48,8,56,16" alt="Aqua" href="#" onClick="java script:modPukiWiki_tag('Aqua'); return false;">
<area shape="rect" coords="56,8,64,16" alt="White" href="#" onClick="java script:modPukiWiki_tag('White'); return false;">
</map>

<script type="text/javascript" src="{$s_url}/modPukiWiki.js"></script>
<script type="text/javascript">
<!--
	if (modPukiWiki_WinIE)
	{
		document.write('※ PukiWiki書式ヘルパー⇒');
		modPukiWiki_show_fontset_img("$i_url","$f_url");
	}
	else
	{
		document.write('※ PukiWiki書式も使えます。');
	}
-->
</script>
EOD;
	
	//Hack smilies move for bold, italic ...
    $areacontent = isset( $GLOBALS[$textarea_id] ) ? $GLOBALS[$textarea_id] : '';
    echo "<img src='".XOOPS_URL."/images/bold.gif' alt='bold' onmouseover='style.cursor=\"hand\"' onclick='setVisible(\"".$hiddentext."\");makeBold(\"".$hiddentext."\");' />&nbsp;<img src='".XOOPS_URL."/images/italic.gif' alt='italic' onmouseover='style.cursor=\"hand\"' onclick='setVisible(\"".$hiddentext."\");makeItalic(\"".$hiddentext."\");' />&nbsp;<img src='".XOOPS_URL."/images/underline.gif' alt='underline' onmouseover='style.cursor=\"hand\"' onclick='setVisible(\"".$hiddentext."\");makeUnderline(\"".$hiddentext."\");'/>&nbsp;<img src='".XOOPS_URL."/images/linethrough.gif' alt='linethrough' onmouseover='style.cursor=\"hand\"' onclick='setVisible(\"".$hiddentext."\");makeLineThrough(\"".$hiddentext."\");' /></a>&nbsp;<input type='text' id='".$textarea_id."Addtext' size='20' />&nbsp;<input type='button' onclick='xoopsCodeText(\"$textarea_id\", \"".$hiddentext."\", \"".htmlspecialchars(_ENTERTEXTBOX, ENT_QUOTES)."\")' value='"._ADD."' /><br />{$wiki_helper}<br /><textarea id='".$textarea_id."' name='".$textarea_id."' cols='$cols' rows='$rows' onkeyup=\"modPukiWiki_pos();\" onmouseup=\"modPukiWiki_pos();>".$areacontent."</textarea><br />\n";
	//Fin du hack
}

/*
*  Displays smilie image buttons used to insert smilie codes to a target textarea in a form
* $textarea_id is a unique of the target textarea
*/
function xoopsSmilies($textarea_id)
{
	$myts =& MyTextSanitizer::getInstance();
	$smiles =& $myts->getSmileys();
	if (empty($smileys)) {
		$db =& Database::getInstance();
		if ($result = $db->query('SELECT * FROM '.$db->prefix('smiles').' WHERE display=1')) {
			while ($smiles = $db->fetchArray($result)) {
			//hack smilies move for the smilies !!
				echo "<img src='".XOOPS_UPLOAD_URL."/".htmlspecialchars($smiles['smile_url'])."' border='0' onmouseover='style.cursor=\"hand\"' alt='' onclick='xoopsCodeSmilie(\"".$textarea_id."\", \" ".$smiles['code']." \");' />";
			//fin du hack
			}
		}
	} else {
		$count = count($smiles);
		for ($i = 0; $i < $count; $i++) {
			if ($smiles[$i]['display'] == 1) {
			//hack bis
				echo "<img src='".XOOPS_UPLOAD_URL."/".$myts->oopsHtmlSpecialChars($smiles['smile_url'])."' border='0' alt='' onclick='xoopsCodeSmilie(\"".$textarea_id."\", \" ".$smiles[$i]['code']." \");' onmouseover='style.cursor=\"hand\"' />";
			//fin du hack
			}
		}
	}
	//hack for more
	echo "&nbsp;[<a href='#moresmiley' onmouseover='style.cursor=\"hand\"' alt='' onclick='openWithSelfMain(\"".XOOPS_URL."/misc.php?action=showpopups&amp;type=smilies&amp;target=".$textarea_id."\",\"smilies\",300,475);'>"._MORE."</a>]";
}  //fin du hack
?>