<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//

function plugin_br_convert()
{
	return '<br class="'.PukiWikiConfig::getParam('style_prefix').'spacer" />';
}
?>