<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//

function plugin_size_inline()
{
	if (func_num_args() != 2)
	{
		return FALSE;
	}
	
	list($size,$body) = func_get_args();
	
	if ($size == '' or $body == '')
	{
		return FALSE;
	}

	if (!preg_match('/^\d+$/',$size))
	{
		return $body;
	}
	
	$s_size = htmlspecialchars($size);
	return "<span style=\"font-size:{$s_size}px;display:inline-block;line-height:130%;text-indent:0px\">$body</span>";
}
?>
