<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
// div class="clear"を表示する
// plugin=clear

function plugin_clear_convert() {
	return '<div class="'.PukiWikiConfig::getParam('style_prefix').'clear"></div>';
}
?>
