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
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 *
 *
 * @package     kernel
 * @subpackage  form
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
/**
 * base class
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formtextarea.php";

// Make sure you have included /include/xoopscodes.php, otherwise DHTML will not work properly!

/**
 * A textarea with xoopsish formatting and smilie buttons
 *
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 *
 * @package     kernel
 * @subpackage  form
 */
class XoopsFormDhtmlTextArea extends XoopsFormTextArea
{
	/**
     * Hidden text
	 * @var	string
	 * @access	private
	 */
	var $_hiddenText;

	/**
	 * Constructor
	 *
     * @param	string  $caption    Caption
     * @param	string  $name       "name" attribute
     * @param	string  $value      Initial text
     * @param	int     $rows       Number of rows
     * @param	int     $cols       Number of columns
     * @param	string  $hiddentext Hidden Text
	 */
	function XoopsFormDhtmlTextArea($caption, $name, $value, $rows=5, $cols=50, $hiddentext="xoopsHiddenText")
	{
		$this->XoopsFormTextArea($caption, $name, $value, $rows, $cols);
		$this->_hiddenText = $hiddentext;
	}

	/**
	 * Prepare HTML for output
	 *
     * @return	string  HTML
	 */
	function render()
	{
		$ret = "<a name='moresmiley'></a><img onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/url.gif' alt='url' onclick='xoopsCodeUrl(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES)."\");' />&nbsp;<img onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/email.gif' alt='email' onclick='javascript:xoopsCodeEmail(\"".$this->getName()."\", \"".htmlspecialchars(_ENTEREMAIL, ENT_QUOTES)."\");' />&nbsp;<img onclick='javascript:xoopsCodeImg(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERIMGURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES)."\", \"".htmlspecialchars(_IMGPOSRORL, ENT_QUOTES)."\", \"".htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES)."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/imgsrc.gif' alt='imgsrc' />&nbsp;<img onmouseover='style.cursor=\"hand\"' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/imagemanager.php?target=".$this->getName()."\",\"imgmanager\",400,430);' src='".XOOPS_URL."/images/image.gif' alt='image' />&nbsp;<img src='".XOOPS_URL."/images/code.gif' onmouseover='style.cursor=\"hand\"' alt='code' onclick='javascript:xoopsCodeCode(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERCODE, ENT_QUOTES)."\");' />&nbsp;<img onclick='javascript:xoopsCodeQuote(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERQUOTE, ENT_QUOTES)."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/quote.gif' alt='quote' /><br />\n";

		$sizearray = array("xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large");
		$ret .= "<select id='".$this->getName()."Size' onchange='setVisible(\"xoopsHiddenText\");setElementSize(\"".$this->_hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$ret .= "<option value='SIZE'>"._SIZE."</option>\n";
		foreach ( $sizearray as $size ) {
			$ret .=  "<option value='$size'>$size</option>\n";
		}
		$ret .= "</select>\n";
		$fontarray = array("Arial", "Courier", "Georgia", "Helvetica", "Impact", "Verdana");
		$ret .= "<select id='".$this->getName()."Font' onchange='setVisible(\"".$this->_hiddenText."\");setElementFont(\"".$this->_hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$ret .= "<option value='FONT'>"._FONT."</option>\n";
		foreach ( $fontarray as $font ) {
			$ret .= "<option value='$font'>$font</option>\n";
		}
		$ret .= "</select>\n";
		$colorarray = array("00", "33", "66", "99", "CC", "FF");
		$ret .= "<select id='".$this->getName()."Color' onchange='setVisible(\"".$this->_hiddenText."\");setElementColor(\"".$this->_hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$ret .= "<option value='COLOR'>"._COLOR."</option>\n";
		foreach ( $colorarray as $color1 ) {
			foreach ( $colorarray as $color2 ) {
				foreach ( $colorarray as $color3 ) {
					$ret .= "<option value='".$color1.$color2.$color3."' style='background-color:#".$color1.$color2.$color3.";color:#".$color1.$color2.$color3.";'>#".$color1.$color2.$color3."</option>\n";
				}
			}
		}
		$ret .= "</select><span id='".$this->_hiddenText."'>"._EXAMPLE."</span>\n";
		$ret .= "<br />\n";

