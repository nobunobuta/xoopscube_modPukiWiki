<?php
//環境設定ファイル

/*
 * アップロードファイル関連(#ref #isbn等の画像キャッシュ用に使用)
 *
 * XOOPS環境下では、uploadsディレクトリに自動設定
 * WordPress環境下では、Fileアップロード関連の設定を参照して自動設定
 */
//アップロードファイルのディレクトリ （最後は "/" で終わる事）
// if (!defined('MOD_PUKI_UPLOAD_DIR')) define('MOD_PUKI_UPLOAD_DIR','/var/www/htdocs/uploads/');

//アップロードファイルのURL （最後は "/" で終わる事）
// if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL','http://foo.bar.com/uploads/');

/*
 * PukiWikiとの連携時に設定 XOOPSのB-Wiki及びPukiWikiModモジュールは自動検知
 */

//PukiWikiのURL
// if (!defined('MOD_PUKI_WIKI_URL')) define('MOD_PUKI_WIKI_URL','http://foo.bar.com/pukiwiki/index.php');

//PukiWikiのwikiテキストの格納ディレクトリ
// if (!defined('MOD_PUKI_WIKI_DATA_DIR')) define('MOD_PUKI_WIKI_DATA_DIR','/var/www/htdocs/pukiwiki/wiki/');

//PukiWikiのキャッシュディレクトリ
// if (!defined('MOD_PUKI_WIKI_CACHE_DIR')) define('MOD_PUKI_WIKI_CACHE_DIR', '/var/www/htdocs/pukiwiki/cache/');

//PukiWikiのバージョン(1.3もしくは1.4)
//if (!defined('MOD_PUKI_WIKI_VER')) define('MOD_PUKI_WIKI_VER','1.4');

/*
 * その他の設定
 */

//PukiWikiの文字エンコーディング指定
// if (!defined('MOD_PUKI_SOURCE_ENCODING')) define('MOD_PUKI_SOURCE_ENCODING','EUC-JP');

//TIMEZONE関連の設定
// if (!defined('MOD_PUKI_ZONETIME')) define('MOD_PUKI_ZONETIME',9 * 3600); // JST = GMT+9
// if (!defined('MOD_PUKI_ZONE')) define('MOD_PUKI_ZONE','JST');
?>
