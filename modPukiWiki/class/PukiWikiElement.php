<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
// modPukiWiki HTML生成用クラス群
//
// 修正元ファイル：PukiWiki 1.4のconvert_html.php
//
class PukiWikiElement
{ // ブロック要素
	var $parent;   // 親要素
	var $last;     // 次に要素を挿入する先
	var $elements; // 要素の配列
	
	function PukiWikiElement()
	{
		$this->elements = array();
		$this->last = &$this;
	}

	function setParent(&$parent)
	{
		$this->parent = &$parent;
	}

	function &add(&$obj)
	{
		if ($this->canContain($obj))
		{
			return $this->insert($obj);
		}
		
		return $this->parent->add($obj);
	}

	function &insert(&$obj)
	{
//		nl2br(var_dump(debug_backtrace()));
		$obj->setParent($this);
		$this->elements[] = &$obj;
		
		return $this->last = &$obj->last;
	}
	function canContain($obj)
	{
		return TRUE;
	}

	function wrap($string, $tag, $param = '', $canomit = TRUE)
	{
		return ($canomit and $string == '') ? '' : "<$tag$param>$string</$tag>";
	}

	function toString()
	{
		$ret = array();
		foreach (array_keys($this->elements) as $key)
		{
			$ret[] = $this->elements[$key]->toString();
		}
		
		return join("\n",$ret);
	}

	function dump($indent = 0)
	{
		$ret = str_repeat(' ', $indent).get_class($this)."\n";
		
		$indent += 2;
		
		foreach (array_keys($this->elements) as $key)
		{
			$ret .= is_object($this->elements[$key]) ?
				$this->elements[$key]->dump($indent) : '';
				//str_repeat(' ',$indent).$this->elements[$key];
		}
		
		return $ret;
	}
}

class PukiWikiInline extends PukiWikiElement
{ // インライン要素

	function PukiWikiInline($text)
	{
		parent::PukiWikiElement();
		
		if (substr($text,0,1) == '~') // 行頭~。パラグラフ開始
		{
			$this = new PukiWikiParagraph(' '.substr($text,1));
			$this->last = &$this;
			
			return;
		}
		$this->elements[] = trim((substr($text, 0, 1) == "\n") ? $text : PukiWikiFunc::make_link($text));
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		
		return $this;
	}

	function canContain($obj)
	{
		return is_a($obj,'PukiWikiInline');
	}

	function toString()
	{
		return join(PukiWikiConfig::getParam('line_break') ? "<br />\n" : "\n",$this->elements);
	}

	function &toPara($class = '')
	{
		$obj = &new PukiWikiParagraph('', $class);
		$obj->insert($this);
		return $obj;
	}
}

class PukiWikiParagraph extends PukiWikiElement
{ // 段落
	var $param;
	
	function PukiWikiParagraph($text, $param = '')
	{
		parent::PukiWikiElement();
		
		$this->param = $param;
		if ($text == '')
		{
			return;
		}
		if (substr($text,0,1) == '~')
		{
			$text = ' '.substr($text, 1);
		}
		$this->insert(new PukiWikiInline($text));
	}

	function canContain($obj)
	{
		return is_a($obj,'PukiWikiInline');
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}

class PukiWikiHeading extends PukiWikiElement
{ // *
	var $level;
	var $id;
	var $msg_top;
	
	function PukiWikiHeading(&$root, $text)
	{
		parent::PukiWikiElement();
		
		$this->level = min(3, strspn($text, '*'));
		list($text, $this->msg_top, $this->id) = $root->getAnchor($text, $this->level);
		$this->insert(new PukiWikiInline($text));
		$this->level++; // h2,h3,h4
	}

	function &insert(&$obj)
	{
		parent::insert($obj);
		return $this->last = &$this;
	}

	function canContain(&$obj)
	{
		return FALSE;
	}

	function toString()
	{
		return $this->msg_top.$this->wrap(parent::toString(), 'h'.$this->level, " id=\"{$this->id}\" class=\"".PukiWikiConfig::getParam('style_prefix')."head\"");
	}
}

class PukiWikiHRule extends PukiWikiElement
{ // ----

