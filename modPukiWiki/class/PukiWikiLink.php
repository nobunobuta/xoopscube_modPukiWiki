<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki��������ѥ��饹��
//
// �������ե����롧PukiWiki 1.4��make_link.php
//

//����饤�����Ǥ��ִ�����
class PukiWikiInlineConverter
{
	var $converters; // as array()
	var $pattern;
	var $pos;
	var $result;

	function PukiWikiInlineConverter($converters=NULL,$excludes=NULL)
	{
		
		if ($converters === NULL)
		{
			if (PukiWikiConfig::GetParam('autourllink')) {
				$converters = array(
					'plugin',        // ����饤��ץ饰����
					'note',          // ���
					'url',           // URL
					'url_interwiki', // URL (interwiki definition)
					'mailto',        // mailto:
					'interwikiname', // InterWikiName
					'autolink',      // AutoLink
					'bracketname',   // BracketName
					'wikiname',      // WikiName
					'autolink_a',    // AutoLink(����ե��٥å�)
				);
			} else {
				$converters = array(
					'plugin',        // ����饤��ץ饰����
					'note',          // ���
					'url_interwiki', // URL (interwiki definition)
					'mailto',        // mailto:
					'interwikiname', // InterWikiName
					'autolink',      // AutoLink
					'bracketname',   // BracketName
					'wikiname',      // WikiName
					'autolink_a',    // AutoLink(����ե��٥å�)
				);
			}
		}
		if ($excludes !== NULL)
		{
			$converters = array_diff($converters,$excludes);
		}
		$this->converters = array();
		$patterns = array();
		$start = 1;
		
		foreach ($converters as $name)
		{
			$classname = "PukiWikiLink_$name";
			$converter = new $classname($start);
			$pattern = $converter->get_pattern();
			if ($pattern === FALSE)
			{
				continue;
			}
			$patterns[] = "(\n$pattern\n)";
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			$start++;
		}
		$this->pattern = join('|',$patterns);
	}
	function convert($string,$page)
	{
		$this->page = $page;
		$this->result = array();
		
		$string = preg_replace_callback("/{$this->pattern}/x",array(&$this,'replace'),$string);
		
		$arr = explode("\x08", PukiWikiConfig::applyRules(htmlspecialchars($string)));
		$retval = '';
		while (count($arr))
		{
			$retval .= array_shift($arr).array_shift($this->result);
		}
		return $retval;
	}
	function replace($arr)
	{
		$obj = $this->get_converter($arr);
		
		$this->result[] = ($obj !== NULL and $obj->set($arr,$this->page) !== FALSE) ?
			$obj->toString() : PukiWikiConfig::applyRules(htmlspecialchars($arr[0]));
		
		return "\x08"; //�����Ѥߤ���ʬ�˥ޡ����������
	}
	function get_objects($string,$page)
	{
		preg_match_all("/{$this->pattern}/x",$string,$matches,PREG_SET_ORDER);
		
		$arr = array();
		foreach ($matches as $match)
		{
			$obj = $this->get_converter($match);
			if ($obj->set($match,$page) !== FALSE)
			{
				$arr[] = $obj; // copy
				if ($obj->body != '')
				{
					$arr = array_merge($arr,$this->get_objects($obj->body,$page));
				}
			}
		}
		return $arr;
	}
	function &get_converter(&$arr)
	{
		foreach (array_keys($this->converters) as $start)
		{
			if ($arr[$start] == $arr[0])
			{
				return $this->converters[$start];
			}
		}
		return NULL;
	}
}
//����饤�����ǽ���Υ١������饹
class PukiWikiLink
{
	var $start;   // ��̤���Ƭ�ֹ�(0���ꥸ��)
	var $text;    // �ޥå�����ʸ��������

	var $type;
	var $page;
	var $name;
	var $body;
	var $alias;

