<?php
/* Don't remove this line */ $cur_PATH = $_SERVER['SCRIPT_FILENAME'];
/* Don't remove this line */ if (!defined('XOOPS_ROOT_PATH')) { exit; }
/* Don't remove this line */ $wp_block_style = <<<EOD
/* ÈþÆý */
#wpBlockContent$wp_num {
	padding-left: 5px;
	padding-right: 5px;
}

#wpBlockContent$wp_num h2 {
	font-size : 16px;
	font-family: "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;;
	border-bottom: 1px solid #dcdcdc;
	margin-bottom: 5px;
}

#wpBlockContent$wp_num h3 {
	font-size : 14px;
	font-family: "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;
	margin-bottom: 5px;
}

#wpBlockContent$wp_num a {
	color: #9B9FAE;
}

#wpBlockContent$wp_num a img {
	border: none;
}

#wpBlockContent$wp_num a:visited {
	color: #9B9FAE;
}

#wpBlockContent$wp_num a:hover {
	color: #7AA0CF;
}

#wpBlockContent$wp_num .storytitle {
	margin: 0;
}

#wpBlockContent$wp_num .storytitle a {
	text-decoration: none;
}

#wpBlockContent$wp_num .meta {
	font-size: 0.9em;
}

#wpBlockContent$wp_num .meta,#wpBlockContent$wp_num .meta a {
	color: #808080;
	font-weight: normal;
	letter-spacing: 0;
}
#wpBlockContent$wp_num .meta ul {
	display: inline;
	margin: 0;
	padding: 0;
	list-style: none;
}

#wpBlockContent$wp_num .meta li {
	display: inline;
}


#wpBlockContent$wp_num .storycontent{
	font: 95% "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;
}

#wpBlockContent$wp_num div.storycontent {
	clear:right;
}

#wpBlockContent$wp_num .feedback {
	color: #ccc;
	text-align: right;
}

#wpBlockContent$wp_num p,#wpBlockContent$wp_num  li,#wpBlockContent$wp_num .feedback {
	font: 95%/175% "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;
}

#wpBlockContent$wp_num blockquote {
	border-left: 5px solid #ccc;
	margin-left: 1.5em;
	padding-left: 5px;
}

EOD;
/* Don't remove this line */ if (!defined("WP_BLOCK_CSS_READ")) { define("WP_BLOCK_CSS_READ","1");$wp_block_style .= <<<EOD

#wpRecentPost {
	font-size: 90%;
	word-break: break-all;
}
#wpRecentPost #postDate {
	font-weight: bold;
	font-size:110%;
}

#wpRecentPost .new1 {
	font-size: 80%;
	font-weight: bold;
	color: #EE0000;
}

#wpRecentPost .new2 {
	font-size: 80%;
	font-weight: bold;
	color: #00BB00;
}

#wp-calendar {
	empty-cells: show;
	font-size: 14px;
	margin: 0;
	width: 90%;
}

#wp-calendar #next a {
	padding-right: 10px;
	text-align: right;
}

#wp-calendar #prev a {
	padding-left: 10px;
	text-align: left;
}

#wp-calendar a {
	display: block;
	color: #000000;
	text-decoration: none;
}

#wp-calendar a:hover {
	background: #A6C9E6;
	color: #333;
}

#wp-calendar caption {
	font-weight: bold;
	font-size: 110%;
	color: #632;
	text-align: left;
}

#wp-calendar td {
	color: #aaa;
	font: normal 12px "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;
	letter-spacing: normal;
	padding: 2px 0;
	text-align: center;
}

#wp-calendar td.pad:hover {
	background: #fff;
}

#wp-calendar #today {
	background: #D85F7D;
	color: #ffffff;
}

