<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id$
//
// modPukiWiki HTML生成用クラス群
//
// 修正元ファイル：PukiWiki 1.4のconvert_html.php
// ORG: convert_html.php,v 1.3 2004/10/07 14:15:27 henoheno Exp $
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
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		} else {
			return $this->parent->add($obj);
		}
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
		return ($canomit && $string == '') ? '' : "<$tag$param>$string</$tag>";
	}

	function toString()
	{
		$ret = array();
		foreach (array_keys($this->elements) as $key) {
			$ret[] = $this->elements[$key]->toString();
		}
		
		return join("\n",$ret);
	}

	function dump($indent = 0)
	{
		$ret = str_repeat(' ', $indent).get_class($this)."\n";
		
		$indent += 2;
		
		foreach (array_keys($this->elements) as $key) {
			$ret .= is_object($this->elements[$key]) ?
				$this->elements[$key]->dump($indent) : '';
				//str_repeat(' ',$indent).$this->elements[$key];
		}
		
		return $ret;
	}
}
function & Factory_PukiWikiInline($text)
{
	if (substr($text, 0, 1) == '~') {
		// 行頭 '~' 。パラグラフ開始
		return new PukiWikiParagraph(' ' . substr($text, 1));
	} else {
		return new PukiWikiInline($text);
	}
}

function & Factory_PukiWikiDList(& $root, $text)
{
	$out = explode('|', ltrim($text), 2);
	if (count($out) < 2) {
		return Factory_PukiWikiInline($text);
	} else {
		return new PukiWikiDList($out);
	}
}

function & Factory_PukiWikiTable(& $root, $text)
{
	if (! preg_match("/^\|(.+)\|([hHfFcC]?)$/", $text, $out)) {
		return Factory_PukiWikiInline($text);
	} else {
		return new PukiWikiTable($out);
	}
}

function & Factory_PukiWikiYTable(& $root, $text)
{
	$_value = csv_explode(',', substr($text, 1));
	if (count($_value) == 0) {
		return Factory_PukiWikiInline($text);
	} else {
		return new PukiWikiYTable($_value);
	}
}

function & Factory_PukiWikiDiv(& $root, $text)
{
	if (! preg_match("/^\#([^\(]+)(?:\((.*)\))?/", $text, $out) || ! PukiWikiPlugin::exist_plugin_convert($out[1])) {
		return new PukiWikiParagraph($text);
	} else {
		return new PukiWikiDiv($out);
	}
}