	// constructor
	function PukiWikiLink($start)
	{
		$this->start = $start;
	}
	// �ޥå��˻��Ѥ���ѥ�������֤�
	function get_pattern()
	{
	}
	// ���Ѥ��Ƥ����̤ο����֤� ((?:...)�����)
	function get_count()
	{
	}
	// �ޥå������ѥ���������ꤹ��
	function set($arr,$page)
	{
	}
	// ʸ������Ѵ�����
	function toString()
	{
	}
	
	//private
	// �ޥå��������󤫤顢��ʬ��ɬ�פ���ʬ����������o��
	function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr = array_pad(array_splice($arr,$this->start,$count),$count,'');
//		var_dump ($arr);echo "<br>";
		$this->text = $arr[0];
		return $arr;
	}
	// ���ܥѥ�᡼�������ꤹ��
	function setParam($page,$name,$body,$type='',$alias='')
	{
		static $converter = NULL;
		
		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if ($type != 'InterWikiName' and preg_match('/\.(gif|png|jpe?g)$/i',$alias))
		{
			$alias = htmlspecialchars($alias);
			$alias = "<img src=\"$alias\" alt=\"$name\" />";
		}
		else if ($alias != '')
		{
			if ($converter === NULL)
			{
				$converter = new PukiWikiInlineConverter(array('plugin'));
			}
			$alias = PukiWikiConfig::applyRules($converter->convert($alias,$page));
		}
		$this->alias = $alias;
		
		return TRUE;
	}
	// �ڡ���̾�Υ�󥯤����
	function make_pagelink($page, $alias='',$anchor='',$refer='')
	{
		$s_page = htmlspecialchars(PukiWikiFunc::strip_bracket($page));
		$s_alias = ($alias == '') ? $s_page : $alias;
		
		if ($page == '') {
			return "<a href=\"$anchor\">$s_alias</a>";
		}
		
		$r_page = rawurlencode($page);
		$r_refer = ($refer == '') ? '' : '&amp;refer='.rawurlencode($refer);
		

		if (PukiWikiFunc::is_page($page)) {
			$passage = "";
			$title = PukiWikiConfig::getParam('link_compact') ? '' : " title=\"$s_page$passage\"";
			return "<a href=\"".MOD_PUKI_WIKI_URL."?$r_page$anchor\"$title>$s_alias</a>";
		} else {
			$retval = "$s_alias<a href=\"".MOD_PUKI_WIKI_URL."?cmd=edit&amp;page=$r_page$r_refer\">".PukiWikiConfig::getParam('_symbol_noexists')."</a>";
			if (!PukiWikiConfig::getParam('link_compact')) {
				$retval = "<span class=\"".PukiWikiConfig::getParam('style_prefix')."noexists\">$retval</span>";
			}
			return $retval;
		}
	}
}
// ����饤��ץ饰����
class PukiWikiLink_plugin extends PukiWikiLink
{
	var $pattern;
	var $plain,$param;
	
