<?php
// $Id$

/*
 * countdown.inc.php
 * License: GPL
 * Author: nao-pon http://hypweb.net
 * XOOPS Module Block Plugin
 *
 * XOOPSのブロックを表示するプラグイン
 */

// Modified for modPukiWiki by nobunobu
//   2004/08/10 : XOOPS環境下で無い場合は、即刻リターン

function plugin_xoopsblock_init() {
	if (defined('XOOPS_ROOT_PATH')) { //XOOPS環境下で無い場合用
		include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
		include_once(XOOPS_ROOT_PATH."/class/xoopsblock.php");
	}
}

function plugin_xoopsblock_convert() {
	if (!defined('XOOPS_ROOT_PATH')) { //XOOPS環境下で無い場合用
		return '';
	}
	$old_errrpt = error_reporting(0);
	list($tgt,$option1,$option2) = func_get_args();
	
	unset($tgt_bid);
	if (preg_match("/^\d+$/",$tgt))
		$tgt_bid = $tgt;

	$align = "left";
	$around = false;
	$width = "";
	if (preg_match("/^(left|center|right)$/i",$option2,$arg))
		$align = $arg[1];
	if (preg_match("/^(left|center|right)$/i",$option1,$arg))
		$align = $arg[1];
	if (preg_match("/^(around|float)(:?w?([\d]+%?))?$/i",$option2,$arg))
	{
		if ($arg[1]) $around = true;
		$width = (!strstr($arg[3],"%"))? $arg[3]."px" : $arg[3];
		$width = ",width:".$arg[3].";";
	}
	if (preg_match("/^(around|float)(:?w?([\d]+%?))?$/i",$option1,$arg))
	{
		if ($arg[1]) $around = true;
		$width = (!strstr($arg[3],"%"))? $arg[3]."px" : $arg[3];
		$width = " width:".$width.";";
	}
	$style = " style='float:{$align};{$width}'";
	$clear = ($around)? "" : "<div style='clear:both;'></div>";

	global $xoopsUser;
	$xoopsblock = new XoopsBlock();
	$xoopsgroup = new XoopsGroup();
	$arr = array();
	$side = null;
	
	if ( $xoopsUser ) {
		//$arr = $xoopsblock->getAllBlocksByGroup($xoopsUser->groups(), true, $side, XOOPS_BLOCK_VISIBLE);
		$arr = $xoopsblock->getAllBlocksByGroup($xoopsUser->groups());
	} else {
		if (method_exists($xoopsgroup,"getByType")){
			//XOOPS 1.3
			//$arr = $xoopsblock->getAllBlocksByGroup($xoopsgroup->getByType("Anonymous"), true, $side, XOOPS_BLOCK_VISIBLE);
			$arr = $xoopsblock->getAllBlocksByGroup($xoopsgroup->getByType("Anonymous"));
		} else {
			//XOOPS 2
			//$arr = $xoopsblock->getAllBlocksByGroup(plugin_xoopsblock_getByType("Anonymous"), true, $side, XOOPS_BLOCK_VISIBLE);
			$arr = $xoopsblock->getAllBlocksByGroup(plugin_xoopsblock_getByType("Anonymous"));
		}
	}
	
	$ret = "";
	if (file_exists(XOOPS_ROOT_PATH.'/class/template.php'))
	{
		// XOOPS 2 系用
		require_once XOOPS_ROOT_PATH.'/class/template.php';
		$xoopsTpl = new XoopsTpl();
	}
	foreach ( $arr as $myblock ) {
		$block = array();
		$block_type = ($myblock->getVar("type"))? $myblock->getVar("type") : $myblock->getVar("block_type");
		//$name = ($myblock->getVar("type") != "C") ? $myblock->getVar("name") : $myblock->getVar("title");
		$name = ($block_type != "C") ? $myblock->getVar("name") : $myblock->getVar("title");
		$bid = $myblock->getVar('bid');

		if ($tgt == "?"){
			$ret .= "<li>(".$bid.")".$name."</li>";
		} else {
			if ($tgt_bid === $bid || $tgt == $name){
				$block = $myblock->buildBlock();
				if (!is_object($xoopsTpl))
					// XOOPS 1 系用
					$ret = $block['content'];
				else
				{
					// XOOPS 2 系用
					$bcachetime = $myblock->getVar('bcachetime');
					if (empty($bcachetime)) {
						$xoopsTpl->xoops_setCaching(0);
					} else {
						$xoopsTpl->xoops_setCaching(2);
						$xoopsTpl->xoops_setCacheTime($bcachetime);
					}
					$btpl = $myblock->getVar('template');
					if ($btpl != '') {
						if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl)) {
							if (!$block) {
								$ret = "";
							}
							$xoopsTpl->assign_by_ref('block', $block);
							$bcontent =& $xoopsTpl->fetch('db:'.$btpl);
							$xoopsTpl->clear_assign('block');
						} else {
							$bcontent =& $xoopsTpl->fetch('db:'.$btpl);
						}
					} else {
						if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
							if (!$block) {
								$ret = "";
							}
							$xoopsTpl->assign_by_ref('dummy_content', $block['content']);
							$bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
							$xoopsTpl->clear_assign('block');
						} else {
							$bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
						}
					}
					$ret = $bcontent;
				}
				unset($myblock);
				unset($block);
				break;
			}
		}
		unset($myblock);
		unset($block);
	}
	if ($tgt == "?") $ret = "<ul>$ret</ul>";
	unset($xoopsblock,$xoopsgroup);
	error_reporting($old_errrpt);
	return "<div{$style}>{$ret}</div>{$clear}";
}

function plugin_xoopsblock_getByType($type=""){
	// For XOOPS 2
	global $xoopsDB;
	$ret = array();
	$where_query = "";
	if ( !empty($type) ) {
		$where_query = " WHERE group_type='".$type."'";
	}
	$sql = "SELECT groupid FROM ".$xoopsDB->prefix("groups")."".$where_query;
	$result = $xoopsDB->query($sql);
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$ret[] = $myrow['groupid'];
	}
	return $ret;
}


?>