	function PukiWikiHRule(&$root, $text)
	{
		parent::PukiWikiElement();
	}

	function canContain(&$obj)
	{
		return FALSE;
	}

	function toString()
	{
		return PukiWikiConfig::getParam('hr');
	}
}

class PukiWikiListContainer extends PukiWikiElement
{
	var $tag;
	var $tag2;
	var $level;
	var $style;
	var $margin;
	var $left_margin;
	
	function PukiWikiListContainer( $tag, $tag2, $head, $text)
	{
		parent::PukiWikiElement();
		
		//マージンを取得
		$var_margin = "_{$tag}_margin";
		$var_left_margin = "_{$tag}_left_margin";
		$this->margin = PukiWikiConfig::getParam($var_margin);
		$this->left_margin = PukiWikiConfig::getParam($var_left_margin);
		
		//初期化
		$this->tag = $tag;
		$this->tag2 = $tag2;
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));
		
		parent::insert(new PukiWikiListElement($this->level, $tag2));
		if ($text != '')
		{
			$this->last = &$this->last->insert(new PukiWikiInline($text));
		}
	}
	
	function canContain(&$obj)
	{
		return (!is_a($obj, 'PukiWikiListContainer')
			or ($this->tag == $obj->tag and $this->level == $obj->level));
	}

	function setParent(&$parent)
	{
		parent::setParent($parent);
		
		$step = $this->level;
		if (isset($parent->parent) and is_a($parent->parent, 'PukiWikiListContainer'))
		{
			$step -= $parent->parent->level;
		}
		$margin = $this->margin * $step;
		if ($step == $this->level)
		{
			$margin += $this->left_margin;
		}
		$this->style = sprintf(PukiWikiConfig::getParam('_list_pad_str'), $this->level, $margin, $margin);
	}

	function &insert(&$obj)
	{
		if (!is_a($obj, get_class($this)))
		{
			return $this->last = &$this->last->insert($obj);
		}
        // 行頭文字のみの指定時はUL/OLブロックを脱出
        // BugTrack/524 
		if (count($obj->elements) == 1 && count($obj->elements[0]->elements) == 0)
		{
			return $this->last->parent; // up to PukiWikiListElement.
		}
		// move elements.
		foreach(array_keys($obj->elements) as $key)
		{
			parent::insert($obj->elements[$key]);
		}
		
		return $this->last;
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->tag, $this->style);
	}
}

class PukiWikiListElement extends PukiWikiElement
{
	function PukiWikiListElement($level, $head)
	{
		parent::PukiWikiElement();
		$this->level = $level;
		$this->head = $head;
	}

	function canContain(&$obj)
	{
		return (!is_a($obj, 'PukiWikiListContainer') or ($obj->level > $this->level));
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->head);
	}
}

class PukiWikiUList extends PukiWikiListContainer
{ // -
	function PukiWikiUList( &$root, $text)
	{
		parent::PukiWikiListContainer('ul', 'li', '-', $text);
	}
}

class PukiWikiOList extends PukiWikiListContainer
{ // +
	function PukiWikiOList( &$root, $text)
	{
		parent::PukiWikiListContainer('ol', 'li', '+', $text);
	}
}

class PukiWikiDList extends PukiWikiListContainer
{ // :
	function PukiWikiDList( &$root, $text)
	{
		$out = explode('|', $text, 2);
		if (count($out) < 2)
		{
			$this = new PukiWikiInline($text);
			$this->last = &$this;
			
			return;
		}
		parent::PukiWikiListContainer('dl', 'dt', ':', $out[0]);
		
		$this->last = &PukiWikiElement::insert(new PukiWikiListElement($this->level, 'dd'));
		if ($out[1] != '')
		{
			$this->last = &$this->last->insert(new PukiWikiInline($out[1]));
		}
	}
}

class PukiWikiBQuote extends PukiWikiElement
{ // >
	var $level;
	
