<?php
//環境定義ファイルの読込
if (file_exists('PukiWiki.ini.php')) {
	include('PukiWiki.ini.php');
} else if (file_exists('PukiWiki.ini.dist.php')) {
	include('PukiWiki.ini.dist.php');
}

if (!defined('MOD_PUKI_SOURCE_ENCODING')) define('MOD_PUKI_SOURCE_ENCODING','EUC-JP');
if (!defined('MOD_PUKI_ZONETIME')) define('MOD_PUKI_ZONETIME',9 * 3600); // JST = GMT+9
if (!defined('MOD_PUKI_ZONE')) define('MOD_PUKI_ZONE','JST');

if (!defined('MOD_PUKI_LOCALZONE')) define('MOD_PUKI_LOCALZONE',date('Z'));
if (!defined('MOD_PUKI_UTIME')) define('MOD_PUKI_UTIME',time()-MOD_PUKI_LOCALZONE);


if (!defined('MOD_PUKI_BASE')) {
    define('MOD_PUKI_BASE', dirname(__FILE__));
	define('MOD_PUKI_PLUGIN_DIR', MOD_PUKI_BASE . '/plugin/');
	define('MOD_PUKI_CONFIG_DIR',  MOD_PUKI_BASE . '/config/');
	define('MOD_PUKI_IMAGE_DIR',  MOD_PUKI_BASE . '/images/');
	define('MOD_PUKI_DATA_DIR',  MOD_PUKI_BASE . '/wiki/');
	define('MOD_PUKI_LANG_BASE',  MOD_PUKI_BASE . '/lang/');
	define('MOD_PUKI_DEFAULT', MOD_PUKI_CONFIG_DIR .'default.php');
}

//For XOOPS Environment
if (defined('XOOPS_ROOT_PATH')) {
	//XOOPS環境下での各種環境を自動設定
	if (file_exists(XOOPS_ROOT_PATH.'/modules/bwiki/xoops_version.php')) {
		//XOOPS環境下でB-Wikiが導入されていた場合に自動検知してページリンク機能などを有効にするための定義を設定
		if (!defined('MOD_PUKI_WIKI_URL')) define('MOD_PUKI_WIKI_URL',XOOPS_URL.'/modules/bwiki/index.php');
		if (!defined('MOD_PUKI_WIKI_DATA_DIR')) define('MOD_PUKI_WIKI_DATA_DIR',XOOPS_ROOT_PATH.'/modules/bwiki/wiki/');
		if (!defined('MOD_PUKI_WIKI_CACHE_DIR')) define('MOD_PUKI_WIKI_CACHE_DIR', XOOPS_ROOT_PATH.'/modules/bwiki/cache/');
		if (!defined('MOD_PUKI_WIKI_UPLOAD_DIR')) define('MOD_PUKI_WIKI_UPLOAD_DIR', XOOPS_ROOT_PATH.'/modules/bwiki/attach/');
		if (!defined('MOD_PUKI_WIKI_VER')) define('MOD_PUKI_WIKI_VER','1.4');
		
	} else if (file_exists(XOOPS_ROOT_PATH.'/modules/pukiwiki/xoops_version.php')) {
		//XOOPS環境下でPukiWikiModが導入されていた場合に自動検知してページリンク機能などを有効にするための定義を設定
		if (!defined('MOD_PUKI_WIKI_URL')) define('MOD_PUKI_WIKI_URL',XOOPS_URL.'/modules/pukiwiki/index.php');
		if (!defined('MOD_PUKI_WIKI_DATA_DIR')) define('MOD_PUKI_WIKI_DATA_DIR',XOOPS_ROOT_PATH.'/modules/pukiwiki/wiki/');
		if (!defined('MOD_PUKI_WIKI_CACHE_DIR')) define('MOD_PUKI_WIKI_CACHE_DIR', XOOPS_ROOT_PATH.'/modules/pukiwiki/cache/');
		if (!defined('MOD_PUKI_WIKI_UPLOAD_DIR')) define('MOD_PUKI_WIKI_UPLOAD_DIR', XOOPS_ROOT_PATH.'/modules/pukiwiki/attach/');
		if (!defined('MOD_PUKI_WIKI_VER')) define('MOD_PUKI_WIKI_VER','1.3');
	}
	//キャッシュのファイルの保管先 
	// XOOPS環境下では、cacheディレクトリ下にmodPukiWikiというディレクトリを作成して自動設定
	if (!defined('MOD_PUKI_CACHE_DIR')) {
		define('MOD_PUKI_CACHE_DIR',XOOPS_ROOT_PATH.'/cache/modPukiWiki/');
		if (!file_exists(MOD_PUKI_CACHE_DIR)) mkdir(MOD_PUKI_CACHE_DIR, 0777);
	}
	//画像キャッシュなどのファイルの保管先 
	// XOOPS環境下では、uploadsディレクトリ下にmodPukiWikiというディレクトリを作成して自動設定
	if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL', XOOPS_URL.'/uploads/modPukiWiki/');
	if (!defined('MOD_PUKI_UPLOAD_DIR')) {
		define('MOD_PUKI_UPLOAD_DIR',XOOPS_ROOT_PATH.'/uploads/modPukiWiki/');
		if (!file_exists(MOD_PUKI_UPLOAD_DIR)) mkdir(MOD_PUKI_UPLOAD_DIR, 0777);
	}
	//言語ファイル
	if (!defined('MOD_PUKI_LANG')) {
		if (!defined('_LANGCODE')) {
			define('MOD_PUKI_LANG','en');
		} else {
			if (file_exists(MOD_PUKI_LANG_BASE.'/'._LANGCODE)) {
				define('MOD_PUKI_LANG',_LANGCODE);
			} else {
				define('MOD_PUKI_LANG', 'en');
			}
		}
	}
} else if (defined('ABSPATH') and ('WPINC')) {
//For WordPress Environment
	//キャッシュのファイルの保管先 
	// XOOPS環境下では、wp-contentディレクトリ下にmodPukiWikiというディレクトリを作成して自動設定
	if (!defined('MOD_PUKI_CACHE_DIR')) {
		define('MOD_PUKI_CACHE_DIR',ABSPATH.'/wp-content/modPukiWiki/');
		if (!file_exists(MOD_PUKI_CACHE_DIR)) mkdir(MOD_PUKI_CACHE_DIR, 0777);
	}
	//画像キャッシュなどのファイルの保管先 
	// WordPress環境下では、Fileアップロード関連の設定を参照して自動設定
	if (file_exists(get_settings('fileupload_realpath'))) {
		if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL', get_settings('fileupload_url').'/modPukiWiki/');
		if (!defined('MOD_PUKI_UPLOAD_DIR')) {
			define('MOD_PUKI_UPLOAD_DIR', get_settings('fileupload_realpath').'/modPukiWiki/');
			if (!file_exists(MOD_PUKI_UPLOAD_DIR)) mkdir(MOD_PUKI_UPLOAD_DIR, 0777);
		}
	}
} else {
	//キャッシュのファイルの保管先 
	// 汎用環境下では、modPukiWikiのcacheディレクトリをそのまま利用
	if (!defined('MOD_PUKI_CACHE_DIR')) define('MOD_PUKI_CACHE_DIR', MOD_PUKI_BASE . '/cache/');
	// デフォルトの言語は日本語に
	if (!defined('MOD_PUKI_LANG')) define('MOD_PUKI_LANG','ja');
}
if (!defined('MOD_PUKI_LANG_DIR')) define('MOD_PUKI_LANG_DIR',MOD_PUKI_LANG_BASE.'/'.MOD_PUKI_LANG);

