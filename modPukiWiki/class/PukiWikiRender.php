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
		global $_PukiWikiFootExplain;
		
		if (!is_array($wikistr)) {
			$wikistr = explode("\n", $wikistr);
		}
		
		$this->_body->parse($wikistr);
		$retstr = $this->_body->toString();
		if (count($_PukiWikiFootExplain)) {
			ksort($_PukiWikiFootExplain,SORT_NUMERIC);
			$retstr .= count($_PukiWikiFootExplain) ? PukiWikiConfig::getParam('note_hr').join("\n",$_PukiWikiFootExplain) : '';
		}
		$_PukiWikiFootExplain=array();
		return $retstr;
	}

	function parse($wikistr) {
		if (!is_array($wikistr)) {
			$wikistr = explode("\n", $wikistr);
		}
		$this->_body->parse($wikistr);
	}

	function render() {
		global $_PukiWikiFootExplain;

		$retstr = $this->_body->toString();
		if (count($_PukiWikiFootExplain)) {
			ksort($_PukiWikiFootExplain,SORT_NUMERIC);
			$retstr .= count($_PukiWikiFootExplain) ? PukiWikiConfig::getParam('note_hr').join("\n",$_PukiWikiFootExplain) : '';
		}
		$_PukiWikiFootExplain=array();
		return  $retstr;
	}

}
?>