	function PukiWikiBQuote( &$root, $text)
	{
		parent::PukiWikiElement();
		
		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));
		
		if ($head == '<') //blockquote close
		{
			$level = $this->level;
			$this->level = 0;
			$this->last = &$this->end($root, $level);
			if ($text != '')
			{
				$this->last = &$this->last->insert(new PukiWikiInline($text));
			}
		}
		else
		{
			$this->insert(new PukiWikiInline($text));
		}
	}

	function canContain(&$obj)
	{
		return (!is_a($obj, get_class($this)) or $obj->level >= $this->level);
	}

	function &insert(&$obj)
	{
        // BugTrack/521, BugTrack/545
		if (is_a($obj, 'inline')) {
        	return parent::insert($obj->toPara(' class="'.PukiWikiConfig::getParam('style_prefix').'quotation"'));
        }
		if (is_a($obj, 'PukiWikiBQuote') and $obj->level == $this->level and count($obj->elements))
		{
			$obj = &$obj->elements[0];
			if (is_a($this->last,'PukiWikiParagraph') and count($obj->elements))
			{
				$obj = &$obj->elements[0];
			}
		}
		return parent::insert($obj);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'blockquote');
	}

	function &end(&$root, $level)
	{
		$parent = &$root->last;
		
		while (is_object($parent))
		{
			if (is_a($parent,'PukiWikiBQuote') and $parent->level == $level)
			{
				return $parent->parent;
			}
			$parent = &$parent->parent;
		}
		return $this;
	}
}

class PukiWikiTableCell extends PukiWikiElement
{
	var $tag = 'td'; // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style; // is array('width'=>, 'align'=>...);
	
	function PukiWikiTableCell( $text, $is_template = FALSE)
	{
		parent::PukiWikiElement();
		$this->style = $matches = array();
	
		while (preg_match('/^(?:(LEFT|CENTER|RIGHT)|(BG)?COLOR\(([#\w]+)\)|SIZE\((\d+)\)):(.*)$/',$text,$matches))
		{
			if ($matches[1])
			{
				$this->style['align'] = 'text-align:'.strtolower($matches[1]).';';
				$text = $matches[5];
			}
			else if ($matches[3])
			{
				$name = $matches[2] ? 'background-color' : 'color';
				$this->style[$name] = $name.':'.htmlspecialchars($matches[3]).';';
				$text = $matches[5];
			}
			else if ($matches[4])
			{
				$this->style['size'] = 'font-size:'.htmlspecialchars($matches[4]).'px;';
				$text = $matches[5];
			}
		}
		if ($is_template and is_numeric($text))
		{
			$this->style['width'] = "width:{$text}px;";
		}
		if ($text == '>')
		{
			$this->colspan = 0;
		}
		else if ($text == '~')
		{
			$this->rowspan = 0;
		}
		else if (substr($text, 0, 1) == '~')
		{
			$this->tag = 'th';
			$text = substr($text, 1);
		}
		if ($text != '' and $text{0} == '#')
		{
			// セル内容が'#'で始まるときはPukiWikiDivクラスを通してみる
			$obj = &new PukiWikiDiv($this, $text);
			if (is_a($obj, 'PukiWikiParagraph'))
			{
				$obj = &$obj->elements[0];
			}
		}
		else
		{
			$obj = &new PukiWikiInline($text);
		}
		$this->insert($obj);
	}

	function setStyle(&$style)
	{
		foreach ($style as $key=>$value)
		{
			if (!array_key_exists($key, $this->style))
			{
				$this->style[$key] = $value;
			}
		}
	}

	function toString()
	{
		if ($this->rowspan == 0 or $this->colspan == 0)
		{
			return '';
		}
		$param = " class=\"".PukiWikiConfig::getParam('style_prefix')."style_{$this->tag}\"";
		if ($this->rowspan > 1)
		{
			$param .= " rowspan=\"{$this->rowspan}\"";
		}
		if ($this->colspan > 1)
		{
			$param .= " colspan=\"{$this->colspan}\"";
			unset($this->style['width']);
		}
		if (count($this->style))
		{
			$param .= ' style="'.join(' ', $this->style).'"';
		}
		
		return $this->wrap(parent::toString(), $this->tag, $param, FALSE);
	}
}