#wp-calendar th {
	font-style: normal;
	font-size: 11px;
	text-transform: capitalize;
}
EOD;
/* Don't remove this line */ }
/* Don't remove this line */ global $wp_id,$wp_filter; if ((@in_array('pukiwiki', $wp_filter[$wp_id]['the_content']["6"])) and
/* Don't remove this line */ (!preg_match("/^".preg_quote(XOOPS_ROOT_PATH."/modules/wordpress".$wp_num."/","/")."/i",$cur_PATH))){/* Don't remove this line */ if (!defined("WP_BLOCK_WIKI_READ")) {
/* Don't remove this line */ define("WP_BLOCK_WIKI_READ","1");$wp_block_style .= <<<EOD
/*
 * modPukiWikiÍÑ¤Î¥¹¥¿¥¤¥ë¥·¡¼¥È¥µ¥ó¥×¥ë
 */
div.modPukiWP_ie5 {
	text-align:left;
}

.modPukiWP_style_table
{
	padding:0px;
	border:0px;
	margin:2px 0px 2px 0px;
	text-align:left;
	color:inherit;
	width:auto;
	background-color:#ccd5dd;
}

.modPukiWP_style_th
{
	padding:5px;
	margin:1px;
	text-align:center;
	color:inherit;
	background-color:#EEEEEE;
}

thead th.modPukiWP_style_th,
tfoot th.modPukiWP_style_th
{
	color:inherit;
	background-color:#E0E8F0;
}

.modPukiWP_style_td
{
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
}

thead td.modPukiWP_style_td,
tfoot td.modPukiWP_style_td
{
	color:inherit;
	background-color:#D0D8E0;
}
.modPukiWP_head {
	font-family: "¥Ò¥é¥®¥Î³Ñ¥´ Pro W3", Osaka, Verdana, "£Í£Ó £Ð¥´¥·¥Ã¥¯", sans-serif;
	margin-left: 0px;
	border-left: 10px solid #dcdcdc;
	border-bottom: 1px solid #dcdcdc;
	background-color:#FEFEFE;
	padding-left: 0.3em;
	margin-bottom: 5px;
}
h1.modPukiWP_head {
	font-size : 20pt;
}
h2.modPukiWP_head {
	font-size : 16pt;
}
h3.modPukiWP_head {
	font-size : 12pt;
}
h4.modPukiWP_head {
	font-size : 11pt;
}
h5.modPukiWP_head {
	font-size : 10pt;
	background-color: #DDEEFF;
	padding: 0px;
}

h6.modPukiWP_head {
	font-size : 9pt;
	background-color: #DDEEFF;
	padding: 0px;
}

ul.modPukiWP_list1 li
{
	list-style-type:disc;
}
ul.modPukiWP_list2 li
{
	list-style-type:circle;
}
ul.modPukiWP_list3 li
{
	list-style-type:square;
}
ol.modPukiWP_list1 li
{
	list-style-type:decimal;
}
ol.modPukiWP_list2 li
{
	list-style-type:lower-roman;
}
ol.modPukiWP_list3 li
{
	list-style-type:lower-alpha;
}

pre.modPukiWP_pre {
	border-top:    #DDDDEE 1px solid;
	border-bottom: #888899 1px solid;
	border-left:   #DDDDEE 1px solid;
	border-right:  #888899 1px solid;
	font-size:12px;
	line-height:120%;
	padding:0.5em 10px 0.5em 10px;
	margin: 5px 10px 5px 20px;
	white-space: pre;
	width:95ex;
	height:auto;
	max-width:95ex;
	background-color: #F0F8FF;
	color: black;
	overflow:auto; /* Moziila/OperaÂÐºö */
	white-space: pre;
}

span.modPukiWP_noexists
{
	color:inherit;
	background-color:#FFFACC;
}

.modPukiWP_small
{
	font-size:80%;
}


a.modPukiWP_note_super
{
	color:#DD3333;
	background-color:inherit;
	font-weight:bold;
	font-size:60%;
	vertical-align:super;
}

hr.modPukiWP_full_hr
{
	border-style:ridge;
	border-color:#333333;
	border-width:1px 0px;
}
hr.modPukiWP_note_hr
{
	width:90%;
	border-style:ridge;
	border-color:#333333;
	border-width:1px 0px;
	text-align:center;
	margin:1em auto 0em auto;
}

span.modPukiWP_size1
{
	font-size:xx-small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size2
{
	font-size:x-small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size3
{
	font-size:small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size4
{
	font-size:medium;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size5
{
	font-size:large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size6
{
	font-size:x-large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.modPukiWP_size7
{
	font-size:xx-large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
.modPukiWP_anchor_super
{
	font-size:xx-small;
	vertical-align:super;
}
.modPukiWP_clear{
	margin:0px;
	clear:both;
}
div.modPukiWP_img_margin
{
	margin-left:32px;
	margin-right:32px;
}
EOD;
/* Don't remove this line */ }
/* Don't remove this line */ }
?>