	function PukiWikiLink_plugin($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$this->pattern = <<<EOD
&
(      # (1) plain
 (\w+) # (2) plugin name
 (?:
  \(
   ((?:(?!\)[;{]).)*) # (3) parameter
  \)
 )?
)
EOD;
		return <<<EOD
{$this->pattern}
(?:
 \{
  ((?:(?R)|(?!};).)*) # (4) body
 \}
)?
;
EOD;
	}
	function get_count()
	{
		return 4;
	}
	function set($arr,$page)
	{
		list($all,$this->plain,$name,$this->param,$body) = $this->splice($arr);
		
		// ����Υץ饰����̾����ӥѥ�᡼�����󎾤��ʤ��� PHP4.1.2 (?R)�к�
		if (preg_match("/^{$this->pattern}/x",$all,$matches)
			and $matches[1] != $this->plain)
		{
			list(,$this->plain,$name,$this->param) = $matches;
		}
		return parent::setParam($page,$name,$body,'plugin');
	}
	function toString()
	{
		$body = ($this->body == '') ? '' : PukiWikiFunc::make_link($this->body);
		// �ץ饰����ƤӽФ�
		if (PukiWikiPlugin::exist_plugin_inline($this->name))
		{
			$str = PukiWikiPlugin::do_plugin_inline($this->name,$this->param,$body);
			if ($str !== FALSE) //����
			{
				return $str;
			}
		}
		
		// �ץ饰����¸�ߤ��ʤ������Ѵ��ˎ���
		$body = ($body == '') ? ';' : "\{$body};";
		return PukiWikiConfig::applyRules(htmlspecialchars('&'.$this->plain).$body);
	}
}
// url
class PukiWikiLink_url extends PukiWikiLink
{
	function PukiWikiLink_url($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		return <<<EOD
(\[\[             # (1) open bracket
 ((?:(?!\]\]).)+) # (2) alias
 (?:>|:)
)?
(                 # (3) url
 (?:https?|ftp|news):\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+
)
(?($s1)\]\])      # close bracket
EOD;
	}
	function get_count()
	{
		return 3;
	}
	function set($arr,$page)
	{
		list(,,$alias,$name) = $this->splice($arr);
		return parent::setParam($page,htmlspecialchars($name),'','url',$alias == '' ? $name : $alias);
	}
	function toString()
	{
		return "<a href=\"{$this->name}\">{$this->alias}</a>";
	}
}
// url (InterWiki definition type)
class PukiWikiLink_url_interwiki extends PukiWikiLink
{
	function PukiWikiLink_url_interwiki($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		return <<<EOD
\[       # open bracket
(        # (1) url
 (?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*'();\/?:\@&=+\$,%#\w.-]*
)
\s
([^\]]+) # (2) alias
\]       # close bracket
EOD;
	}
	function get_count()
	{
		return 2;
	}
	function set($arr,$page)
	{
		list(,$name,$alias) = $this->splice($arr);
		return parent::setParam($page,htmlspecialchars($name),'','url',$alias);
	}
	function toString()
	{
		return "<a href=\"{$this->name}\">{$this->alias}</a>";
	}
}
//mailto:
class PukiWikiLink_mailto extends PukiWikiLink
{
	var $is_image,$image;
	
	function PukiWikiLink_mailto($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		return <<<EOD
(?:
 \[\[
 ((?:(?!\]\]).)+)(?:>|:)  # (1) alias
)?
([\w.-]+@[\w-]+\.[\w.-]+) # (2) mailto
(?($s1)\]\])              # close bracket if (1)
EOD;
	}
	function get_count()
	{
		return 2;
	}
	function set($arr,$page)
	{
		list(,$alias,$name) = $this->splice($arr);
		return parent::setParam($page,$name,'','mailto',$alias == '' ? $name : $alias);
	}
	function toString()
	{
		return "<a href=\"mailto:{$this->name}\">{$this->alias}</a>";
	}
}
// BracketName
class PukiWikiLink_bracketname extends PukiWikiLink
{
	var $anchor,$refer;
	