class PukiWikiTable extends PukiWikiElement
{ // |
	var $type;
	var $types;
	var $col; // number of column
	
	function PukiWikiTable( &$root, $text)
	{
		parent::PukiWikiElement();
		
		$out = array();
		if (!preg_match("/^\|(.+)\|([hHfFcC]?)$/", $text, $out))
		{
			$this = new PukiWikiInline($text);
			$this->last = &$this;
			
			return;
		}
		$cells = explode('|', $out[1]);
		$this->col = count($cells);
		$this->type = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = ($this->type == 'c');
		$row = array();
		foreach ($cells as $cell)
		{
			$row[] = &new PukiWikiTableCell($cell, $is_template);
		}
		$this->elements[] = $row;
	}

	function canContain(&$obj)
	{
		return is_a($obj, 'PukiWikiTable') and ($obj->col == $this->col);
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		$this->types[] = $obj->type;
		
		return $this;
	}

	function toString()
	{
		static $parts = array('h'=>'thead', 'f'=>'tfoot', ''=>'tbody');
		
		// rowspanを設定(下から上へ)
		for ($ncol = 0; $ncol < $this->col; $ncol++)
		{
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow)
			{
				$row = &$this->elements[$nrow];
				if ($row[$ncol]->rowspan == 0)
				{
					$rowspan++;
					continue;
				}
				$row[$ncol]->rowspan = $rowspan;
				while (--$rowspan) // 行種別を継承する
				{
					$this->types[$nrow + $rowspan] = $this->types[$nrow];
				}
				$rowspan = 1;
			}
		}
		// colspan,styleを設定
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow)
		{
			$row = &$this->elements[$nrow];
			if ($this->types[$nrow] == 'c')
			{
				$stylerow = &$row;
			}
			$colspan = 1;
			foreach (array_keys($row) as $ncol)
			{
				if ($row[$ncol]->colspan == 0)
				{
					$colspan++;
					continue;
				}
				$row[$ncol]->colspan = $colspan;
				if ($stylerow !== NULL)
				{
					$row[$ncol]->setStyle($stylerow[$ncol]->style);
					while (--$colspan) // 列スタイルを継承する
					{
						$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
					}
				}
				$colspan = 1;
			}
		}
		// テキスト化
		$string = '';
		foreach ($parts as $type => $part)
		{
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow)
			{
				if ($this->types[$nrow] != $type)
				{
					continue;
				}
				$row = &$this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol)
				{
					$row_string .= $row[$ncol]->toString();
				}
				$part_string .= $this->wrap($row_string, 'tr');
			}
			$string .= $this->wrap($part_string, $part);
		}
		$string = $this->wrap($string, 'table', ' class="'.PukiWikiConfig::getParam('style_prefix').'style_table" cellspacing="1" border="0"');
		return $this->wrap($string, 'div', ' class="'.PukiWikiConfig::getParam('style_prefix').'ie5"');
	}
}

class PukiWikiYTable extends PukiWikiElement
{ // ,
	var $col;
	
	function PukiWikiYTable( &$root, $text)
	{
		parent::PukiWikiElement();
		
		$_value = csv_explode(',', substr($text,1));
		if (count($_value) == 0)
		{
			$this = new PukiWikiInline($text);
			$this->last = &$this;
			
			return;
		}
		$align = $value = $matches = array();
		foreach($_value as $val)
		{
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/', $val, $matches))
			{
				$align[] =($matches[1] != '') ?
					((array_key_exists(3,$matches) and $matches[3] != '') ?
						' style="text-align:center"' : ' style="text-align:right"'
					) : '';
				$value[] = $matches[2];
			}
			else
			{
				$align[] = '';
				$value[] = $val;
			}
		}
		$this->col = count($value);
		$colspan = array();
		foreach ($value as $val)
		{
			$colspan[] = ($val == '==') ? 0 : 1;
		}
		$str = '';
		for ($i = 0; $i < count($value); $i++)
		{
			if ($colspan[$i])
			{
				while ($i + $colspan[$i] < count($value) and $value[$i + $colspan[$i]] == '==')
				{
					$colspan[$i]++;
				}
				$colspan[$i] = ($colspan[$i] > 1) ? " colspan=\"{$colspan[$i]}\"" : '';
				$str .= "<td class=\"".PukiWikiConfig::getParam('style_prefix')."style_td\"{$align[$i]}{$colspan[$i]}>".PukiWikiFunc::make_link($value[$i]).'</td>';
			}
		}
		$this->elements[] = $str;
	}