// インライン要素
class PukiWikiInline extends PukiWikiElement
{ 
	function PukiWikiInline($text)
	{
		parent::PukiWikiElement();
		$this->elements[] = trim((substr($text, 0, 1) == "\n") ? $text : PukiWikiFunc::make_link($text));
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function canContain($obj)
	{
		return is_a($obj, 'PukiWikiInline');
	}

	function toString()
	{
		return join(PukiWikiConfig::getParam('line_break') ? "<br />\n" : "\n", $this->elements);
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

		if ($text == '') return;

		if (substr($text,0,1) == '~')
			$text = ' '.substr($text, 1);

		$this->insert(Factory_PukiWikiInline($text));
	}

	function canContain($obj)
	{
		return is_a($obj, 'PukiWikiInline');
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}

// * Heading1
// ** Heading2
// *** Heading3
// **** Heading4
// ***** Heading5
// ****** Heading6
class PukiWikiHeading extends PukiWikiElement
{
	var $level;
	var $id;
	var $msg_top;
	
	function PukiWikiHeading(& $root, $text)
	{
		parent::PukiWikiElement();
		
		$this->level  = min(6, strspn($text, '*'));
		@list($text, $this->msg_top, $this->id )= $root->getAnchor($text, $this->level);
		$this->insert(Factory_PukiWikiInline($text));
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
		return $this->msg_top . $this->wrap(parent::toString(), 'h' . $this->level, " id=\"{$this->id}\" class=\"".PukiWikiConfig::getParam('style_prefix')."head\"");
	}
}

// ----
class PukiWikiHRule extends PukiWikiElement
{

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
			$this->last =& $this->last->insert(Factory_PukiWikiInline($text));
	}
	
	function canContain(&$obj)
	{
		return (! is_a($obj, 'PukiWikiListContainer')
			|| ($this->tag == $obj->tag && $this->level == $obj->level));
	}

	function setParent(&$parent)
	{
		parent::setParent($parent);
		
		$step = $this->level;
		if (isset($parent->parent) && is_a($parent->parent, 'PukiWikiListContainer')) {
			$step -= $parent->parent->level;
		}
		$margin = $this->margin * $step;
		if ($step == $this->level) {
			$margin += $this->left_margin;
		}
		$this->style = sprintf(PukiWikiConfig::getParam('_list_pad_str'), $this->level, $margin, $margin);
	}

	function &insert(&$obj)
	{
		if (!is_a($obj, get_class($this)))
			return $this->last = &$this->last->insert($obj);

        // 行頭文字のみの指定時はUL/OLブロックを脱出
        // BugTrack/524 
		if (count($obj->elements) == 1 && empty($obj->elements[0]->elements))
			return $this->last->parent; // up to PukiWikiListElement.

		// move elements.
		foreach(array_keys($obj->elements) as $key)
			parent::insert($obj->elements[$key]);
		
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
		return (! is_a($obj, 'PukiWikiListContainer') || ($obj->level > $this->level));
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->head);
	}
}

// - One
// - Two
// - Three
class PukiWikiUList extends PukiWikiListContainer
{
	function PukiWikiUList( &$root, $text)
	{
		parent::PukiWikiListContainer('ul', 'li', '-', $text);
	}
}

// + One
// + Two
// + Three
class PukiWikiOList extends PukiWikiListContainer
{
	function PukiWikiOList( &$root, $text)
	{
		parent::PukiWikiListContainer('ol', 'li', '+', $text);
	}
}

// : definition1 | description1
// : definition2 | description2
// : definition3 | description3
class PukiWikiDList extends PukiWikiListContainer
{
	function PukiWikiDList($out)
	{
		parent::PukiWikiListContainer('dl', 'dt', ':', $out[0]);
		
		$this->last = &PukiWikiElement::insert(new PukiWikiListElement($this->level, 'dd'));
		if ($out[1] != '')
		{
			$this->last =& $this->last->insert(Factory_PukiWikiInline($out[1]));
		}
	}
}

// > Someting cited
// > like E-mail text
class PukiWikiBQuote extends PukiWikiElement
{
	var $level;
	