	function PukiWikiLink_bracketname($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$WikiName = PukiWikiConfig::getParam('WikiName');
		$BracketName  = PukiWikiConfig::getParam('BracketName');
		
		$s2 = $this->start + 2;
		return <<<EOD
\[\[                     # open bracket
(?:((?:(?!\]\]).)+)>)?   # (1) alias
(\[\[)?                  # (2) open bracket
(                        # (3) PageName
 (?:$WikiName)
 |
 (?:$BracketName)
)?
(\#(?:[a-zA-Z][\w-]*)?)? # (4) anchor
(?($s2)\]\])             # close bracket if (2)
\]\]                     # close bracket
EOD;
	}
	function get_count()
	{
		return 4;
	}
	function set($arr,$page)
	{
		$WikiName = PukiWikiConfig::getParam('WikiName');

		list(,$alias,,$name,$this->anchor) = $this->splice($arr);
		if ($name == '' and $this->anchor == '')
		{
			return FALSE;
		}
		if ($name != '' and preg_match("/^$WikiName$/",$name))
		{
			return parent::setParam($page,$name,'','pagename',$alias);
		}
		if ($alias == '')
		{
			$alias = $name.$this->anchor;
		}
		if ($name == '')
		{
			if ($this->anchor == '')
			{
				return FALSE;
			}
		}
		else
		{
			if (!(PukiWikiFunc::is_pagename($name)))
			{
				return FALSE;
			}
		}
		return parent::setParam($page,$name,'','pagename',$alias);
	}
	function toString()
	{
		return $this->make_pagelink(
			$this->name,
			$this->alias,
			$this->anchor,
			$this->page
		);
	}
}
// WikiName
class PukiWikiLink_wikiname extends PukiWikiLink
{
	function PukiWikiLink_wikiname($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$WikiName = PukiWikiConfig::getParam('WikiName');
		$nowikiname = PukiWikiConfig::getParam('nowikiname');
		
		return $nowikiname ? FALSE : "($WikiName)";
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		list($name) = $this->splice($arr);
		return parent::setParam($page,$name,'','pagename',$name);
	}
	function toString()
	{
		return $this->make_pagelink(
			$this->name,
			$this->alias,
			'',
			$this->page
		);
	}
}
// AutoLink
class PukiWikiLink_autolink extends PukiWikiLink
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only
	
	function PukiWikiLink_autolink($start)
	{
		parent::PukiWikiLink($start);
		
		$autolink = PukiWikiConfig::getParam('autolink');
		if (!$autolink or !file_exists(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat'))
		{
			return;
		}
		@list($auto,$auto_a,$forceignorepages) = file(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat');
		$this->auto = $auto;
		$this->auto_a = $auto_a; 
		$this->forceignorepages = explode("\t",trim($forceignorepages));
	}
	function get_pattern()
	{
		return isset($this->auto) ? "({$this->auto})" : FALSE;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		$WikiName = PukiWikiConfig::getParam('WikiName');
		
		list($name) = $this->splice($arr);
		// ̵��ꥹ�Ȥ˴ޤޤ�Ƥ��롢���뤤��¸�ߤ��ʤ��ڡ�����̤Ƥ�
		if (in_array($name,$this->forceignorepages) or !PukiWikiFunc::is_page($name))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,'','pagename',$name);
	}
	function toString()
	{
		return $this->make_pagelink(
			$this->name,
			$this->alias,
			'',
			$this->page
		);
	}
}
class PukiWikiLink_autolink_a extends PukiWikiLink_autolink
{
	function PukiWikiLink_autolink_a($start)
	{
		parent::PukiWikiLink_autolink($start);
	}
	function get_pattern()
	{
		return isset($this->auto_a) ? "({$this->auto_a})" : FALSE;
	}
}
// ���
class PukiWikiLink_note extends PukiWikiLink
{
	function PukiWikiLink_note($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		return <<<EOD
\(\(
 ((?:(?R)|(?!\)\)).)*) # (1) note body
\)\)
EOD;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		global $_PukiWikiFootExplain;
		static $note_id = 0;
		
		list(,$body) = $this->splice($arr);
		
		$id = ++$note_id;
		$note = PukiWikiFunc::make_link($body);
		$style_small = PukiWikiConfig::getParam('style_prefix')."small";
		$style_super = PukiWikiConfig::getParam('style_prefix')."note_super";
		$_PukiWikiFootExplain[$id] = <<<EOD
<a id="notefoot_$id" href="#notetext_$id" class="$style_super">*$id</a>
<span class="$style_small">$note</span>
<br />
EOD;
		$name = "<a id=\"notetext_$id\" href=\"#notefoot_$id\" class=\"".PukiWikiConfig::getParam('style_prefix')."note_super\">*$id</a>";
		
		return parent::setParam($page,$name,$body);
	}
	function toString()
	{
		return $this->name;
	}
}
//InterWikiName
class PukiWikiLink_interwikiname extends PukiWikiLink
{
	var $url = '';
	var $param = '';
	var $anchor = '';
	