	function canContain(&$obj)
	{
		return is_a($obj, 'PukiWikiYTable') and ($obj->col == $this->col);
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		
		return $this;
	}

	function toString()
	{
		$rows = '';
		foreach ($this->elements as $str)
		{
			$rows .= "\n<tr class=\"".PukiWikiConfig::getParam('style_prefix')."style_tr\">$str</tr>\n";
		}
		$rows = $this->wrap($rows, 'table', ' class="'.PukiWikiConfig::getParam('style_prefix').'style_table" cellspacing="1" border="0"');
		return $this->wrap($rows, 'div', ' class="'.PukiWikiConfig::getParam('style_prefix').'ie5"');
	}
}

class PukiWikiPre extends PukiWikiElement
{ // ' '
	function PukiWikiPre( &$root,$text)
	{
		parent::PukiWikiElement();
		$this->elements[] = htmlspecialchars(
			(!PukiWikiConfig::getParam('preformat_ltrim') or $text == '' or $text{0} != ' ') ? $text : substr($text, 1)
		);
	}

	function canContain(&$obj)
	{
		return is_a($obj, 'PukiWikiPre');
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		
		return $this;
	}

	function toString()
	{
		return $this->wrap(join("\n", $this->elements), 'pre' ,' class="'.PukiWikiConfig::getParam('style_prefix').'pre"');
	}
}

class PukiWikiDiv extends PukiWikiElement
{ // #
	var $name;
	var $param;
	
	function PukiWikiDiv( &$root, $text)
	{
		parent::PukiWikiElement();
		
		if (!preg_match("/^\#([^\(]+)(?:\((.*)\))?/", $text, $out) or !PukiWikiPlugin::exist_plugin_convert($out[1]))
		{
			$this = new PukiWikiParagraph($text);
			$this->last = &$this;
			
			return;
		}
		list(, $this->name, $this->param) = array_pad($out,3,'');
	}

	function canContain(&$obj)
	{
		return FALSE;
	}

	function toString()
	{
		return PukiWikiPlugin::do_plugin_convert($this->name,$this->param);
	}
}

class PukiWikiAlign extends PukiWikiElement
{ // LEFT:/CENTER:/RIGHT:
	var $align;
	
	function PukiWikiAlign( $align)
	{
		parent::PukiWikiElement();
		
		$this->align = $align;
	}

	function canContain(&$obj)
	{
		return is_a($obj, 'PukiWikiInline');
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'div', ' style="text-align:'.$this->align.'"');
	}
}

class PukiWikiBody extends PukiWikiElement
{ // PukiWikiBody
	var $id;
	var $count = 0;
	var $contents;
	var $contents_last;
	var $classes = array(
		'-' => 'PukiWikiUList',
		'+' => 'PukiWikiOList',
		':' => 'PukiWikiDList',
		'|' => 'PukiWikiTable',
		',' => 'PukiWikiYTable',
		'>' => 'PukiWikiBQuote',
		'<' => 'PukiWikiBQuote',
		'#' => 'PukiWikiDiv'
	);
	
	function PukiWikiBody($id)
	{
		$this->id = $id;
		$this->contents = &new PukiWikiElement();
		$this->contents_last = &$this->contents;
		parent::PukiWikiElement();
	}