	function PukiWikiBQuote(& $root, $text)
	{
		parent::PukiWikiElement();
		
		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));
		
		if ($head == '<') {//Blockquote close
			$level = $this->level;
			$this->level = 0;
			$this->last = &$this->end($root, $level);
			if ($text != '')
				$this->last = &$this->last->insert(Factory_PukiWikiInline($text));
		} else {
			$this->insert(Factory_PukiWikiInline($text));
		}
	}

	function canContain(&$obj)
	{
		return (! is_a($obj, get_class($this)) || $obj->level >= $this->level);
	}

	function &insert(&$obj)
	{
        // BugTrack/521, BugTrack/545
		if (is_a($obj, 'PukiWikiInline'))
        	return parent::insert($obj->toPara(' class="'.PukiWikiConfig::getParam('style_prefix').'quotation"'));

		if (is_a($obj, 'PukiWikiBQuote') && $obj->level == $this->level && count($obj->elements)) {
			$obj = &$obj->elements[0];
			if (is_a($this->last, 'PukiWikiParagraph') && count($obj->elements))
				$obj = &$obj->elements[0];
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
		
		while (is_object($parent)) {
			if (is_a($parent, 'PukiWikiBQuote') && $parent->level == $level)
				return $parent->parent;
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
	
	function PukiWikiTableCell($text, $is_template = FALSE)
	{
		parent::PukiWikiElement();
		$this->style = $matches = array();
		if (PukiWikiConfig::getParam("ExtTable")) {
			$text = $this->get_cell_style($text);
		}
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
		if ($is_template && is_numeric($text)) {
			$this->style['width'] = "width:{$text}px;";
		}
		
		if ($text == '>') {
			$this->colspan = 0;
		} else if ($text == '~') {
			$this->rowspan = 0;
		} else if (substr($text, 0, 1) == '~') {
			$this->tag = 'th';
			$text = substr($text, 1);
		}

		if ($text != '' && $text{0} == '#') {
			// セル内容が'#'で始まるときはPukiWikiDivクラスを通してみる
			$obj =& Factory_PukiWikiDiv($this, $text);
			if (is_a($obj, 'PukiWikiParagraph')) {
				$obj = &$obj->elements[0];
			}
		} else if (preg_match("/\n/", $text,$match) and PukiWikiConfig::getParam("ExtTable")) {
//			echo $string."<br>\n";
			$string = preg_replace("/(^|\n)(\|[^\r]+?\|)(\n[^|]|$)/e","'$1'.stripslashes(str_replace('->\n','___td_br___','$2')).'$3'",$text);
//			echo $string."<br>\n";
			$lines=array();
			$lines = explode("\n", $string);
			$obj = &new PukiWikiBody(2);
			$obj->parse($lines);
		} else {
			$obj =& Factory_PukiWikiInline($text);
		}

		$this->insert($obj);
	}

	function setStyle(&$style)
	{
		foreach ($style as $key=>$value) {
			if (! isset($this->style[$key]))
				$this->style[$key] = $value;
		}
	}

	function toString()
	{
		if ($this->rowspan == 0 || $this->colspan == 0) return '';

		$param = " class=\"".PukiWikiConfig::getParam('style_prefix')."style_{$this->tag}\"";
		if ($this->rowspan > 1) {
			$param .= " rowspan=\"{$this->rowspan}\"";
		}
		if ($this->colspan > 1) {
			$param .= " colspan=\"{$this->colspan}\"";
			unset($this->style['width']);
		}
		if (! empty($this->style)) {
			$param .= ' style="'.join(' ', $this->style).'"';
		}
		
		return $this->wrap(parent::toString(), $this->tag, $param, FALSE);
	}
	
	function get_cell_style($string) {
		$cells = explode('|',$string,2);
//		echo "CELL: {$cells[0]}\n";
		$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent";
		if (preg_match("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['fcolor'] = "color:".$tmp[1].";";
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,no|,one|,1)?\))/i","FC:$2",$cells[0]);
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$cells[0]);
		}
		// セル規定背景色指定
		if (preg_match("/(?:SC|BC):(#?[0-9abcdef]{6}?|$colors_reg|0)/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['color'] = "background-color:".$tmp[1].";";
			$cells[0] = preg_replace("/(?:SC|BC):(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,no|,one|,1)?\))/i","BC:$2",$cells[0]);
			$cells[0] = preg_replace("/(?:SC|BC):(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$cells[0]);
		}
		// セル規定背景画指定
		if (preg_match("/(?:SC|BC):\(([^),]*)(,once|,1)?\)/i",$cells[0],$tmp)) {
			$tmp[1] = str_replace("http","HTTP",$tmp[1]);
			$this->style['color'] .= "background-image: url(".$tmp[1].");";
			if ($tmp[2]) $this->style['color'] .= "background-repeat: no-repeat;";
			$cells[0] = preg_replace("/(?:SC|BC):\(([^),]*)(,once|,1)?\)/i","",$cells[0]);
		}
		if (preg_match("/K:([0-9]+),?([0-9]*)(one|two|boko|deko|in|out|dash|dott)?/i",$cells[0],$tmp)) {
			if (array_key_exists (3,$tmp)) {
				if (preg_match("/one/i",$tmp[3])) $border_type = "solid";
				else if (preg_match("/two/i",$tmp[3])) $border_type = "double";
				else if (preg_match("/boko/i",$tmp[3])) $border_type = "groove";
				else if (preg_match("/deko/i",$tmp[3])) $border_type = "ridge";
				else if (preg_match("/in/i",$tmp[3])) $border_type = "inset";
				else if (preg_match("/out/i",$tmp[3])) $border_type = "outset";
				else if (preg_match("/dash/i",$tmp[3])) $border_type = "dashed";
				else if (preg_match("/dott/i",$tmp[3])) $border_type = "dotted";
				else $border_type = "outset";
			} else {
				$border_type = "outset";
			}
			//$this->table_style .= " border=\"".$tmp[1]."\"";
			if (array_key_exists (1,$tmp)) {
				if ($tmp[1]==="0"){
					$this->style['border'] = "border:none;";
				} else {
					$this->style['border'] = "border:".$border_type." ".$tmp[1]."px;";
				}
			}
			if (array_key_exists (2,$tmp)) {
				if ($tmp[2]!=""){
					$this->style['padding'] = " padding:".$tmp[2].";";
				} else {
					$this->style['padding'] = " padding:5px;";
				}
			}
			$cells[0] = preg_replace("/K:([0-9]+),?([0-9]*)(one|two|boko|deko|in|out|dash|dott)?/i","",$cells[0]);
		} else {
//			$this->style['border'] = "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['border-color'] = "border-color:".$tmp[1].";";
			$cells[0] = preg_replace("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$cells[0]);
		}
//		echo "CELL: {$cells[0]}\n";
		// セル規定文字揃え、幅指定
		if (preg_match("/(LEFT|CENTER|RIGHT)(:TOP|:MIDDLE|:BOTTOM)?(:([0-9]+[%]?))?/i",$cells[0],$tmp)) {
//			var_dump($tmp); echo "<br>\n";
			if (substr($tmp[0],0,1) != ':') {
				if (array_key_exists (3,$tmp)) {
					if ($tmp[4]) {
						if (!strpos($tmp[4],"%")) $tmp[4] .= "px";
						$this->style['width'] = "width:".$tmp[4].";";
					}
				}
				if (array_key_exists (1,$tmp)) {
					if ($tmp[1]) $this->style['align'] = "text-align:".strtolower($tmp[1]).";";
				}
				if (array_key_exists (2,$tmp)) {
					if ($tmp[2]) $this->style['valign'] = "vertical-align:".substr(strtolower($tmp[2]),1).";";
				}
				$cells[0] = preg_replace("/(LEFT:|CENTER:|RIGHT:)(TOP:|MIDDLE:|BOTTOM:)?(([0-9]+[%]?))?/i","",$cells[0]);
			}
		}
//		echo "CELL2: {$cells[0]}<br>\n";
//		var_dump($this->style);
		return implode('|',$cells);
	}
}

// | title1 | title2 | title3 |
// | cell1  | cell2  | cell3  |
// | cell4  | cell5  | cell6  |
class PukiWikiTable extends PukiWikiElement
{
	var $type;
	var $types;
	var $col; // number of column
	var $table_around,$table_sheet,$table_style,$div_style,$table_align;
	
	
	function PukiWikiTable($out)
	{
		parent::PukiWikiElement();
		
//		echo $text."<br/>\n";
		if (PukiWikiConfig::getParam("ExtTable")) {
//			echo $out[1]."\n";
			$cells = $this->table_inc_add(explode('|', $out[1]));
			if (strtolower($out[2])=='c') {
				$cells[0] = $this->get_table_style($cells[0]);
			}
//			var_dump($cells);
		} else {
			$cells = explode('|', $out[1]);
		}
		$this->col = count($cells);
		$this->type = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = ($this->type == 'c');
		$row = array();
		foreach ($cells as $cell) {
			$row[] = &new PukiWikiTableCell($cell, $is_template);
		}
		$this->elements[] = $row;
	}

	function canContain(&$obj)
	{
		return is_a($obj, 'PukiWikiTable') && ($obj->col == $this->col);
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
		for ($ncol = 0; $ncol < $this->col; $ncol++) {
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow) {
				$row = &$this->elements[$nrow];
				if ($row[$ncol]->rowspan == 0) {
					++$rowspan;
					continue;
				}
				$row[$ncol]->rowspan = $rowspan;
				while (--$rowspan) { // 行種別を継承する
					$this->types[$nrow + $rowspan] = $this->types[$nrow];
				}
				$rowspan = 1;
			}
		}

		// colspan,styleを設定
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow) {
			$row = &$this->elements[$nrow];
			if ($this->types[$nrow] == 'c') {
				$stylerow = &$row;
			}
			$colspan = 1;
			foreach (array_keys($row) as $ncol) {
				if ($row[$ncol]->colspan == 0) {
					++$colspan;
					continue;
				}
				$row[$ncol]->colspan = $colspan;
				if ($stylerow !== NULL) {
					$row[$ncol]->setStyle($stylerow[$ncol]->style);
					while (--$colspan) {// 列スタイルを継承する
						$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
					}
				}
				$colspan = 1;
			}
		}

		// テキスト化
		$string = '';
		foreach ($parts as $type => $part) {
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow) {
				if ($this->types[$nrow] != $type) 
					continue;
				$row = &$this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol) {
					$row_string .= $row[$ncol]->toString();
				}
				$part_string .= $this->wrap($row_string, 'tr');
			}
			$string .= $this->wrap($part_string, $part);
		}
		$string = $this->wrap($string, 'table', ' class="'.PukiWikiConfig::getParam('style_prefix').'style_table"'."$this->table_style style=\"$this->table_sheet\"");
		
		return $this->wrap($string, 'div', ' class="'.PukiWikiConfig::getParam('style_prefix').'ie5" '.$this->div_style).$this->table_around;
	}
	// テーブル入れ子用の連結
	function table_inc_add ($arytable)
	{
		// }{で囲んだ場合は、同じセル内＝テーブルを入れ子にできる。
		$td_level = 0 ;
		$lines_tmp = array();
		$td_tmp = "";
		foreach($arytable as $td){
			if (preg_match('/^\}([^|]*)$/',$td,$reg)) {
				$td_level += 1;
				if ($td_level == 1) $td = $reg[1];
			}
			if (preg_match('/^([^|]*)\{$/',$td,$reg)) {
				$td_level -= 1;
				if ($td_level == 0) $td = $reg[1];
			}
			if ($td_level) {
				if ($td_level == 1) {
					//表内であるかの判定
					if (preg_match('/^.*___td_br___$/',$td) || preg_match('/^___td_br___.*$/',$td)) {
						$rep_str = "\n";
					} else {
						$rep_str = "->\n";
					}
					$td = preg_replace('/___td_br___([ #\-+*]|(___td_br___)+)/e',"str_replace('___td_br___','$rep_str','$0')",$td);
					$td_tmp .= str_replace('~___td_br___',"~$rep_str",$td)."|";
				} else {
					$td_tmp .= str_replace('___td_br___',"->\n",$td)."|";
				}
			} else {
				$td_tmp .= $td;//ok
				$td_tmp = str_replace('___td_br___',"\n",$td_tmp);
				$lines_tmp[] = $td_tmp;
				$td_tmp = "";
			}
		}
		return $lines_tmp;
	}
	function get_table_style($string) {
//		echo "TABLE: $string <br>\n";
		$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent";
		//$this->table_around = "<br clear=all /><br />";
		$this->table_around = "<br clear=all />";
		// 回り込み指定
		if (preg_match("/AROUND/i",$string)) $this->table_around = "";
		// ボーダー指定
		if (preg_match("/B:([0-9]+),?([0-9]*)(one|two|boko|deko|in|out|dash|dott)?/i",$string,$reg)) {
			if (array_key_exists (3,$reg)) {
				if (preg_match("/one/i",$reg[3])) $border_type = "solid";
				else if (preg_match("/two/i",$reg[3])) $border_type = "double";
				else if (preg_match("/boko/i",$reg[3])) $border_type = "groove";
				else if (preg_match("/deko/i",$reg[3])) $border_type = "ridge";
				else if (preg_match("/in/i",$reg[3])) $border_type = "inset";
				else if (preg_match("/out/i",$reg[3])) $border_type = "outset";
				else if (preg_match("/dash/i",$reg[3])) $border_type = "dashed";
				else if (preg_match("/dott/i",$reg[3])) $border_type = "dotted";
				else $border_type = "outset";
			} else {
				$border_type = "outset";
			}
			
			//$this->table_style .= " border=\"".$reg[1]."\"";
			if (array_key_exists (1,$reg)) {
				if ($reg[1]==="0"){
					$this->table_sheet .= "border:none;";
				} else {
					$this->table_sheet .= "border:".$border_type." ".$reg[1]."px;";
				}
			}
			if (array_key_exists (2,$reg)) {
				if ($reg[2]!=""){
					$this->table_style .= " cellspacing=\"".$reg[2]."\"";
				} else {
					$this->table_style .= " cellspacing=\"1\"";
				}
			}
			$string = preg_replace("/B:([0-9]+),?([0-9]*)(one|two|boko|deko|in|out|dash|dott)?/i","",$string);
		} else {
			$this->table_style .= " border=\"0\" cellspacing=\"1\"";
			//$this->table_style .= " cellspacing=\"1\"";
			//$this->table_sheet .= "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/BC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i",$string,$reg)) {
			$this->table_sheet .= "border-color:".$reg[1].";";
			$string = preg_replace("/BC:(#?[0-9abcdef]{6}?|$colors_reg)/i","",$string);
		}
		// テーブル背景色指定
		if (preg_match("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i",$string,$reg)) {
			if ($reg[1]==="0") $reg[1]="transparent";
			$this->table_sheet .= "background-color:".$reg[1].";";
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,no|,one|,1)?\))/i","TC:$2",$string);
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$string);
		}
		// テーブル背景画像指定
		if (preg_match("/TC:\(([^),]*)(,once|,1)?\)/i",$string,$reg)) {
			$reg[1] = str_replace("http","HTTP",$reg[1]);
			$this->table_sheet .= "background-image: url(".$reg[1].");";
			if ($reg[2]) $this->table_sheet .= "background-repeat: no-repeat;";
			$string = preg_replace("/TC:\(([^),]*)(,once|,1)?\)/i","",$string);
		}
		// 配置・幅指定
		if (preg_match("/T(LEFT|RIGHT)/i",$string,$reg)) {
			$this->table_align = strtolower($reg[1]);
			$this->table_style .= " align=\"".$this->table_align."\"";
			$this->div_style = " style=\"text-align:".$this->table_align."\"";
			if ($this->table_align == "left"){
				$this->table_sheet .= "margin-left:10px;margin-right:auto;";
			} else {
				$this->table_sheet .= "margin-left:auto;margin-right:10px;";
			}
		}
		if (preg_match("/T(CENTER)/i",$string,$reg)) {
			$this->table_style .= " align=\"".strtolower($reg[1])."\"";
			$this->div_style = " style=\"text-align:".strtolower($reg[1])."\"";
			$this->table_sheet .= "margin-left:auto;margin-right:auto;";
			$this->table_around = "";
		}
