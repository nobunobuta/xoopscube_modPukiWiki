<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki各種設定用クラス
//  インスタンス化せずにメンバー関数を呼び出す
//  この部分は、globalに頼っているのが少々気にくわないけど・・・
//  PukiWikiのプラグイン等からパラメータ参照する事を考えると、今のところはこれが一番お手軽

class PukiWikiConfig {
	function initParams() {
		global $_PukiWikiParam;
		$_PukiWikiParam = array();
	}

	function setParam($name,$value) {
		global $_PukiWikiParam;
		$_PukiWikiParam[$name] = $value;
	}
	
	function getParam($name) {
		global $_PukiWikiParam;
		if (!empty($_PukiWikiParam[$name])) {
			return $_PukiWikiParam[$name];
		} else {
			return null;
		}
	}
	
	function initRules() {
		global $_PukiWikiRules, $_PukiWikiRulesPattern,$_PukiWikiRulesReplace;
		$_PukiWikiRules = array();
		$_PukiWikiRulesPattern = '';
		$_PukiWikiRulesReplace = '';
	}

	function addRule($from,$to) {
		global $_PukiWikiRules, $_PukiWikiRulesPattern,$_PukiWikiRulesReplace;
		$_PukiWikiRules[$from] = $to;
		$_PukiWikiRulesPattern = array_map(create_function('$a','return "/$a/";'),array_keys($_PukiWikiRules));
		$_PukiWikiRulesReplace = array_values($_PukiWikiRules);
	}

	function addRuleArray($rulearray) {
		global $_PukiWikiRules, $_PukiWikiRulesPattern,$_PukiWikiRulesReplace;
		$_PukiWikiRules = array_merge($_PukiWikiRules, $rulearray);
		$_PukiWikiRulesPattern = array_map(create_function('$a','return "/$a/";'),array_keys($_PukiWikiRules));
		$_PukiWikiRulesReplace = array_values($_PukiWikiRules);
	}

	function applyRules($str) {
		global $_PukiWikiRulesPattern, $_PukiWikiRulesReplace;
		return preg_replace($_PukiWikiRulesPattern, $_PukiWikiRulesReplace, $str);
	}

	function initInterWiki() {
		global $_PukiWikiInterWiki;
		$_PukiWikiInterWiki = array();
	}
	function addInterWiki($str) {
		global $_PukiWikiInterWiki;
		$_PukiWikiInterWiki[] = $str;
	}
	
	function addInterWikiArray($ary) {
		global $_PukiWikiInterWiki;
		$_PukiWikiInterWiki = array_merge($_PukiWikiInterWiki,$ary);
	}
	
	function getInteWikiArray() {
		global $_PukiWikiInterWiki;
		return $_PukiWikiInterWiki;
	}
}
?>