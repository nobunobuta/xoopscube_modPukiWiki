<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWikiレンダリングエンジン メインクラス
//
/**
 * @package     modPukiWiki
 * 
 * @author	    Nobuki Kowa <Nobuki@Kowa.ORG>
 * @copyright	Copyright &copy; 2004 Nobuki Kowa<br/>
 *                 License is GNU/GPL.<br/>
 *                 Based on PukiWiki 1.4 by PukiWiki Developers Team.<br/>
 *                   Copyright &copy; 2001,2002,2003 PukiWiki Developers Team.<br/>
 *                   License is GNU/GPL.<br/>
 *                   Based on "PukiWiki" 1.3 by sng<br/>
 *                     Copyright &copy; 2001,2002 by sng, PukiWiki Developers Team<br/>
 *                 Partly based on PukiWikiMod 0.8.0 by nao-pon.<br/>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * modPukiWiki Rendering Engine  class.
 *
 *
 * @author  Nobuki Kowa <Nobuki@Kowa.ORG>
 * @todo    :
 * @public
 */

class PukiWikiRender {
	var $_body;
	var $_settings;
	var $_linerules;
	var $_pattern;
	var $_replace;
	var $_source;
	var $_md5hash;
	/**
	 * @desc		PukiWikiRenderクラスのコンストラクタ
	 *
	 * @param		$config		使用するconfigファイルのファイル名
	 * @return		
	 * 
	 * @author		
	 */
	
	function PukiWikiRender($config='') {
		//デフォルトの設定ファイル読込
		require (MOD_PUKI_DEFAULT);
		PukiWikiConfig::initParams();
		foreach($_settings as $key=>$value) {
			PukiWikiConfig::setParam($key,$value);
		}
		PukiWikiConfig::initRules();
		PukiWikiConfig::addRuleArray($_rules);
		
		PukiWikiConfig::initInterWiki();
		$this->_body = &new PukiWikiBody($this,1);
		//コンストラクタのパラメータで$configが指定されている場合は、読み込む。
		if ($config and file_exists(MOD_PUKI_CONFIG_DIR.$config.".php")) {
			include (MOD_PUKI_CONFIG_DIR.$config.".php");
		} else if ($config and file_exists(MOD_PUKI_CONFIG_DIR.$config.".dist.php")){
			include (MOD_PUKI_CONFIG_DIR.$config.".dist.php");
		}
	}

	/**
	 * @desc		PukiWiki書式の文字列をHTMLに変換する。
	 *				実際には、parseメソッドとrenderメソッドを連続して呼び出している。
	 *
	 * @param		$wikistr	PukiWiki書式にて記述された文字列
	 * @return		変換結果のHTML文字列
	 * 
	 * @author		
	 */
	function transform($wikistr) {
		$this->parse($wikistr);
		return $this->render();
	}

	/**
	 * @desc		PukiWiki書式の文字列を解釈する。
	 *
	 * @param		$wikistr	PukiWiki書式にて記述された文字列
	 * @return
	 * 
	 * @author		
	 */
	function parse($wikistr) {
		//Wikiソースの保存とmd5ハッシュの取得
		$this->_source = $wikistr;
		$this->_md5hash = md5($wikistr);

		//他のPukiWikiシステムとの連携初期化
		$this->_init_PukiWiki_env();
		
		// キャッシュ確認 by nao-pon
		if (PukiWikiConfig::getParam('use_cache')) {
			$cache_file = MOD_PUKI_CACHE_DIR.$this->_md5hash.".cache";
			if (file_exists($cache_file)) return;
		}
		
		if (!is_array($wikistr)) {
			$wikistr = $this->_line_explode($wikistr);
		}
		$this->_body->parse($wikistr);
	}

	/**
	 * @desc		parseメソッドによって解釈された結果を元にしてHTML文字列を出力
	 *
	 * @return		変換結果のHTML文字列
	 * 
	 * @author		
	 */
	function render() {
		global $_PukiWikiFootExplain;

		// キャッシュ確認 by nao-pon
		if (PukiWikiConfig::getParam('use_cache')) {
			$cache_file = MOD_PUKI_CACHE_DIR.$this->_md5hash.".cache";
			if (file_exists($cache_file)) return join('',file($cache_file));
		}
		
		$retstr = $this->_body->toString();

		$retstr = $this->_fix_table_br($retstr);
		if (count($_PukiWikiFootExplain)) {
			ksort($_PukiWikiFootExplain,SORT_NUMERIC);
			$retstr .= count($_PukiWikiFootExplain) ? PukiWikiConfig::getParam('note_hr').join("\n",$_PukiWikiFootExplain) : '';
		}
		$_PukiWikiFootExplain=array();

		// 自ホスト名を省略 マルチドメイン対策 Original by nao-pon
		@list($host,$port) = explode(':',$_SERVER['HTTP_HOST']);
		if (!$port) {
			if (!empty($_SERVER['SSL']) and ($_SERVER['SSL']=='on')) {
				$thishost = 'https://'.$host;
			} else {
				$thishost = 'http://'.$host;
			}
		} else if (!empty($_SERVER['SSL']) and ($_SERVER['SSL']=='on')) {
			$thishost = 'https://'.$host.":".$port;
		} else {
			$thishost = 'http://'.$host.":".$port;
		}
		$retstr = str_replace("<a href=\"{$thishost}","<a href=\"",$retstr);

		if (PukiWikiConfig::getParam('use_cache'))
		{
			//キャッシュ保存 by nao-pon
			$fp = fopen($cache_file, "wb");
			fwrite($fp, $retstr);
			fclose($fp);
		}
		return  $retstr;
	}