//Plugin等で使用する画像ファイルを、画像キャッシュディレクトリに保存する。
if(defined('MOD_PUKI_UPLOAD_DIR')) {
	$modPukiImages = array(
		'file.gif',
		'noimage.png',
		'noimage_s.png',
		'smile.gif',
		'bigsmile.gif',
		'huh.gif',
		'oh.gif',
		'wink.gif',
		'sad.gif',
		'heart.gif',
		'worried.png',
	);
	foreach ($modPukiImages as $modPukiIMG) {
		if (!file_exists(MOD_PUKI_UPLOAD_DIR.$modPukiIMG)) copy (MOD_PUKI_IMAGE_DIR.$modPukiIMG, MOD_PUKI_UPLOAD_DIR.$modPukiIMG);
	}
	if (!defined('MOD_PUKI_NOIMAGE')) define('MOD_PUKI_NOIMAGE',MOD_PUKI_UPLOAD_URL.'/noimage.png');
	if (!defined('MOD_PUKI_NOIMAGE_S')) define('MOD_PUKI_NOIMAGE_S',MOD_PUKI_UPLOAD_URL.'/noimage_s.png');
	if (!defined('MOD_PUKI_FILE_ICON')) define('MOD_PUKI_FILE_ICON',MOD_PUKI_UPLOAD_URL.'/file.gif');
}

//modPukiWikiの各クラスをロード
require_once (MOD_PUKI_BASE.'/class/PukiWikiConfig.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiRender.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiElement.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiLink.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiPlugin.php');
require_once (MOD_PUKI_BASE.'/class/PukiWikiFunc.php');
//PHP4.2以前に存在しない関数群の定義
require_once (MOD_PUKI_BASE.'/lib/func.php');
?>
