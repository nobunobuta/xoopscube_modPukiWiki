<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWikiレンダリングエンジン メインクラス
//
class PukiWikiRender {
	var $_body;
	var $_settings;
	var $_linerules;
	var $_pattern;
	var $_replace;
	
	function PukiWikiRender($config='') {
		//デフォルトの設定ファイル読込
		require (MOD_PUKI_DEFAULT);
		//コンストラクタのパラメータで$configが指定されている場合は、読み込む。
		if ($config and file_exists(MOD_PUKI_CONFIG_DIR.$config.".php")) {
			include (MOD_PUKI_CONFIG_DIR.$config.".php");
		}
		PukiWikiConfig::initParams();
		foreach($_settings as $key=>$value) {
			PukiWikiConfig::setParam($key,$value);
		}
		PukiWikiConfig::initRules();
		PukiWikiConfig::addRuleArray($_rules);
		
		PukiWikiConfig::initInterWiki();
		$this->_body = &new PukiWikiBody($this,1);
	}

	function transform($wikistr) {
		$this->parse($wikistr);
		return $this->render();
	}

	function parse($wikistr) {
		if (!is_array($wikistr)) {
			$wikistr = $this->_line_explode($wikistr);
		}
		$this->_body->parse($wikistr);
	}

	function render() {
		global $_PukiWikiFootExplain;

		$retstr = $this->_body->toString();

		$retstr = $this->_fix_table_br($retstr);
		if (count($_PukiWikiFootExplain)) {
			ksort($_PukiWikiFootExplain,SORT_NUMERIC);
			$retstr .= count($_PukiWikiFootExplain) ? PukiWikiConfig::getParam('note_hr').join("\n",$_PukiWikiFootExplain) : '';
		}
		$_PukiWikiFootExplain=array();
		return  $retstr;
	}



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
		return $string;
	}
}
?>