	function getSource() {
		return $this->_source;
	}
	
	// ソースを取得
	function getLocalPage($page = NULL)
	{
		if (! PukiWikiFunc::is_local_page($page)) {
			return "";
		} else {
			$source = str_replace("\r", '', file(PukiWikiFunc::get_local_filename($page)));
			return implode("",$source);
		}
	}
	// Private メソッド関数群

	function _line_explode($string) {
		if (PukiWikiConfig::getParam("ExtTable")) {
			$string = preg_replace("/((\x0D\x0A)|(\x0D)|(\x0A))/","\n",$string);
			//表内箇所の判定のため表と表の間は空行が2行必要
			$string = str_replace("|\n\n|","|\n\n\n|",$string);
			//表内はすべて置換
			$string = preg_replace("/(^|\n)(\|[^\r]+?\|)(\n[^|]|$)/e","'$1'.stripslashes(str_replace('->\n','___td_br___','$2')).'$3'",$string);
			//echo $string."<br/>";
			//表と表の間は空行2行を1行に戻す
			$string = str_replace("|\n\n\n|","|\n\n|",$string);
		}
		$string = explode("\n", $string);
		return $string;
	}
	
	function _fix_table_br($string) {
		$string = str_replace("~___td_br___","<br>",$string);
		$string = str_replace("___td_br___","",$string);
		$string = preg_replace("/^<p>([^<>\n]*)<\/p>$/sD","$1",$string);
		return $string;
	}

	function _init_PukiWiki_env() {
		//他のPukiWikiシステムとの連携時の初期化 Original By nao-pon
		//  PukiWikiMod用共通リンクへの対応
		//  AutoLink有効時に、AutoLinkデータ読込と、AutoLinkデータ更新時のキャッシュクリア
	
		// PukiWikiMod 共通リンクディレクトリ読み込み by nao-pon
		$wiki_common_dirs = "";
		if (defined('MOD_PUKI_WIKI_CACHE_DIR')) {
			if ((MOD_PUKI_WIKI_VER == "1.3") && file_exists(MOD_PUKI_WIKI_CACHE_DIR."config.php")) {
				include(MOD_PUKI_WIKI_CACHE_DIR."config.php");
			}
		}
		// PukiWikiMod 共通リンクディレクトリ展開
		$wiki_common_dirs = preg_split("/\s+/",trim($wiki_common_dirs));
		sort($wiki_common_dirs,SORT_STRING);
		PukiWikiConfig::setParam('wiki_common_dirs',$wiki_common_dirs);

		// AutoLinkデータ読み込みとチェック(AutoLink有効時のみ)
		$autolink_dat = array();
		if ((PukiWikiConfig::getParam('autolink')) && (defined('MOD_PUKI_WIKI_CACHE_DIR')) && (file_exists(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat'))) {
			$autolink_dat = file(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat');
			if (!file_exists(MOD_PUKI_CACHE_DIR .'autolink.dat') || ($autolink_dat != file(MOD_PUKI_CACHE_DIR .'autolink.dat'))) {
				// 比較用オートリンクデータを保存
				@list($pattern, $pattern_a, $forceignorelist) = $autolink_dat;
				if ($fp = fopen(MOD_PUKI_CACHE_DIR . 'autolink.dat', 'wb')) {
					set_file_buffer($fp, 0);
					flock($fp, LOCK_EX);
					rewind($fp);
					fputs($fp, trim($pattern)   . "\n");
					if (count($autolink_dat)==3) {
						fputs($fp, trim($pattern_a) . "\n");
						fputs($fp, trim($forceignorelist) . "\n");
					}
					flock($fp, LOCK_UN);
					fclose($fp);
				} else {
//					die_message('Cannot write autolink file '. MOD_PUKI_CACHE_DIR . '/autolink.dat<br />Maybe permission is not writable');
				}
				
				// オートリンクデータが更新されているのでキャッシュをクリア
				$dh = dir(MOD_PUKI_CACHE_DIR);
				while (($file = $dh->read()) !== FALSE) {
					if (substr($file,-6) != '.cache') {
						continue;
					}
					$file = MOD_PUKI_CACHE_DIR.$file;
					unlink($file);
				}
				$dh->close();
			}
		}
		PukiWikiConfig::setParam('autolink_dat',$autolink_dat);
	}
}
?>