//		echo "TABLE2: $string<br>\n";
		if (preg_match("/(TLEFT|TCENTER|TRIGHT|(?:[^A-Z]T)):([0-9]+[%]?)/i",$string,$reg)) {
			if (array_key_exists (2,$reg)) {
				if (!strpos($reg[2],"%")) $reg[2] .= "px";
				$this->table_sheet .= "width:".$reg[2].";";
			}
		}
		$string = preg_replace("/(TLEFT|TCENTER|TRIGHT|([^F])T):([0-9]+[%]?)?/i","",$string);
//		echo "TABLE2: $string<br>\n";
		return $string;
	}
}

// , title1 , title2 , title3
// , cell1  , cell2  , cell3
// , cell4  , cell5  , cell6
class PukiWikiYTable extends PukiWikiElement
{
	var $col;
	
	function PukiWikiYTable($_value)
	{
		parent::PukiWikiElement();
		
		$align = $value = $matches = array();
		foreach($_value as $val) {
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/', $val, $matches)) {
				$align[] =($matches[1] != '') ?
					((isset($matches[3]) && $matches[3] != '') ?
						' style="text-align:center"' :
						' style="text-align:right"'
					) : '';
				$value[] = $matches[2];
			} else {
				$align[] = '';
				$value[] = $val;
			}
		}
		$this->col = count($value);
		$colspan = array();
		foreach ($value as $val) {
			$colspan[] = ($val == '==') ? 0 : 1;
		}
		$str = '';
		for ($i = 0; $i < count($value); $i++) {
			if ($colspan[$i]) {
				while ($i + $colspan[$i] < count($value) && $value[$i + $colspan[$i]] == '==') {
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
		return is_a($obj, 'PukiWikiYTable') && ($obj->col == $this->col);
	}

	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function toString()
	{
		$rows = '';
		foreach ($this->elements as $str) {
			$rows .= "\n<tr class=\"".PukiWikiConfig::getParam('style_prefix')."style_tr\">$str</tr>\n";
		}
		$rows = $this->wrap($rows, 'table', ' class="'.PukiWikiConfig::getParam('style_prefix').'style_table" cellspacing="1" border="0"');
		return $this->wrap($rows, 'div', ' class="'.PukiWikiConfig::getParam('style_prefix').'ie5"');
	}
}

// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
class PukiWikiPre extends PukiWikiElement
{
	function PukiWikiPre(& $root, $text)
	{
		parent::PukiWikiElement();
		$this->elements[] = htmlspecialchars(
			(! PukiWikiConfig::getParam('preformat_ltrim') || $text == '' || $text{0} != ' ') ? $text : substr($text, 1)
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
		// <Pre>による大量行出力時にスクロールバーを表示する用の処理 by nobunobu
		$maxlines = PukiWikiConfig::getParam('pre_maxlines') ? PukiWikiConfig::getParam('pre_maxlines') :20;
		$maxcols = PukiWikiConfig::getParam('pre_maxcols') ? PukiWikiConfig::getParam('pre_maxcols') :90;
		$colcount = 0;
		$linecount = count($this->elements);
		foreach($this->elements as $line) {
			if (mb_strwidth($line) > $colcount) $colcount = mb_strwidth($line);
		}
		if ($colcount > $maxcols) $linecount += 1.2; //横スクロールバーが表示されるときの為の、かなりいい加減な補正
		if (($linecount>=$maxlines) or ($colcount > $maxcols)) {
			if ($linecount > $maxlines) {
				$linecount = $maxlines + 0.5;
			}
			return $this->wrap(join("\n", $this->elements), 'pre' ,' style="height:'.$linecount*1.2.'em;" class="'.PukiWikiConfig::getParam('style_prefix').'pre"');
		} else {
			return $this->wrap(join("\n", $this->elements), 'pre' ,' class="'.PukiWikiConfig::getParam('style_prefix').'pre"');
		}
	}
}

// #someting(started with '#')
class PukiWikiDiv extends PukiWikiElement
{
	var $name;
	var $param;
	
	function PukiWikiDiv($out)
	{
		parent::PukiWikiElement();
		list(, $this->name, $this->param) = array_pad($out,3,'');
	}

	function canContain(&$obj)
	{
		return FALSE;
	}

	function toString()
	{
		return PukiWikiPlugin::do_plugin_convert($this->name, $this->param);
	}
}

// LEFT:/CENTER:/RIGHT:
class PukiWikiAlign extends PukiWikiElement
{
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

// Body
class PukiWikiBody extends PukiWikiElement
{
	var $id;
	var $count = 0;
	var $contents;
	var $contents_last;
	var $classes = array(
		'-' => 'PukiWikiUList',
		'+' => 'PukiWikiOList',
		'>' => 'PukiWikiBQuote',
		'<' => 'PukiWikiBQuote'
	);
	var $factories = array(
		':' => 'PukiWikiDList',
		'|' => 'PukiWikiTable',
		',' => 'PukiWikiYTable',
		'#' => 'PukiWikiDiv'
	);
	
	function PukiWikiBody($id)
	{
		$this->id = $id;
		$this->contents =& new PukiWikiElement();
		$this->contents_last = &$this->contents;
		parent::PukiWikiElement();
	}

	function parse(&$lines)
	{
		$this->last = &$this;
		$matches = array();
		
		while (! empty($lines)) {
			$line = array_shift($lines);
			
			// Escape comments
			if (substr($line, 0, 2) == '//') continue;
			
			if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/',$line,$matches)) {
				// <div style="text-align:...">
				$this->last = &$this->last->add(new PukiWikiAlign(strtolower($matches[1])));

				if ($matches[2] == '') continue;

				$line = $matches[2];
			}
			
			$line = preg_replace("/[\r\n]*$/",'',$line);
			
			// Empty
			if ($line == '') {
				$this->last = &$this;
				continue;
			}

			// Horizontal Rule
			if (substr($line,0,4) == '----') {
				$this->insert(new PukiWikiHRule($this, $line));
				continue;
			}

			// The first character
			$head = $line{0};
			
			// Heading
			if ($head == '*') {
				$this->insert(new PukiWikiHeading($this, $line));
				continue;
			}
			
			// Pre
			if ($head == ' ' || $head == "\t") {
				$this->last = & $this->last->add(new PukiWikiPre($this,$line));
				continue;
			}
			
			// Line Break
			if (substr($line, -1) == '~') {
				$line = substr($line,0,-1)."\r";
			}
			
			// Other Character
			if (isset($this->classes[$head])) {
				$classname = $this->classes[$head];
				$this->last = &$this->last->add(new $classname($this,$line));
				continue;
			}
			
			// Other Character
			if (isset($this->factories[$head])) {
				$factoryname = 'Factory_' . $this->factories[$head];
				$this->last  = & $this->last->add($factoryname($this, $line));
				continue;
			}

			// Default
			$this->last =& $this->last->add(Factory_PukiWikiInline($line));
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
		if (is_a($obj, 'PukiWikiInline')) $obj = & $obj->toPara();
		return parent::insert($obj);
	}

	function toString()
	{
		$text = parent::toString();
		
		// #contents
		$text = preg_replace_callback('/(<p[^>]*>)<del>#contents<\/del>(\s*)(<\/p>)/', array(&$this,'replace_contents'),$text);
		
		return "$text";
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
		parent::PukiWikiListContainer('ul', 'li', '-', str_repeat('-', $level));
		$this->insert(Factory_PukiWikiInline($text));
	}

	function setParent(&$parent)
	{
		parent::setParent($parent);
		$step = $this->level;
		$margin = $this->left_margin;
		if (isset($parent->parent) && is_a($parent->parent, 'PukiWikiListContainer')) {
			$step -= $parent->parent->level;
			$margin = 0;
		}
		$margin += $this->margin * ($step == $this->level ? 1 : $step);
		$this->style = sprintf(PukiWikiConfig::getParam('_list_pad_str'), $this->level, $margin,$margin);
	}
}
?>
