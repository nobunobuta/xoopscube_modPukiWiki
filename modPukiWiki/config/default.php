<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki�ν������ѥ�᡼��(default)

//��PukiWiki�Υѥ��������
	$_settings['WikiName'] = '(?:[A-Z][a-z]+){2,}(?!\w)';
	$_settings['BracketName'] = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
	$_settings['InterWikiName'] = "(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])";
	$_settings['NotePattern'] = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// ������󥰥���å����ͭ���ˤ���
	$_settings['use_cache'] = 0;
/////////////////////////////////////////////////
// ���Ԥ�ȿ�Ǥ���(���Ԥ�<br />���ִ�����)
	$_settings['line_break'] = 0;
/////////////////////////////////////////////////
// <pre>�ι�Ƭ���ڡ�����ҤȤļ�����
	$_settings['preformat_ltrim'] = 1;
/////////////////////////////////////////////////
// ��ĥ�ơ��֥�񼰤���Ѥ���
	$_settings['ExtTable'] = false;
/////////////////////////////////////////////////
// ���Ф��Ԥ˸�ͭ�Υ��󥫡���ư��������
	$_settings['fixed_heading_anchor'] = 0;
	$_settings['_symbol_anchor'] = '&dagger;';
	$_settings['_symbol_noexists'] = '?';
/////////////////////////////////////////////////
// �硦�����Ф������ܼ�������󥯤�ʸ��
	$_settings['top'] = "";
/////////////////////////////////////////////////
// ���ɽ���򥳥�ѥ��Ȥˤ���
	$_settings['link_compact'] = 0;

/////////////////////////////////////////////////
// AutoLink��ͭ���ˤ�����ϡ�AutoLink�оݤȤʤ�
// �ڡ���̾�κ�û�Х��ȿ������
// AutoLink��̵���ˤ������0
	$_settings['autolink'] = 0;
/////////////////////////////////////////////////
// URLʸ�����ưŪ�˥���Ѵ��������1
	$_settings['autourllink'] = 1;
/////////////////////////////////////////////////
// WikiName�� *̵����* �������1
	$_settings['nowikiname'] = 1;
/////////////////////////////////////////////////
// ���եե����ޥå�
	$_settings['date_format'] = 'Y-m-d';
/////////////////////////////////////////////////
// ����ե����ޥå�
	$_settings['time_format'] = 'H:i:s';
/////////////////////////////////////////////////
// ��������
	$_msg_week = array('��','��','��','��','��','��','��');
//	$_msg_week = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	$_settings['$weeklabels'] = $_msg_week;

//��������class�Υץ�ե��å���
	$_settings['style_prefix'] = 'modPuki_';

//�ꥹ��ɽ���Υ�������
	$_settings['_ul_left_margin'] = 0;   // �ꥹ�ȤȲ��̺�ü�Ȥδֳ�(px)
	$_settings['_ul_margin'] = 16;       // �ꥹ�Ȥγ��ش֤δֳ�(px)
	$_settings['_ol_left_margin'] = 0;   // �ꥹ�ȤȲ��̺�ü�Ȥδֳ�(px)
	$_settings['_ol_margin'] = 16;       // �ꥹ�Ȥγ��ش֤δֳ�(px)
	$_settings['_dl_left_margin'] = 0;   // �ꥹ�ȤȲ��̺�ü�Ȥδֳ�(px)
	$_settings['_dl_margin'] = 16;        // �ꥹ�Ȥγ��ش֤δֳ�(px)
	$_settings['_list_pad_str'] = ' class="'.$_settings['style_prefix'].'list%d" style="padding-left:%dpx;margin-left:%dpx"';

/////////////////////////////////////////////////
// ��ʿ���Υ���
	$_settings['hr'] = '<hr class="'.$_settings['style_prefix'].'full_hr" />';
/////////////////////////////////////////////////
// ʸ��������ľ����ɽ�����륿��
	$_settings['note_hr'] = '<hr class="'.$_settings['style_prefix'].'note_hr" />';

/////////////////////////////////////////////////
// HTTP�ꥯ�����Ȥ˥ץ��������Ф���Ѥ���
	$_settings['use_proxy'] = 0;
// proxy �ۥ���
	$_settings['proxy_host'] = 'proxy.xxx.yyy.zzz';
// proxy �ݡ����ֹ�
	$_settings['proxy_port'] = 8080;
// proxy��Basicǧ�ڤ�ɬ�פʾ���1
	$_settings['need_proxy_auth'] = 0;
// proxy��Basicǧ����ID,PW
	$_settings['proxy_auth_user'] = 'foo';
	$_settings['proxy_auth_pass'] = 'foo_password';
// �ץ��������Ф���Ѥ��ʤ��ۥ��ȤΥꥹ��
	$_settings['no_proxy'] = array(
	'localhost',        // localhost
	'127.0.0.0/8',      // loopback
	'10.0.0.0/8',     // private class A
	'172.16.0.0/12',  // private class B
	'192.168.0.0/16', // private class C
	//'no-proxy.com',
	);

//�֤������롼��
	$_entity_pattern = trim(join('',file(MOD_PUKI_CONFIG_DIR.'entities.dat')));

	$_rules = array(
		"COLOR\(([^\(\)]*)\){([^}]*)}"	=> '<span style="color:$1">$2</span>',
		"SIZE\(([^\(\)]*)\){([^}]*)}"	=> '<span style="font-size:$1px">$2</span>',
		"COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)"	=> '<span style="color:$1">$2</span>',
		"SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)"	=> '<span class="'.PukiWikiConfig::getParam('style_prefix').'size$1">$2</span>',
		"%%%(?!%)((?:(?!%%%).)*)%%%"	=> '<ins>$1</ins>',
		"%%(?!%)((?:(?!%%).)*)%%"	=> '<del>$1</del>',
		"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
		"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
		"__((?:(?!__).)*)__" => '<u>\\1</u>',
		'&amp;br;'	=> '<br />',
		"\r"=>"<br />\n", /* �����˥�����ϲ��� */
		'^#contents$'=>'<del>#contents</del>',
		'&amp;(#[0-9]+|#x[0-9a-f]+|'.$_entity_pattern.');'=>'&$1;',
	);
?>