	function PukiWikiLink_interwikiname($start)
	{
		parent::PukiWikiLink($start);
	}
	function get_pattern()
	{
		$s2 = $this->start + 2;
		$s5 = $this->start + 5;
		return <<<EOD
\[\[                  # open bracket
(?:
 ((?:(?!\]\]).)+)>    # (1) alias
)?
(\[\[)?               # (2) open bracket
((?:(?!\s|:|\]\]).)+) # (3) InterWiki
(?<! > | >\[\[ )      # not '>' or '>[['
:                     # separator
(                     # (4) param
 (\[\[)?              # (5) open bracket
 (?:(?!>|\]\]).)+
 (?($s5)\]\])         # close bracket if (5)
)
(?($s2)\]\])          # close bracket if (2)
\]\]                  # close bracket
EOD;
	}
	function get_count()
	{
		return 5;
	}
	function set($arr,$page)
	{
		list(,$alias,,$name,$this->param) = $this->splice($arr);
		if (preg_match('/^([^#]+)(#[A-Za-z][\w-]*)$/',$this->param,$matches))
		{
			list(,$this->param,$this->anchor) = $matches;
		}
		$url = $this->get_interwiki_url($name,$this->param);
		$this->url = ($url === FALSE) ?
			MOD_PUKI_WIKI_URL.'?'.rawurlencode('[['.$name.':'.$this->param.']]') :
			htmlspecialchars($url);
		
		return parent::setParam(
			$page,
			htmlspecialchars($name.':'.$this->param),
			'',
			'InterWikiName',
			$alias == '' ? $name.':'.$this->param : $alias
		);
	}
	function toString()
	{
		return "<a href=\"{$this->url}{$this->anchor}\" title=\"{$this->name}\">{$this->alias}</a>";
	}

	function get_interwiki_url($name,$param)
	{
		static $interwikinames;
		static $encode_aliases = array('sjis'=>'SJIS','euc'=>'EUC-JP','utf8'=>'UTF-8');
		
		$WikiName = PukiWikiConfig::getParam('WikiName');
		
		if (!isset($interwikinames))
		{
			$interwikinames = array();
			foreach (PukiWikiConfig::getInteWikiArray() as $line)
			{
				if (preg_match('/\[((?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*\'();\/?:\@&=+\$,%#\w.-]*)\s([^\]]+)\]\s?([^\s]*)/',$line,$matches))
				{
					$interwikinames[$matches[2]] = array($matches[1],$matches[3]);
				}
			}
		}
		if (!array_key_exists($name,$interwikinames))
		{
			return FALSE;
		}
		list($url,$opt) = $interwikinames[$name];
		
		// ʸ�����󥳡��ǥ���
		switch ($opt)
		{
			// YukiWiki��
			case 'yw':
				if (!preg_match("/$WikiName/",$param))
				{
					$param = '[['.mb_convert_encoding($param,'SJIS',MOD_PUKI_SOURCE_ENCODING).']]';
				}
	//			$param = htmlspecialchars($param);
				break;
			
			// moin��
			case 'moin':
				$param = str_replace('%','_',rawurlencode($param));
				break;
			
			// ����ʸ�����󥳡��ǥ��󥰤Τޤ�URL���󥳡���
			case '':
			case 'std':
				$param = rawurlencode($param);
				break;
			
			// URL���󥳡��ɤ��ʤ�
			case 'asis':
			case 'raw':
	//			$param = htmlspecialchars($param);
				break;
			
			default:
				// �����ꥢ�����Ѵ�
				if (array_key_exists($opt,$encode_aliases))
				{
					$opt = $encode_aliases[$opt];
				}
				// ���ꤵ�줿ʸ�������ɤإ��󥳡��ɤ���URL���󥳡���
				$param = rawurlencode(mb_convert_encoding($param,$opt,'auto'));
		}
		
		// �ѥ�᡼�����ִ�
		if (strpos($url,'$1') !== FALSE)
		{
			$url = str_replace('$1',$param,$url);
		}
		else
		{
			$url .= $param;
		}
		
		return $url;
	}
}

?>
