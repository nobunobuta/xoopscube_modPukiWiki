<?php
//�Ķ�����ե�������ɹ�
include('PukiWiki.ini.php');

if (!defined('MOD_PUKI_SOURCE_ENCODING')) define('MOD_PUKI_SOURCE_ENCODING','EUC-JP');
if (!defined('MOD_PUKI_ZONETIME')) define('MOD_PUKI_ZONETIME',9 * 3600); // JST = GMT+9
if (!defined('MOD_PUKI_ZONE')) define('MOD_PUKI_ZONE','JST');

if (!defined('MOD_PUKI_LOCALZONE')) define('MOD_PUKI_LOCALZONE',date('Z'));
if (!defined('MOD_PUKI_UTIME')) define('MOD_PUKI_UTIME',time()- - MOD_PUKI_LOCALZONE);


if (!defined('MOD_PUKI_BASE')) {
    define('MOD_PUKI_BASE', dirname(__FILE__));
	define('MOD_PUKI_PLUGIN_DIR', MOD_PUKI_BASE . '/plugin/');
	define('MOD_PUKI_CONFIG_DIR',  MOD_PUKI_BASE . '/config/');
	define('MOD_PUKI_IMAGE_DIR',  MOD_PUKI_BASE . '/images/');
	define('MOD_PUKI_DATA_DIR',  MOD_PUKI_BASE . '/wiki/');
	define('MOD_PUKI_DEFAULT', MOD_PUKI_CONFIG_DIR .'default.php');
}