	function parse(&$lines)
	{
		$this->last = &$this;
		
		while (count($lines))
		{
			$line = array_shift($lines);
			
			if (substr($line,0,2) == '//') //コメントは処理しない
			{
				continue;
			}
			
			if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/',$line,$matches))
			{
				$this->last = &$this->last->add(new PukiWikiAlign(strtolower($matches[1]))); // <div style="text-align:...">
				if ($matches[2] == '')
				{
					continue;
				}
				$line = $matches[2];
			}
			
			$line = preg_replace("/[\r\n]*$/",'',$line);
			
			// Empty
			if ($line == '')
			{
				$this->last = &$this;
				continue;
			}
			// Horizontal Rule
			if (substr($line,0,4) == '----')
			{
				$this->insert(new PukiWikiHRule($this,$line));
				continue;
			}
			// 行頭文字
			$head = $line{0};
			
			// PukiWikiHeading
			if ($head == '*')
			{
				$this->insert(new PukiWikiHeading($this,$line));
				continue;
			}
			// PukiWikiPre
			if ($head == ' ' or $head == "\t")
			{
				$this->last = &$this->last->add(new PukiWikiPre($this,$line));
				continue;
			}
			// Line Break
			if (substr($line,-1) == '~')
			{
				$line = substr($line,0,-1)."\r";
			}
			// Other Character
			if (array_key_exists($head, $this->classes))
			{
				$classname = $this->classes[$head];
				$this->last = &$this->last->add(new $classname($this,$line));
				continue;
			}
			
			// Default
			$this->last = &$this->last->add(new PukiWikiInline($line));
		}
	}

	function getAnchor($text,$level)
	{
		$anchor = (($id = PukiWikiFunc::make_heading($text,FALSE)) == '') ?
			'' : " &aname($id,super,full){".PukiWikiConfig::getParam('_symbol_anchor')."};";
		$text = ' '.$text;
		$id = "content_{$this->id}_{$this->count}";
		$this->count++;
		$this->contents_last = &$this->contents_last->add(new Contents_UList($text,$level,$id));
		
		return array($text. $anchor, $this->count > 1 ? "\n".PukiWikiConfig::getParam('top') : '', $id);
	}

	function &insert(&$obj)
	{
		if (is_a($obj, 'PukiWikiInline'))
		{
			$obj = &$obj->toPara();
		}
		return parent::insert($obj);
	}

	function toString()
	{
		$text = parent::toString();
		
		// #contents
		$text = preg_replace_callback('/(<p[^>]*>)<del>#contents<\/del>(\s*)(<\/p>)/', array(&$this,'replace_contents'),$text);
		
		return "$text\n";
	}

	function replace_contents($arr)
	{
		$contents  = "<div class=\"".PukiWikiConfig::getParam('style_prefix')."contents\">\n";
		$contents .= "<a id=\"contents_{$this->id}\"></a>";
		$contents .= $this->contents->toString();
		$contents .= "</div>\n";
		array_shift($arr);
		
		return ($arr[1] != '') ? $contents.join('',$arr) : $contents;
	}

}

class Contents_UList extends PukiWikiListContainer
{
	function Contents_UList( $text,$level,$id)
	{
		// テキストのリフォーム
		// 行頭\nで整形済みを表す ... X(
		PukiWikiFunc::make_heading($text);
		$text = "\n<a href=\"#$id\">$text</a>\n";
		parent::PukiWikiListContainer('ul', 'li', '-', str_repeat('-',$level));
		$this->insert(new PukiWikiInline($text));
	}

	function setParent(&$parent)
	{
		parent::setParent($parent);
		$step = $this->level;
		$margin = $this->left_margin;
		if (isset($parent->parent) and is_a($parent->parent,'PukiWikiListContainer'))
		{
			$step -= $parent->parent->level;
			$margin = 0;
		}
		$margin += $this->margin * ($step == $this->level ? 1 : $step);
		$this->style = sprintf(PukiWikiConfig::getParam('_list_pad_str'), $this->level, $margin,$margin);
	}
}
?>
