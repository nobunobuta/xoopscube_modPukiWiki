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
// Author: Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)        //
//         Goghs Cheng (http://www.eqiao.com, http://www.devbeez.com/)       //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //

/**
 * Class to "clean up" text for various uses
 *
 * <b>Singleton</b>
 *
 * @package		kernel
 * @subpackage	core
 *
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @author      Goghs Cheng
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
 
class MyTextSanitizer
{
	/**
	 * @var	array
	 */
	var $smileys = array();

	/**
	 *
	 */
	var $censorConf;

	/*
	* Constructor of this class
    *
	* Gets allowed html tags from admin config settings
	* <br> should not be allowed since nl2br will be used
	* when storing data.
    *
    * @access	private
    *
    * @todo Sofar, this does nuttin' ;-)
	*/
	function MyTextSanitizer()
	{

	}

	/**
	 * Access the only instance of this class
     *
     * @return	object
     *
     * @static
     * @staticvar   object
	 */
	function &getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new MyTextSanitizer();
		}
		return $instance;
	}

	/**
	 * Get the smileys
     *
     * @return	array
	 */
	function getSmileys()
	{
		return $this->smileys;
	}

    /**
     * Replace emoticons in the message with smiley images
     *
     * @param	string  $message
     *
     * @return	string
     */
    function &smiley($message)
	{
		$db =& Database::getInstance();
		if (empty($this->smileys)) {
			if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles"))){
				while ($smiles = $db->fetchArray($getsmiles)) {
					$message =& str_replace($smiles['code'], '<img src="'.XOOPS_UPLOAD_URL.'/'.htmlspecialchars($smiles['smile_url']).'" alt="" />', $message);
					array_push($this->smileys, $smiles);
				}
			}
		} else {
			foreach ($this->smileys as $smile) {
				$message =& str_replace($smile['code'], '<img src="'.XOOPS_UPLOAD_URL.'/'.htmlspecialchars($smile['smile_url']).'" alt="" />', $message);
			}
		}
		return $message;
	}

	/**
	 * Make links in the text clickable
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function &makeClickable(&$text)
	{
		$patterns = array(
			"/(^|[^\]_a-z0-9-=\"'\/])(\[\[((?:(?!\]\]).)+)(&gt;|>|:))((?:https?|ftp|news):\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)(\]\])/i",
			"/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i",
			"/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i",
			"/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i",
			"/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([^, \r\n\"\(\)'<>\[\]]+)/i"
		);
		$replacements = array(
			"\\1<a href='\\5' target='_blank'>\\3</a>",
			"\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>",
			"\\1<a href=\"http://www.\\2.\\3\" target=\"_blank\">www.\\2.\\3</a>",
			"\\1<a href=\"ftp://ftp.\\2.\\3\" target=\"_blank\">ftp.\\2.\\3</a>",
			"\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>"
		);
		$texts = preg_split("/\[code].*\[\/code\]/sU",$text);
		preg_match_all("/\[code].*\[\/code\]/sU",$text,$match,PREG_PATTERN_ORDER);
		$match[0][] = "";
		$ret = "";
		$i=0;
		foreach($texts as $block)
		{
			$ret .= preg_replace($patterns, $replacements, $block).$match[0][$i];
			$i++;
		}
		return $ret;
		//return preg_replace($patterns, $replacements, $text);
	}
	
	function &renderWikistyle(&$text,$br=1,$use_cache=1)
	{
		//modPukiWiki
		include_once(XOOPS_ROOT_PATH.'/class/modPukiWiki/PukiWiki.php');
		static $render;
		if (!is_object($render))
			$render = &new PukiWikiRender;
		
		$br = ($br)? 1 : 0;
		$use_cache = ($use_cache)? 1 : 0;
		PukiWikiConfig::setParam('line_break',$br);
		PukiWikiConfig::setParam('use_cache',$use_cache);
		PukiWikiConfig::setParam('autolink',1);
		PukiWikiConfig::setParam('nowikiname',0);
		PukiWikiConfig::setParam('makepage_link',0);
		
		// BB Code email
		$text = preg_replace("/\[email](.+?)\[\/email]/i","$1",$text);
		// BB Code url
		$text = preg_replace("/\[url=(['\"]?)((?:ht|f)tp[s]?:\/\/[^\"'<>]+)\\1\](.+)\[\/url\]/sU","[[$3:$2]]",$text);
		
		$texts = preg_split("/\[code].*\[\/code\]/sU",$text);
		preg_match_all("/\[code].*\[\/code\]/sU",$text,$match,PREG_PATTERN_ORDER);
		$ret = "";
		$i=0;
		$count = count($match[0]);
		foreach($texts as $block)
		{
			if ($i < $count)
				$ret .= $block."\n\n_____cODe_".$i."_____\n\n";
			else
				$ret .= $block;
			$i++;
		}
		$ret = $render->transform($ret);
		while($i >= 0)
		{
			$ret = str_replace("_____cODe_".$i."_____",$match[0][$i],$ret);
			$i--;
		}
		
		// XOOPS Quote style
		$ret = str_replace(array('<blockquote>','</blockquote>'),array(_QUOTEC.'<div class="xoopsQuote"><blockquote>','</blockquote></div>'),$ret);
		
		return $ret;
	}
	
	/**
	 * Replace XoopsCodes with their equivalent HTML formatting
	 *
	 * @param   string  $text
	 * @param   bool    $allowimage Allow images in the text?
     *                              On FALSE, uses links to images.
	 * @return  string
	 **/
	function &xoopsCodeDecode(&$text, $allowimage = 1)
	{
		$patterns = array();
		$replacements = array();
		$patterns[] = "/\[code](.*)\[\/code\]/esU";
		//$replacements[] = "'<div class=\"xoopsCode\"><code><pre>'.wordwrap(MyTextSanitizer::htmlSpecialChars('\\1'), 100).'</pre></code></div>'";
		$replacements[] = "'<div class=\"xoopsCode\"><code><pre>'.trim(MyTextSanitizer::htmlSpecialChars('\\1','code'),'\r\n').'</pre></code></div>'";
		// RMV: added new markup for intrasite url (allows easier site moves)
		// TODO: automatically convert other URLs to this format if XOOPS_URL matches??
		$patterns[] = "/\[siteurl=(['\"]?)([^\"'<>]*)\\1](.*)\[\/siteurl\]/sU";
		$replacements[] = '<a href="'.XOOPS_URL.'/\\2" target="_blank">\\3</a>';
		$patterns[] = "/\[url=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
		$replacements[] = '<a href="\\2" target="_blank">\\3</a>';
		$patterns[] = "/\[url=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
		$replacements[] = '<a href="\\2" target="_blank">\\3</a>';
		$patterns[] = "/\[url=(['\"]?)([^\"'<>]*)\\1](.*)\[\/url\]/sU";
		$replacements[] = '<a href="http://\\2" target="_blank">\\3</a>';
		$patterns[] = "/\[c(?:olor)?=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/c(?:olor)?\]/sU";
		$replacements[] = '<span style="color: #\\2;">\\3</span>';
		$patterns[] = "/\[s(?:ize)?=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/s(?:ize)?\]/sU";
		$replacements[] = '<span style="font-size: \\2;">\\3</span>';
		$patterns[] = "/\[f(?:ont)?=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/f(?:ont)?\]/sU";
		$replacements[] = '<span style="font-family: \\2;">\\3</span>';
		$patterns[] = "/\[e(?mail)?]([^;<>\*\(\)\"']*)\[\/e(?:mail)?\]/sU";
		$replacements[] = '<a href="mailto:\\1">\\1</a>';
		$patterns[] = "/\[b](.*)\[\/b\]/sU";
		$replacements[] = '<b>\\1</b>';
		$patterns[] = "/\[i](.*)\[\/i\]/sU";
		$replacements[] = '<i>\\1</i>';
		$patterns[] = "/\[u](.*)\[\/u\]/sU";
		$replacements[] = '<u>\\1</u>';
		$patterns[] = "/\[d](.*)\[\/d\]/sU";
		$replacements[] = '<del>\\1</del>';
		//$patterns[] = "/\[li](.*)\[\/li\]/sU";
		//$replacements[] = '<li>\\1</li>';
		$patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$patterns[] = "/\[img]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1 id=(['\"]?)([0-9]*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$patterns[] = "/\[img id=(['\"]?)([0-9]*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		if ($allowimage != 1) {
			$replacements[] = '<a href="\\3" target="_blank">\\3</a>';
			$replacements[] = '<a href="\\1" target="_blank">\\1</a>';
			$replacements[] = '<a href="'.XOOPS_URL.'/image.php?id=\\4" target="_blank">\\4</a>';
			$replacements[] = '<a href="'.XOOPS_URL.'/image.php?id=\\2" target="_blank">\\3</a>';
		} else {
			//$replacements[] = '<img src="\\3" align="\\2" alt="" />';
			$replacements[] = '<img src="\\3" style="float:\\2;" alt="" />';
			$replacements[] = '<img src="\\1" alt="" />';
			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\4" align="\\2" alt="\\4" />';
			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\2" alt="\\3" />';
		}
		$patterns[] = "/\[clear]/i";
		$replacements[] = '<div style="clear:both;"></div>';
		$patterns[] = "/\[quote]/sU";
		$replacements[] = _QUOTEC.'<div class="xoopsQuote"><blockquote>';
		//$replacements[] = 'Quote: <div class="xoopsQuote"><blockquote>';
		$patterns[] = "/\[\/quote]/sU";
		$replacements[] = '</blockquote></div>';
		$patterns[] = "/javascript:/si";
		$replacements[] = "java script:";
		$patterns[] = "/about:/si";
		$replacements[] = "about :";
		return preg_replace($patterns, $replacements, $text);
	}

	/**
	 * Convert linebreaks to <br /> tags
     *
     * @param	string  $text
     *
     * @return	string
	 */
	function &nl2Br($text,$br=1)
	{
		static $count = 0;
		$match = array();
		$texts = preg_split("/<div class=\"xoopsCode\"><code><pre>.*<\/pre><\/code><\/div>/sU",$text);
		if (preg_match_all("/<div class=\"xoopsCode\"><code><pre>.*<\/pre><\/code><\/div>/sU",$text,$match,PREG_PATTERN_ORDER))
			$count ++;
		
		$match[0][] = "";
		$ret = "";
		$i=0;
		foreach($texts as $block)
		{
			$match[0][$i] = preg_replace("/(\015\012)|(\015)|(\012)/","\n",$match[0][$i]);
			$c_line = count(explode("\n",$match[0][$i])) * 14 + 32; //Set 'line-height:14px;' on css.
			if (!empty($match[0][$i]))
			{
				if ($c_line < 420)
					$match[0][$i] = str_replace("<div class=\"xoopsCode\">","<div class=\"xoopsCode\" style=\"height:".$c_line."px;\">",$match[0][$i]);
				else
					$match[0][$i] = str_replace("<div class=\"xoopsCode\">","<div class=\"xoopsCode\" style=\"height:420px;\">",$match[0][$i]);
				
				$j_script = "<script type=\"text/javascript\"><!--\nh_xoops_make_copy_button('code_area".$count."_".$i."');\n--></script>";
				$bef = array("<pre>","</div>");
				$aft = array("<pre id=\"code_area".$count."_".$i."\">", "</div>".$j_script);
				$match[0][$i] = str_replace($bef,$aft,$match[0][$i]);
			}
			else
				$match[0][$i] = "";
			if ($br)
				$ret .= preg_replace("/(\015\012)|(\015)|(\012)/","<br />\n",$block).$match[0][$i];
			else
				$ret .= $block.$match[0][$i];
			$i++;
		}
		return $ret;
	}

	/**
	 * Add slashes to the text if magic_quotes_gpc is turned off.
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function &addSlashes($text)
	{
		if (!get_magic_quotes_gpc()) {
			$text =& addslashes($text);
		}
		return $text;
	}
	/*
	* if magic_quotes_gpc is on, stirip back slashes
    *
    * @param	string  $text
    *
    * @return	string
	*/
	function &stripSlashesGPC($text)
	{
		if (get_magic_quotes_gpc()) {
			$text =& stripslashes($text);
		}
		return $text;
	}

	/*
	*  for displaying data in html textbox forms
    *
    * @param	string  $text
    *
    * @return	string
	*/
	function &htmlSpecialChars($text,$strip="")
	{
		//if ($strip) $text = stripslashes($text);
		if ($strip == "code")
		{
			$text = str_replace(array("\'",'\"'),array("'",'"'),$text);
			$text = $this->undoHtmlSpecialChars($text);
			return htmlspecialchars($text);
		}
		//return preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES));
		return preg_replace(array("/&amp;/i", "/&nbsp;/i"), array('&', '&amp;nbsp;'), htmlspecialchars($text, ENT_QUOTES));
	}

	/**
	 * Reverses {@link htmlSpecialChars()}
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function &undoHtmlSpecialChars(&$text)
	{
		return preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i"), array(">", "<", "\"", "'"), $text);
	}

	/**
	 * Filters textarea form data in DB for display
	 *
	 * @param   string  $text
	 * @param   bool    $html   allow html?
	 * @param   bool    $smiley allow smileys?
	 * @param   bool    $xcode  allow xoopscode?
	 * @param   bool    $image  allow inline images?
	 * @param   bool    $br     convert linebreaks?
	 * @return  string
	 **/
	function &displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
	{
		//if ($html != 1) {
		//	// html not allowed
		//	$text =& $this->htmlSpecialChars($text);
		//}
		//$text =& $this->makeClickable($text);
		if ($html != 1)
		{
			$text =& $this->renderWikistyle($text,$br,1);
			$br = 0;
		}
		else
			$text =& $this->makeClickable($text);
		
		if ($smiley != 0) {
			// process smiley
			$text =& $this->smiley($text);
		}
		if ($xcode != 0) {
			// decode xcode
			if ($image != 0) {
				// image allowed
				$text =& $this->xoopsCodeDecode($text);
			} else {
				// image not allowed
				$text =& $this->xoopsCodeDecode($text, 0);
			}
		}
		//if ($br != 0) {
			$text =& $this->nl2Br($text,$br);
		//}
		return $text;
	}

	/**
	 * Filters textarea form data submitted for preview
	 *
	 * @param   string  $text
	 * @param   bool    $html   allow html?
	 * @param   bool    $smiley allow smileys?
	 * @param   bool    $xcode  allow xoopscode?
	 * @param   bool    $image  allow inline images?
	 * @param   bool    $br     convert linebreaks?
	 * @return  string
	 **/
	function &previewTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
	{
		$text =& $this->stripSlashesGPC($text);
		//if ($html != 1) {
		//	// html not allowed
			//	$text =& $this->htmlSpecialChars($text);
		//}
		//$text =& $this->makeClickable($text);
		if ($html != 1)
		{
			$text =& $this->renderWikistyle($text,$br,0);
			$br = 0;
		}
		else
			$text =& $this->makeClickable($text);
		
		if ($smiley != 0) {
			// process smiley
			$text =& $this->smiley($text);
		}
		if ($xcode != 0) {
			// decode xcode
			if ($image != 0) {
				// image allowed
				$text =& $this->xoopsCodeDecode($text);
			} else {
				// image not allowed
				$text =& $this->xoopsCodeDecode($text, 0);
			}
		}
		//if ($br != 0) {
			$text =& $this->nl2Br($text,$br);
		//}
		return $text;
	}

	/**
	 * Replaces banned words in a string with their replacements
	 *
	 * @param   string $text
	 * @return  string
     *
     * @deprecated
	 **/
	function &censorString(&$text)
	{
		if (!isset($this->censorConf)) {
			$config_handler =& xoops_gethandler('config');
			$this->censorConf =& $config_handler->getConfigsByCat(XOOPS_CONF_CENSOR);
		}
		if ($this->censorConf['censor_enable'] == 1) {
			$replacement = $this->censorConf['censor_replace'];
			foreach ($this->censorConf['censor_words'] as $bad) {
				$bad = quotemeta($bad);
				$patterns[] = "/(\s)".$bad."/siU";
				$replacements[] = "\\1".$replacement;
				$patterns[] = "/^".$bad."/siU";
				$replacements[] = $replacement;
				$patterns[] = "/(\n)".$bad."/siU";
				$replacements[] = "\\1".$replacement;
				$patterns[] = "/]".$bad."/siU";
				$replacements[] = "]".$replacement;
				$text = preg_replace($patterns, $replacements, $text);
   			}
		}
   		return $text;
	}


##################### Deprecated Methods ######################

	/**#@+
	 * @deprecated
	 */
	function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
	{
		if ( $allowhtml == 0 ) {
			//$text = $this->htmlSpecialChars($text);
			$text = $this->renderWikistyle($text,1,1);
		} else {
			//$config =& $GLOBALS['xoopsConfig'];
			//$allowed = $config['allowed_html'];
			//$text = strip_tags($text, $allowed);
			$text = $this->makeClickable($text);
		}
		if ( $smiley == 1 ) {
			$text = $this->smiley($text);
		}
		if ( $bbcode == 1 ) {
			$text = $this->xoopsCodeDecode($text);
		}
		$text = $this->nl2Br($text);
		return $text;
	}

	function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
	{
		$text = $this->oopsStripSlashesGPC($text);
		if ( $allowhtml == 0 ) {
			//$text = $this->htmlSpecialChars($text);
			$text = $this->renderWikistyle($text,1,0);
		} else {
			//$config =& $GLOBALS['xoopsConfig'];
			//$allowed = $config['allowed_html'];
			//$text = strip_tags($text, $allowed);
			$text = $this->makeClickable($text);
		}
		if ( $smiley == 1 ) {
			$text = $this->smiley($text);
		}
		if ( $bbcode == 1 ) {
			$text = $this->xoopsCodeDecode($text);
		}
		$text = $this->nl2Br($text);
		return $text;
	}

	function makeTboxData4Save($text)
	{
		//$text = $this->undoHtmlSpecialChars($text);
		return $this->addSlashes($text);
	}

	function makeTboxData4Show($text, $smiley=0)
	{
		$text = $this->htmlSpecialChars($text);
		return $text;
	}

	function makeTboxData4Edit($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function makeTboxData4Preview($text, $smiley=0)
	{
		$text = $this->stripSlashesGPC($text);
		$text = $this->htmlSpecialChars($text);
		return $text;
	}

	function makeTboxData4PreviewInForm($text)
	{
		$text = $this->stripSlashesGPC($text);
		return $this->htmlSpecialChars($text);
	}

	function makeTareaData4Save($text)
	{
		return $this->addSlashes($text);
	}

	function &makeTareaData4Show(&$text, $html=1, $smiley=1, $xcode=1)
	{
		return $this->displayTarea($text, $html, $smiley, $xcode);
	}

	function makeTareaData4Edit($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function &makeTareaData4Preview(&$text, $html=1, $smiley=1, $xcode=1)
	{
		return $this->previewTarea($text, $html, $smiley, $xcode);
	}

	function makeTareaData4PreviewInForm($text)
	{
		//if magic_quotes_gpc is on, do stipslashes
		$text = $this->stripSlashesGPC($text);
		return $this->htmlSpecialChars($text);
	}

	function makeTareaData4InsideQuotes($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function &oopsStripSlashesGPC($text)
	{
		return $this->stripSlashesGPC($text);
	}

	function &oopsStripSlashesRT($text)
	{
		if (get_magic_quotes_runtime()) {
			$text =& stripslashes($text);
		}
		return $text;
	}

	function &oopsAddSlashes($text)
	{
		return $this->addSlashes($text);
	}

	function &oopsHtmlSpecialChars($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function &oopsNl2Br($text)
	{
		return $this->nl2br($text);
	}
    /**#@-*/
}
?>
