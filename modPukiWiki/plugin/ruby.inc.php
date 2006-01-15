<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//

function plugin_ruby_inline()
{
	if (func_num_args() != 2)
	{
		return FALSE;
	}

	list($ruby,$body) = func_get_args();

	if ($ruby == '' or $body == '')
	{
		return FALSE;
	}

	$s_ruby = htmlspecialchars($ruby);
	if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
		return "<ruby><rb>$body</rb><rp>(</rp><rt>$s_ruby</rt><rp>)</rp></ruby>";
	} else {
		return "$body($s_ruby)";
	}
}
?>
