<?php
function plugin_pical_init() {
	global $pical_has_cat,$pical_mycat, $pical_mid, $xoopsUser;
	if (!defined('XOOPS_ROOT_PATH')) { //XOOPS�Ķ�����̵�������
		return;
	}
	$xoopsDB =& Database::getInstance();
	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='piCal'" ) ;
	$mids = $xoopsDB->fetchRow( $rs ) ;
	if (!count($mids)) {
		$pical_mid = 0;
		return;
	}
	$pical_mid = $mids[ 0 ] ;

	$sql4check_cat = "SELECT cid FROM ".$xoopsDB->prefix('pical_cat')." LIMIT 1" ;
	$pical_has_cat = $xoopsDB->query( $sql4check_cat ) ;

	if ($pical_has_cat) {
		// ��ʬ���Ȥ� mid ����
		if( is_object( $xoopsUser ) ) {
			if(!$xoopsUser->isadmin( $pical_mid )) {
				$member_handler =& xoops_gethandler('member');
				// ���̥桼���ϼ�ʬ�ν�°���륰�롼�פΤ�
				$my_group_ids =& $member_handler->getGroupsByUser( $xoopsUser->uid() ) ;
				$cal->groups = array() ;
				$ids4sql = '(' ;
				foreach( $my_group_ids as $id ) {
					$cal->groups[ $id ] = $system_groups[ $id ] ;
					$ids4sql .= "$id," ;
				}
				$ids4sql .= "0)" ;

				// ���̥桼���Υ��ƥ��ꥢ����������
				$sql = "SELECT distinct cid,pid,cat_title,cat_desc,ismenuitem,cat_extkey1 FROM ".$xoopsDB->prefix('pical_cat')." LEFT JOIN ".$xoopsDB->prefix('group_permission')." ON cid=gperm_itemid WHERE gperm_name='pical_cat' AND gperm_modid='$pical_mid' AND enabled AND gperm_groupid IN $ids4sql ORDER BY weight" ;
				$rs = mysql_query( $sql ) ;
				$pical_mycat = array() ;
				while( $cat = mysql_fetch_object( $rs ) ) {
					$pical_mycat[ intval( $cat->cid ) ] = $cat ;
				}
			} else {
				// �����桼���ξ�������ƥ���
				$sql = "SELECT cid,pid,cat_title,cat_desc,ismenuitem,cat_extkey1 FROM ".$xoopsDB->prefix('pical_cat')."  ORDER BY weight" ;
				$rs = mysql_query( $sql ) ;
				$pical_mycat = array() ;
				while( $cat = mysql_fetch_object( $rs ) ) {
					$pical_mycat[ intval( $cat->cid ) ] = $cat ;
				}
			}
		} else {
			$sql = "SELECT distinct cid,pid,cat_title,cat_desc,ismenuitem,cat_extkey1 FROM ".$xoopsDB->prefix('pical_cat')." LEFT JOIN ".$xoopsDB->prefix('group_permission')." ON cid=gperm_itemid WHERE gperm_name='pical_cat' AND gperm_modid='$pical_mid' AND enabled AND gperm_groupid='".XOOPS_GROUP_ANONYMOUS."' ORDER BY weight" ;
			$rs = mysql_query( $sql ) ;
			$pical_mycat = array() ;
			while( $cat = mysql_fetch_object( $rs ) ) {
				$pical_mycat[ intval( $cat->cid ) ] = $cat ;
			}
		}
	}
	return true;
}
function plugin_pical_convert() {
	global $pical_has_cat,$picat_mycat, $pical_mid, $xoopsUser;
	if (!defined('XOOPS_ROOT_PATH')) { //XOOPS�Ķ�����̵�������
		return '';
	}
	if (!$pical_mid) {
		return '';
	}
	$catname = "";
	list($date,$catname,$option1) = func_get_args();
	$date = str_replace(array('.','/','-'),array('','',''),$date);
	$yyyy = substr($date,0,4);
	$mm = substr($date,4,2);
	$dd = substr($date,6,2);
	$from = mktime(0,0,0,$mm,$dd,$yyyy);
	$to = mktime(0,0,0,$mm,$dd+1,$yyyy);

	global $xoopsUser ;
	if( ! is_object( $xoopsUser ) ) {
		// �����Ԥ������Ȥʤ����(PUBLIC)�쥳���ɤΤ�
		$whr_class = "class='PUBLIC'" ;
	} else if( $xoopsUser->isadmin() ) {
		// �����Ԥ������Ԥʤ���True
		$whr_class = "1" ;
	} else {
		// �̾�桼���ʤ顢PUBLIC�쥳���ɤ����桼��ID�����פ���쥳���ɡ��ޤ��ϡ���°���Ƥ��륰�롼��ID�Τ����ΰ�Ĥ��쥳���ɤΥ��롼��ID�Ȱ��פ���쥳����
		$gids = $xoopsUser->getGroups() ;
		$uid = $xoopsUser->uid() ;
		$ids = '' ;
		// var_dump( $xoopsUser->getGroups() ) ;
		foreach( $gids as $gid ) {
			$ids .= "$gid," ;
		}
		$ids = substr( $ids , 0 , -1 ) ;
		if( intval( $ids ) == 0 ) $group_section = '' ;
		else $group_section = "OR groupid IN ($ids)" ;
		$whr_class = "(class='PUBLIC' OR uid=$uid $group_section)" ;
	}

	$schedules = array();
	$xoopsDB =& Database::getInstance();
	$SQL = "SELECT id, uid, summary, location, contact, description, UNIX_TIMESTAMP(dtstamp) as dtstamp, start, end, allday FROM ".
			$xoopsDB->prefix('pical_event').
			" WHERE start < ".$to." AND end >= ".$from." AND (rrule_pid=0 OR rrule_pid=id) ".
			" AND admission<>0 AND $whr_class AND ".plugin_pical_where_categories($catname).
			" ORDER by allday DESC, start";
//	echo $SQL;
	if ( !$query = $xoopsDB->query($SQL, $maxtopic, 0) ) echo "Error! (piCal)";
	$margin = PukiWikiConfig::getParam('_ul_margin') + PukiWikiConfig::getParam('_ul_left_margin');
	$style = sprintf(PukiWikiConfig::getParam('_list_pad_str'), 1, $margin, $margin);
	$retstr = "<ul $style>";
	while ( $result = $xoopsDB->fetchArray($query) )	{
		$linkurl = XOOPS_URL."/modules/piCal/index.php?action=View&caldate=".$result['dtstamp']."&event_id=".intval($result['id']);
		
		$color_st = "";
		$color_ed = "";
		if ($result['allday']==5) {
			$color_st = "<font color=\"red\">";
			$color_ed = "</font>";
			$range = "��ǰ��</font>";
		} else if ($result['allday']==3) {
			$range = date("m/d",$result['start'])."-".date("m/d",$result['end']);
		} else if ($result['allday']==1) {
			$range = "����";
		} else {
			$range = date("H:i",$result['start'])."-".date("H:i",$result['end']);
		}
		//����ʸ�����å� IE �� "&#13;&#10;"
		$br = (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))? "&#13;&#10;" : " ";
		$title = " title=\"";
		if ($result['location']) {
			$title .= "���:{$result['location']}{$br}";
		}
		if ($result['contact']) {
			$title .= "Ϣ����:{$result['contact']}{$br}";
		}
		if ($result['description']) {
			$title .= "�ܺ�:{$result['description']}{$br}";
		}
		$title .= "\"";
		$retstr .= "<li style=\"vertical-align:middle\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"width:14ex; text-align:left;vertical-align:middle; padding:0;\">".$color_st.$range.$color_ed."</td><td style=\"text-align:left;vertical-align:middle; padding:0;\"><a href=\"".$linkurl."\"".$title.">".$color_st.$result['summary'].$color_ed."</a></td></tr></table></li>";
	}
	$retstr .= "</ul>";
	return $retstr;
}
// ���ƥ���ط���WHERE�Ѿ�������
function plugin_pical_where_categories($catname)
{
	global $pical_has_cat,$pical_mycat, $pical_mid, $xoopsUser;
	if ($pical_has_cat) {
		if( is_object( $xoopsUser ) and $xoopsUser->isadmin( $pical_mid ) ) {
			if(!$catname ) {
				// �����Ԥ������Ԥ�$catname���꤬�ʤ���о��True
				return "1" ;
			} else {
				// �����Ԥ������Ԥ�$catname���꤬����С���������LIKE����
				$catid = 0;
				foreach($pical_mycat as $cid =>$cat) {
					if ($catname == $cat->cat_title) $catid = $cid;
				}
				if($catid) {
					return "categories LIKE '%".sprintf("%05d,",$catid)."%'" ;
				} else {
					// ���ꤵ�줿cid�����¤ˤʤ�
					return '0' ;
				}
			}
		} else {
			if(!$catname) {
				// �����Ԥ������԰ʳ���$catname���꤬�ʤ���С�CAT2GROUP�ˤ������
				$limit_from_perm = "categories='' OR " ;
				foreach( $pical_mycat as $cid => $cat ) {
					$limit_from_perm .= "categories LIKE '%".sprintf("%05d,",$cid)."%' OR " ;
				}
				$limit_from_perm = substr( $limit_from_perm , 0 , -3 ) ;
				$limit_from_perm = "($limit_from_perm)";
				return $limit_from_perm ;
			} else {
				// �����Ԥ������԰ʳ���$catname���꤬����С����¥����å�����$cid����

				$catid = 0;
				foreach($pical_mycat as $cid =>$cat) {
					if ($catname == $cat->cat_title) $catid = $cid;
				}
				if($catid) {
					return "categories LIKE '%".sprintf("%05d,",$catid)."%'" ;
				} else {
					// ���ꤵ�줿cid�����¤ˤʤ�
					return '0' ;
				}
			}
		}
	} else {
		return '1' ;
	}
}
?>
