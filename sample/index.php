<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=EUC-JP" />
<meta http-equiv="content-language" content="ja" />
<title>modPukiWiki����ץ�</title>
<!-- ����ץ�Υ������륷���Ȥ��ɤ߹��ࡣ���ϴĶ��ˤ�ä����� -->
<link rel="stylesheet" type="text/css" media="all" href="../modpukiwiki/sample.css" />
</head>
<body>
<?php
/*
 * ���åץ��ɥե������Ϣ(#ref #isbn���β�������å����Ѥ˻���)
 *    (����ϡ����� modpukiwiki/PukiWiki.ini.php��������٤����ƤǤ���
 *     �����Ǥϡ����ե�����Υǥ��쥯�ȥ��attach�Ȥ��������ǽ�ʥǥ��쥯�ȥ꤬
 *     �����������ˤ��Ƥ��ޤ���
 *
 * XOOPS�Ķ����Ǥϡ�uploads�ǥ��쥯�ȥ�˼�ư����
 * WordPress�Ķ����Ǥϡ�File���åץ��ɴ�Ϣ������򻲾Ȥ��Ƽ�ư����
 */
//���åץ��ɥե�����Υǥ��쥯�ȥ� �ʺǸ�� "/" �ǽ�������
if (!defined('MOD_PUKI_UPLOAD_DIR')) define('MOD_PUKI_UPLOAD_DIR','./attach/');

//���åץ��ɥե������URL �ʺǸ�� "/" �ǽ�������
if (!defined('MOD_PUKI_UPLOAD_URL')) define('MOD_PUKI_UPLOAD_URL','./attach/');
//������ʬ�ϴĶ��ˤ�ä��ѹ����Ʋ�������
//modPukiWiki���ΤΥ��󥯥롼��
require (dirname(__FILE__).'/../modPukiWiki/PukiWiki.php') ;
//PukiWiki�Υ���ץ륽����
$text = <<< EOD
***�إåǥ���
 *�إåǥ��󥰣�
 ʸ�ϣ�
 **�إåǥ��󥰣�
 ʸ�ϣ�
 ***�إåǥ��󥰣�
 ʸ�ϣ�
*�إåǥ��󥰣�
ʸ�ϣ�
**�إåǥ��󥰣�
ʸ�ϣ�
***�إåǥ��󥰣�
ʸ�ϣ�
***���
 [[�ۡ���ڡ���:http://www.kowa.org]]
[[�ۡ���ڡ���:http://www.kowa.org]]

***�ơ��֥�
 |a|a|b|
 |100|300|300|

|a|a|b|
|100|300|300|
***�ꥹ��
 -asdsad
 --dsadasd
-asdsad
--dsadasd
***�ֹ��դ��ꥹ��
 +test
 ++test2
 ++test3
 +++testtest
 ++test4
 +test2
 ++test5
 ---ttttt
 ++test4
+test
++test2
++test3
+++testtest
++test4
+test2
++test5
---ttttt
++test4
***���ڤ���
 ----
----
***��Ĵ
 ''aaaa''
''aaaa''
***����饤��ץ饰����
 &color(RED){TEST};~
 &font(RED,15){TEST};
&color(RED){TEST};~
&font(RED,15){TEST};
***PukiWIkiMod ISBN�ץ饰����Υƥ���
 #isbn(4839912653,left)
 #isbn(B0000CAVZK,left)
 #isbn(B00008HC56,left)
 #isbn(clear)
#isbn(4839912653,left)
#isbn(B0000CAVZK,left)
#isbn(B00008HC56,left)
#isbn(clear)
~
***ShowRSS�ץ饰����Υƥ���
 #showrss(http://www.kowa.org/modules/wordpress/wp-rdf.php, anntenna)
#showrss(http://www.kowa.org/modules/wordpress/wp-rdf.php, antenna)
***����ɽ�����ե�����ź��
 &ref(http://www.kowa.org/modules/wordpress/attach/1086419759.jpeg,w:200);~
&ref(http://www.kowa.org/modules/wordpress/attach/1086419759.jpeg,w:200);~
***InterWiki�����
 -[[�Τ֤Τ֤θ���:Google:�Τ֤Τ�]]
-[[�Τ֤Τ֤θ���>Google:�Τ֤Τ�]]
EOD;

//PukiWikiRender���󥹥�������
$render = new PukiWikiRender;
//InterWikiName������
PukiWikiConfig::addInterWiki('[http://www.google.co.jp/search?ie=utf8&oe=utf8&q=$1&lr=lang_ja&hl=ja Google] utf8');
//������󥰤���ɽ��
echo $render->transform($text);
?>
</body>
</html>
