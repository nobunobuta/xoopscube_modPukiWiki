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
		$patterns = array("/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([^, \r\n\"\(\)'<>\[\]]+)/i");
		$replacements = array("\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", "\\1<a href=\"http://www.\\2.\\3\" target=\"_blank\">www.\\2.\\3</a>", "\\1<a href=\"ftp://ftp.\\2.\\3\" target=\"_blank\">ftp.\\2.\\3</a>", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>");
		return preg_replace($patterns, $replacements, $text);
	}


	/**
	 * Multilanguage Hack by marcan, hsalazar, chad and many other Xoopsers ;)
	 *
	 * Return only the portion of text related to the selected language
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function &formatForML(&$text)
	{
	// Completely Rewrited by nobunobu
		// LANGUAGE DEFINITION TAGS BEGINS HERE
  		
		$patterns = array();
		$replacements = array();

  		global $xoopsConfig;

		$lang_tags = array(
			'ja' => 'japanese',
			'en' => 'english',
			'fr' => 'french',
		);
		
		foreach ($lang_tags as $tag=>$language) {
	  		$patterns[] = "/\[".$tag."](.*)\[\/".$tag."\]/sU"; 
	  		if ($xoopsConfig['language'] == $language) {
		  		$replacements[] = '\\1';
	  		} else { 
	  			$replacements[] = "";
	  		}
		}

		return preg_replace($patterns, $replacements, $text);
	}

	/**
	 * Replace XoopsCodes with their equivalent HTML formatting
	 *
	 * @param   string  $text
	 * @param   bool    $allowimage Allow images in the text?
     *                              On FALSE, uses links to images.
	 * @return  string
	 **/
	// ML Hack by marcan
	// function &xoopsCodeDecode(&$text, $allowimage = 1)
	function &xoopsCodeDecode(&$text, $allowimage = 1, $formatML = 1)
	// End of ML Hack by marcan	
	{
		$patterns = array();
		$replacements = array();

		// ML Hack by marcan
		If ($formatML) {
			$text =& $this->formatForML($text);
		} 
		// End of ML hack by marcan
		
        //$patterns[] = "/\[code](.*)\[\/code\]/esU";
		//$replacements[] = "'<div class=\"xoopsCode\"><code><pre>'.wordwrap(MyTextSanitizer::htmlSpecialChars('\\1'), 100).'</pre></code></div>'";
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
		$patterns[] = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";
		$replacements[] = '<span style="color: #\\2;">\\3</span>';
		$patterns[] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";
		$replacements[] = '<span style="font-size: \\2;">\\3</span>';
		$patterns[] = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";
		$replacements[] = '<span style="font-family: \\2;">\\3</span>';
		$patterns[] = "/\[email]([^;<>\*\(\)\"']*)\[\/email\]/sU";
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
			$replacements[] = '<img src="\\3" align="\\2" alt="" />';
			$replacements[] = '<img src="\\1" alt="" />';
			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\4" align="\\2" alt="\\4" />';
			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\2" alt="\\3" />';
		}
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
	function &nl2Br($text)
	{
		return preg_replace("/(\015\012)|(\015)|(\012)/","<br />",$text);
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
	function &htmlSpecialChars($text)
	{
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
	// ML Hack by marcan
	// function &displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
	function &displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $formatML = 1)
	// End of ML Hack by marcan
	{
		$text =& $this->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)
		$text =& $this->wikiPreConv($text, $xcode); // modPukiWiki Conv by nobunobu

		if ($html != 1) {
			// html not allowed
			$text =& $this->htmlSpecialChars($text);
		}

		$text =& $this->makeClickable($text);
		if ($smiley != 0) {
			// process smiley
			$text =& $this->smiley($text);
		}
		if ($xcode != 0) {
			// decode xcode
			if ($image != 0) {
				// image allowed
				// ML Hack by marcan
				// $text =& $this->xoopsCodeDecode($text);
				$text =& $this->xoopsCodeDecode($text, 1, $formatML);
				// End of ML Hack by marcan
			} else {
				// image not allowed
				// ML Hack by marcan
				// $text =& $this->xoopsCodeDecode($text, 0);
				$text =& $this->xoopsCodeDecode($text, 0, $formatML);
				// End of ML Hack by marcan
			}
		}
		if ($br != 0) {
			$text =& $this->nl2Br($text);
		}
		$text =& $this->wikiConv($text, $xcode, $image, $br); // modPukiWiki Conv by nobunobu
		$text =& $this->codeConv($text, $xcode, $image);	// Ryuji_edit(2003-11-18)
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

		$text =& $this->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)
		$text =& $this->wikiPreConv($text, $xcode); // modPukiWiki Conv

		if ($html != 1) {
			// html not allowed
			$text =& $this->htmlSpecialChars($text);
		}
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
		if ($br != 0) {
			$text =& $this->nl2Br($text);
		}
		$text =& $this->wikiConv($text, $xcode, $image, $br); // modPukiWiki Conv by nobunobu
		$text =& $this->codeConv($text, $xcode, $image);	// Ryuji_edit(2003-11-18)
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
				if ( !empty($bad) ) {
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
		}
   		return $text;
	}

	/**#@+
	 * Processing modPukiWiki [wiki] tag
	 */
	 
	function wikiPreConv($text, $xcode = 1) {
		if($xcode != 0){
			$patterns = "/\[wiki](.*)\[\/wiki\]/esU";
			$replacements = "'[wiki]'.base64_encode('$1').'[/wiki]'";
			$text =  preg_replace($patterns, $replacements, $text);
		}
		return $text;
	}

    function wikiConv($text, $xcode = 1, $image = 1, $br = 1) {
		if($xcode != 0){
			$patterns = "/\[wiki](.*)\[\/wiki\]/esU";
			$replacements = "MyTextSanitizer::wikiElementConv('$1','$br')";
			$text =  preg_replace($patterns, $replacements, $text);
		}
		return $text;
    }

    function wikiElementConv($text, $br=1) {
		include_once(XOOPS_ROOT_PATH.'/class/modPukiWiki/PukiWiki.php');
		static $render;
		if (!is_object($render))
			$render = &new PukiWikiRender('xoops_2');
		if ($br) {
			PukiWikiConfig::setParam('line_break', 1);
		} else {
			PukiWikiConfig::setParam('line_break', 0);
		}
		$text = $render->transform(str_replace('\"', '"', base64_decode($text)));
		// XOOPS Quote style
		$text = str_replace(array('<blockquote>','</blockquote>'),array(_QUOTEC.'<div class="xoopsQuote"><blockquote>','</blockquote></div>'),$text);
		return $text;
    }
	/**#@+
	 * Sanitizing of [code] tag
	 */
	function codePreConv($text, $xcode = 1) {
		if($xcode != 0){
			$patterns = "/\[code](.*)\[\/code\]/esU";
			$replacements = "'[code]'.base64_encode('$1').'[/code]'";
			$text =  preg_replace($patterns, $replacements, $text);
		}
		return $text;
	}

	function codeConv($text, $xcode = 1, $image = 1){
		if($xcode != 0){
			$patterns = "/\[code](.*)\[\/code\]/esU";
			if ($image != 0) {
				// image allowed
				$replacements = "'<div class=\"xoopsCode\"><code><pre>'.MyTextSanitizer::codeSanitizer('$1').'</pre></code></div>'";
				//$text =& $this->xoopsCodeDecode($text);
			} else {
				// image not allowed
				$replacements = "'<div class=\"xoopsCode\"><code><pre>'.MyTextSanitizer::codeSanitizer('$1', 0).'</pre></code></div>'";
				//$text =& $this->xoopsCodeDecode($text, 0);
			}
			$text =  preg_replace($patterns, $replacements, $text);
		}
		return $text;
	}

	function codeSanitizer($str, $image = 1){
		if($image != 0){
			$str = $this->xoopsCodeDecode(
				$this->htmlSpecialChars(str_replace('\"', '"', base64_decode($str)))
				);
		}else{
			$str = $this->xoopsCodeDecode(
				$this->htmlSpecialChars(str_replace('\"', '"', base64_decode($str))),0
				);
		}
		return $str;
	}


	/**#@-*/


##################### Deprecated Methods ######################

	/**#@+
	 * @deprecated
	 */
	function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
	{
		if ( $allowhtml == 0 ) {
			$text = $this->htmlSpecialChars($text);
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
			$text = $this->htmlSpecialChars($text);
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
		// MT hack added by hsalazar //
		$text =& $this->xoopsCodeDecode($text, 0);
		// MT hack added by hsalazar //
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
		// MT hack added by hsalazar //
		$text =& $this->xoopsCodeDecode($text, 0);
		// MT hack added by hsalazar //
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
