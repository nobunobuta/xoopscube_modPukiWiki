<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//

function plugin_font_inline()
{
	$prmcnt = func_num_args();
	if ($prmcnt < 2)
	{
		return FALSE;
	}
	// ���顼�͡��������ɽ��
	$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow";

	$prms = func_get_args();
	$body = array_pop($prms);

	$style = "";
	$color_type = true;
	$decoration = array();
	foreach ($prms as $prm)
	{
		if ($prm == "")
			$color_type = false;
		elseif (preg_match("/^i(talic)?$/i",$prm))
			$style .= "font-style:italic;";
		elseif (preg_match("/^b(old)?$/i",$prm))
			$style .= "font-weight:bold;";
		elseif (preg_match("/^bl(ink)?$/i",$prm))
			$decoration[] = "blink";
		elseif (preg_match("/^u(nderline)?$/i",$prm))
			$decoration[] = "underline";
		elseif (preg_match("/^o(verline)?$/i",$prm))
			$decoration[] = "overline";
		elseif (preg_match("/^l(ine-through)?$/i",$prm))
			$decoration[] = "line-through";
		elseif (preg_match('/^(#[0-9a-f]+|'.$colors_reg.')$/i',$prm,$color))
		{
			if ($color_type)
			{
				$style .= "color:".htmlspecialchars($color[1]).";";
				$color_type = false;
			} else {
				$style .= "background-color:".htmlspecialchars($color[1]).";";
			}
		}
		elseif (preg_match('/^(\d+)$/',$prm,$size))
			//$style .= "font-size:".htmlspecialchars($size[1])."px;display:inline-block;line-height:130%;text-indent:0px;";
			$style .= "font-size:".htmlspecialchars($size[1])."px;line-height:130%;";
		elseif (preg_match('/^(\d+(%|px|pt|em))$/',$prm,$size))
			//$style .= "font-size:".htmlspecialchars($size[1]).";display:inline-block;line-height:130%;text-indent:0px;";
			$style .= "font-size:".htmlspecialchars($size[1]).";line-height:130%;";
		
	}
	if (count($decoration))
		$style .= "text-decoration:".join(" ",$decoration).";";
	
	if ($style == "") return $body;
	return "<span style=\"$style\">$body</span>";
}

?>