//For XOOPS Environment
if (defined('XOOPS_ROOT_PATH')) {
	//XOOPS�Ķ����ǤγƼ�Ķ���ư����
	if (file_exists(XOOPS_ROOT_PATH.'/modules/bwiki/xoops_version.php')) {
		//XOOPS�Ķ�����B-Wiki��Ƴ������Ƥ������˼�ư���Τ��ƥڡ�����󥯵�ǽ�ʤɤ�ͭ���ˤ��뤿������������
		if (!defined('MOD_PUKI_WIKI_URL')) define('MOD_PUKI_WIKI_URL',XOOPS_URL.'/modules/bwiki/index.php');
		if (!defined('MOD_PUKI_WIKI_DATA_DIR')) define('MOD_PUKI_WIKI_DATA_DIR',XOOPS_ROOT_PATH.'/modules/bwiki/wiki/');
		if (!defined('MOD_PUKI_WIKI_CACHE_DIR')) define('MOD_PUKI_WIKI_CACHE_DIR', XOOPS_ROOT_PATH.'/modules/bwiki/cache/');
		if (!defined('MOD_PUKI_WIKI_UPLOAD_DIR')) define('MOD_PUKI_WIKI_UPLOAD_DIR', XOOPS_ROOT_PATH.'/modules/bwiki/attach/');
		if (!defined('MOD_PUKI_WIKI_VER')) define('MOD_PUKI_WIKI_VER','1.4');
		
	} else if (file_exists(XOOPS_ROOT_PATH.'/modules/pukiwiki/xoops_version.php')) {
		//XOOPS�Ķ�����PukiWikiMod��Ƴ������Ƥ������˼�ư���Τ��ƥڡ�����󥯵�ǽ�ʤɤ�ͭ���ˤ��뤿������������
		if (!defined('MOD_PUKI_WIKI_URL')) define('MOD_PUKI_WIKI_URL',XOOPS_URL.'/modules/pukiwiki/index.php');
		if (!defined('MOD_PUKI_WIKI_DATA_DIR')) define('MOD_PUKI_WIKI_DATA_DIR',XOOPS_ROOT_PATH.'/modules/pukiwiki/wiki/');
		if (!defined('MOD_PUKI_WIKI_CACHE_DIR')) define('MOD_PUKI_WIKI_CACHE_DIR', XOOPS_ROOT_PATH.'/modules/pukiwiki/cache/');
		if (!defined('MOD_PUKI_WIKI_UPLOAD_DIR')) define('MOD_PUKI_WIKI_UPLOAD_DIR', XOOPS_ROOT_PATH.'/modules/pukiwiki/attach/');
		if (!defined('MOD_PUKI_WIKI_VER')) define('MOD_PUKI_WIKI_VER','1.3');
	}
	//����å���Υե�������ݴ��� 
	// XOOPS�Ķ����Ǥϡ�cache�ǥ��쥯�ȥ겼��modPukiWiki�Ȥ����ǥ��쥯�ȥ��������Ƽ�ư����
	if (!defined('MOD_PUKI_CACHE_DIR')) {
		define('MOD_PUKI_CACHE_DIR',XOOPS_ROOT_PATH.'/cache/modPukiWiki/');
		if (!file_exists(MOD_PUKI_CACHE_DIR)) mkdir(MOD_PUKI_CACHE_DIR, 0777);
	}
	//��������å���ʤɤΥե�������ݴ��� 
	// XOOPS�Ķ����Ǥϡ�uploads�ǥ��쥯�ȥ겼��modPukiWiki�Ȥ����ǥ��쥯�ȥ��������Ƽ�ư����
	if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL', XOOPS_URL.'/uploads/modPukiWiki/');
	if (!defined('MOD_PUKI_UPLOAD_DIR')) {
		define('MOD_PUKI_UPLOAD_DIR',XOOPS_ROOT_PATH.'/uploads/modPukiWiki/');
		if (!file_exists(MOD_PUKI_UPLOAD_DIR)) mkdir(MOD_PUKI_UPLOAD_DIR, 0777);
	}
//For WordPress Environment
} else if (defined('ABSPATH') and ('WPINC')) {
	//����å���Υե�������ݴ��� 
	// XOOPS�Ķ����Ǥϡ�wp-content�ǥ��쥯�ȥ겼��modPukiWiki�Ȥ����ǥ��쥯�ȥ��������Ƽ�ư����
	if (!defined('MOD_PUKI_CACHE_DIR')) {
		define('MOD_PUKI_CACHE_DIR',ABSPATH.'/wp-content/modPukiWiki/');
		if (!file_exists(MOD_PUKI_CACHE_DIR)) mkdir(MOD_PUKI_CACHE_DIR, 0777);
	}
	//��������å���ʤɤΥե�������ݴ��� 
	// WordPress�Ķ����Ǥϡ�File���åץ��ɴ�Ϣ������򻲾Ȥ��Ƽ�ư����
	if (file_exists(get_settings('fileupload_realpath'))) {
		if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL', get_settings('fileupload_url').'/modPukiWiki/');
		if (!defined('MOD_PUKI_UPLOAD_DIR')) {
			define('MOD_PUKI_UPLOAD_DIR', get_settings('fileupload_realpath').'/modPukiWiki/');
			if (!file_exists(MOD_PUKI_UPLOAD_DIR)) mkdir(MOD_PUKI_UPLOAD_DIR, 0777);
		}
	}
} else {
	//����å���Υե�������ݴ��� 
	// ���ѴĶ����Ǥϡ�modPukiWiki��cache�ǥ��쥯�ȥ�򤽤Τޤ�����
	define('MOD_PUKI_CACHE_DIR', MOD_PUKI_BASE . '/cache/');
}

//Plugin���ǻ��Ѥ�������ե�����򡢲�������å���ǥ��쥯�ȥ����¸���롣
if(defined('MOD_PUKI_UPLOAD_DIR')) {
	$modPukiImages = array(
		'file.gif',
		'noimage.png',
		'smile.gif',
		'bigsmile.gif',
		'huh.gif',
		'oh.gif',
		'wink.gif',
		'sad.gif',
		'heart.gif',
	);
	foreach ($modPukiImages as $modPukiIMG) {
		if (!file_exists(MOD_PUKI_UPLOAD_DIR.$modPukiIMG)) copy (MOD_PUKI_IMAGE_DIR.$modPukiIMG, MOD_PUKI_UPLOAD_DIR.$modPukiIMG);
	}
	if (!defined('MOD_PUKI_NOIMAGE')) define('MOD_PUKI_NOIMAGE',MOD_PUKI_UPLOAD_URL.'/noimage.png');
	if (!defined('MOD_PUKI_FILE_ICON')) define('MOD_PUKI_FILE_ICON',MOD_PUKI_UPLOAD_URL.'/file.gif');
}

//modPukiWiki�γƥ��饹�����
require_once (MOD_PUKI_BASE.'/class/PukiWikiConfig.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiRender.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiElement.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiLink.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiPlugin.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiFunc.php');
//PHP4.2������¸�ߤ��ʤ��ؿ��������
require_once (MOD_PUKI_BASE.'/lib/func.php');
?>
