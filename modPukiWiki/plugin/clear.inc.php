<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
// div class="clear"��ɽ������
// plugin=clear

function plugin_clear_convert() {
	return '<div class="'.PukiWikiConfig::getParam('style_prefix').'clear"></div>';
}
?>