		// for PukiWiki helper.
		$s_url = XOOPS_URL."/include";
		$i_url = XOOPS_URL."/images/modPukiWiki";
		$f_url = XOOPS_URL."/uploads/modPukiWiki";
		$wiki_helper = <<<EOD

<map name="map_button">
<area shape="rect" coords="0,0,22,16" alt="URL" href="#" onClick="javascript:modPukiWiki_linkPrompt('url'); return false;">
<area shape="rect" coords="24,0,40,16" alt="B" href="#" onClick="javascript:modPukiWiki_tag('b'); return false;">
<area shape="rect" coords="43,0,59,16" alt="I" href="#" onClick="javascript:modPukiWiki_tag('i'); return false;">
<area shape="rect" coords="62,0,79,16" alt="U" href="#" onClick="javascript:modPukiWiki_tag('u'); return false;">
<area shape="rect" coords="81,0,103,16" alt="SIZE" href="#" onClick="javascript:modPukiWiki_tag('size'); return false;">
</map>
<map name="map_color">
<area shape="rect" coords="0,0,8,8" alt="Black" href="#" onClick="javascript:modPukiWiki_tag('Black'); return false;">
<area shape="rect" coords="8,0,16,8" alt="Maroon" href="#" onClick="javascript:modPukiWiki_tag('Maroon'); return false;">
<area shape="rect" coords="16,0,24,8" alt="Green" href="#" onClick="javascript:modPukiWiki_tag('Green'); return false;">
<area shape="rect" coords="24,0,32,8" alt="Olive" href="#" onClick="javascript:modPukiWiki_tag('Olive'); return false;">
<area shape="rect" coords="32,0,40,8" alt="Navy" href="#" onClick="javascript:modPukiWiki_tag('Navy'); return false;">
<area shape="rect" coords="40,0,48,8" alt="Purple" href="#" onClick="javascript:modPukiWiki_tag('Purple'); return false;">
<area shape="rect" coords="48,0,55,8" alt="Teal" href="#" onClick="javascript:modPukiWiki_tag('Teal'); return false;">
<area shape="rect" coords="56,0,64,8" alt="Gray" href="#" onClick="javascript:modPukiWiki_tag('Gray'); return false;">
<area shape="rect" coords="0,8,8,16" alt="Silver" href="#" onClick="javascript:modPukiWiki_tag('Silver'); return false;">
<area shape="rect" coords="8,8,16,16" alt="Red" href="#" onClick="javascript:modPukiWiki_tag('Red'); return false;">
<area shape="rect" coords="16,8,24,16" alt="Lime" href="#" onClick="javascript:modPukiWiki_tag('Lime'); return false;">
<area shape="rect" coords="24,8,32,16" alt="Yellow" href="#" onClick="javascript:modPukiWiki_tag('Yellow'); return false;">
<area shape="rect" coords="32,8,40,16" alt="Blue" href="#" onClick="javascript:modPukiWiki_tag('Blue'); return false;">
<area shape="rect" coords="40,8,48,16" alt="Fuchsia" href="#" onClick="javascript:modPukiWiki_tag('Fuchsia'); return false;">
<area shape="rect" coords="48,8,56,16" alt="Aqua" href="#" onClick="javascript:modPukiWiki_tag('Aqua'); return false;">
<area shape="rect" coords="56,8,64,16" alt="White" href="#" onClick="javascript:modPukiWiki_tag('White'); return false;">
</map>

<script type="text/javascript" src="{$s_url}/modPukiWiki.js"></script>
<script type="text/javascript">
<!--
	if (modPukiWiki_WinIE)
	{
		document.write('�� PukiWiki�񼰥إ�ѡ���');
		modPukiWiki_show_fontset_img("$i_url","$f_url");
	}
	else
	{
		document.write('�� PukiWiki�񼰤�Ȥ��ޤ���');
	}
-->
</script>
EOD;
	

		$ret .= "<img onclick='javascript:setVisible(\"".$this->_hiddenText."\");makeBold(\"".$this->_hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/bold.gif' alt='bold' />&nbsp;<img onclick='javascript:setVisible(\"".$this->_hiddenText."\");makeItalic(\"".$this->_hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/italic.gif' alt='italic' />&nbsp;<img onclick='javascript:setVisible(\"".$this->_hiddenText."\");makeUnderline(\"".$this->_hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/underline.gif' alt='underline' />&nbsp;<img onclick='javascript:setVisible(\"".$this->_hiddenText."\");makeLineThrough(\"".$this->_hiddenText."\");' src='".XOOPS_URL."/images/linethrough.gif' alt='linethrough' onmouseover='style.cursor=\"hand\"' />&nbsp;&nbsp;<input type='text' id='".$this->getName()."Addtext' size='20' />&nbsp;<input type='button' onclick='xoopsCodeText(\"".$this->getName()."\", \"".$this->_hiddenText."\", \"".htmlspecialchars(_ENTERTEXTBOX, ENT_QUOTES)."\")' class='formButton' value='"._ADD."' /><br />{$wiki_helper}<br /><textarea id='".$this->getName()."' name='".$this->getName()."' onselect=\"xoopsSavePosition('".$this->getName()."');\" onclick=\"xoopsSavePosition('".$this->getName()."');\" onkeyup=\"xoopsSavePosition('".$this->getName()."');modPukiWiki_pos();\" onmouseup=\"modPukiWiki_pos();\" cols='".$this->getCols()."' rows='".$this->getRows()."'".$this->getExtra().">".$this->getValue()."</textarea><br />\n";
		$ret .= $this->_renderSmileys();
		return $ret;
	}

	/**
	 * prepare HTML for output of the smiley list.
     *
	 * @return	string HTML
	 */
	function _renderSmileys()
	{
		$myts =& MyTextSanitizer::getInstance();
		$smiles =& $myts->getSmileys();
		$ret = '';
		if (empty($smileys)) {
			$db =& Database::getInstance();
			if ($result = $db->query('SELECT * FROM '.$db->prefix('smiles').' WHERE display=1')) {
				while ($smiles = $db->fetchArray($result)) {
					$ret .= "<img onclick='xoopsCodeSmilie(\"".$this->getName()."\", \" ".$smiles['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_UPLOAD_URL."/".htmlspecialchars($smiles['smile_url'], ENT_QUOTES)."' alt='' />";
				}
			}
		} else {
			$count = count($smiles);
			for ($i = 0; $i < $count; $i++) {
				if ($smiles[$i]['display'] == 1) {
					$ret .= "<img onclick='xoopsCodeSmilie(\"".$this->getName()."\", \" ".$smiles[$i]['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_UPLOAD_URL."/".$myts->oopsHtmlSpecialChars($smiles['smile_url'])."' border='0' alt='' />";
				}
			}
		}
		$ret .= "&nbsp;[<a href='#moresmiley' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/misc.php?action=showpopups&amp;type=smilies&amp;target=".$this->getName()."\",\"smilies\",300,475);'>"._MORE."</a>]";
		return $ret;
	}
}
?>
