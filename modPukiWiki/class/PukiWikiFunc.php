<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki���Ѵؿ����饹��
//  ���󥹥��󥹲������˥��С��ؿ���ƤӽФ�
//
// �������ե����롧PukiWiki 1.4��file.php func.php html.php proxy.php�������Ѵؿ��Τߤ���С�
//
class PukiWikiFunc {
	// ʸ����URL���ɤ���
	function is_url($str,$only_http=FALSE)
	{
		$scheme = $only_http ? 'https?' : 'https?|ftp|news';
		return preg_match('/^('.$scheme.')(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]*)$/', $str);
	}

	// ʸ����InterWikiName���ɤ���
	function is_interwiki($str)
	{
		$InterWikiName = PukiWikiConfig::getParam('InterWikiName');

		return preg_match("/^$InterWikiName$/",$str);
	}

	// ʸ���󤬥ڡ���̾���ɤ���
	function is_pagename($str)
	{
		$BracketName=PukiWikiConfig::getParam('BracketName');
		$WikiName=PukiWikiConfig::getParam('WikiName');;
		
		$is_pagename = (!PukiWikiFunc::is_interwiki($str) and preg_match("/^(?!\/)$BracketName$(?<!\/$)/",$str)
			and !preg_match('/(^|\/)\.{1,2}(\/|$)/',$str));
		
		if (defined('MOD_PUKI_SOURCE_ENCODING'))
		{
			if (MOD_PUKI_SOURCE_ENCODING == 'UTF-8')
			{
				$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/',$str)); // UTF-8
			}
			else if (MOD_PUKI_SOURCE_ENCODING == 'EUC-JP')
			{
				$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\x8E\xA1-\xFE][\xA1-\xFE])|(?:\x8F[\xA1-\xFE][\xA1-\xFE]))+$/',$str)); // EUC-JP
			}
		}
		
		return $is_pagename;
	}

	// �ڡ����Υե�����̾������
	function get_filename($page)
	{
		if (MOD_PUKI_WIKI_VER=='1.3') $page=PukiWikiFunc::add_bracket(PukiWikiFunc::strip_bracket($page));
		return MOD_PUKI_WIKI_DATA_DIR . PukiWikiFunc::encode($page) . '.txt';
	}

	// �ڡ�����¸�ߤ��뤫
	function is_page($page,$reload=FALSE)
	{
		return file_exists(PukiWikiFunc::get_filename($page));
	}

	// �ڡ���̾�Υ��󥳡���
	function encode($key)
	{
		return ($key == '') ? '' : strtoupper(join('',unpack('H*0',$key)));
	}

	// �ڡ���̾�Υǥ�����
	function decode($key)
	{
		return ($key == '') ? '' : substr(pack('H*','20202020'.$key),4);
	}

	// [[ ]] ��������
	function strip_bracket($str)
	{
		if (preg_match('/^\[\[(.*)\]\]$/',$str,$match))
		{
			$str = $match[1];
		}
		return $str;
	}

	// [[ ]] ���ղä���
	function add_bracket($str){
		$WikiName=PukiWikiConfig::getParam('WikiName');;
		
		if (!preg_match("/^".$WikiName."$/",$str)){
			if (!preg_match("/\[\[.*\]\]/",$str)) $str = "[[".$str."]]";
		}
		return $str;
	}

	// HTML������������
	function strip_htmltag($str)
	{
		$_symbol_noexists = PukiWikiConfig::getParam('_symbol_noexists');
		
		$noexists_pattern = '#<span class="'.PukiWikiConfig::getParam('style_prefix').'noexists">([^<]*)<a[^>]+>'.
			preg_quote($_symbol_noexists,'#').
			'</a></span>#';
		
		$str = preg_replace($noexists_pattern,'$1',$str);
		return preg_replace('/<[^>]+>/','',$str);
	}

	// ��󥯤��ղä���
	function make_link($string,$page = '')
	{
		static $converter;
		
		if (!isset($converter))
		{
			$converter = new PukiWikiInlineConverter();
		}
		$_converter = $converter; // copy
		return $_converter->convert($string, $page );
	}

	// ���Ф������� (����HTML���������)
	function make_heading(&$str,$strip=TRUE)
	{
		$NotePattern = PukiWikiConfig::getParam('NotePattern');
		
		// ���Ф��θ�ͭID������
		$id = '';
		if (preg_match('/^(\*{0,3})(.*?)\[#([A-Za-z][\w-]+)\](.*?)$/m',$str,$matches)) {
			$str = $matches[2].$matches[4];
			$id = $matches[3];
		} else {
			$str = preg_replace('/^\*{0,3}/','',$str);
		}
		if ($strip) {
			$str = PukiWikiFunc::strip_htmltag(PukiWikiFunc::make_link(preg_replace($NotePattern,'',$str)));
		}
		return $id; 
	}

	// CSV������ʸ����������
	function csv_explode($separator, $string)
	{
		$_separator = preg_quote($separator,'/');
		if (!preg_match_all('/("[^"]*(?:""[^"]*)*"|[^'.$_separator.']*)'.$_separator.'/', $string.$separator, $matches))
		{
			return array();
		}

		$retval = array();
		foreach ($matches[1] as $str)
		{
			$len = strlen($str);
			if ($len > 1 and $str{0} == '"' and $str{$len - 1} == '"')
			{
				$str = str_replace('""', '"', substr($str, 1, -1));
			}
			$retval[] = $str;
		}
		return $retval;
	}

	// �����CSV������ʸ�����
	function csv_implode($glue, $pieces)
	{
		$_glue = ($glue != '') ? '\\'.$glue{0} : '';
		$arr = array();
		foreach ($pieces as $str)
		{
			if (ereg("[$_glue\"\n\r]",$str))
			{
				$str = '"'.str_replace('"', '""', $str).'"';
			}
			$arr[] = $str;
		}
		return join($glue, $arr);
	}

	// ���߻����ޥ������ä�
	function getmicrotime()
	{
		list($usec, $sec) = explode(' ',microtime());
		return ((float)$sec + (float)$usec);
	}

	// ����������
	function get_date($format,$timestamp = NULL)
	{
		$time = ($timestamp === NULL) ? MOD_PUKI_UTIME : $timestamp;
		$time += MOD_PUKI_ZONETIME;
		
		$format = preg_replace('/(?<!\\\)T/',preg_replace('/(.)/','\\\$1',MOD_PUKI_ZONE),$format);
		
		return date($format,$time);
	}

	// ����ʸ�������
	function format_date($val, $paren = FALSE)
	{
		$date_format = PukiWikiConfig::getParam('date_format');
		$time_format = PukiWikiConfig::getParam('time_format');
		$weeklabels = PukiWikiConfig::getParam('weeklabels');
		
		$val += MOD_PUKI_ZONETIME;
		
		$ins_date = date($date_format,$val);
		$ins_time = date($time_format,$val);
		$ins_week = '('.$weeklabels[date('w',$val)].')';
		
		$ins = "$ins_date $ins_week $ins_time";
		return $paren ? "($ins)" : $ins;
	}

	// �в����ʸ�������
	function get_passage($time, $paren = TRUE)
	{
		static $units = array('m'=>60,'h'=>24,'d'=>1);
		
		$time = max(0,(MOD_PUKI_UTIME - $time) / 60); //minutes
		
		foreach ($units as $unit=>$card)
		{
			if ($time < $card)
			{
				break;
			}
			$time /= $card;
		}
		$time = floor($time).$unit;
		
		return $paren ? "($time)" : $time;
	}

	function http_request($url,$method='GET',$headers='',$post=array())
	{
		$use_proxy =PukiWikiConfig::getParam('use_proxy');
		$proxy_host =PukiWikiConfig::getParam('proxy_host');
		$proxy_port =PukiWikiConfig::getParam('proxy_port');
		
		$rc = array();
		$arr = parse_url($url);
		
		$via_proxy = $use_proxy and PukiWikiFunc::via_proxy($arr['host']);
		
		// query
		$arr['query'] = isset($arr['query']) ? '?'.$arr['query'] : '';
		// port
		$arr['port'] = isset($arr['port']) ? $arr['port'] : 80;
		
		$url = $via_proxy ? $arr['scheme'].'://'.$arr['host'].':'.$arr['port'] : '';
		$url .= $arr['path'] ? $arr['path'] : '/';
		$url .= $arr['query'];
		
		$query = $method.' '.$url." HTTP/1.0\r\n";
		$query .= "Host: ".$arr['host']."\r\n";
		$query .= "User-Agent: modPukiWiki/0.1\r\n";

		// Basic ǧ����
		if (isset($arr['user']) and isset($arr['pass']))
		{
			$query .= 'Authorization: Basic '.
				base64_encode($arr['user'].':'.$arr['pass'])."\r\n";
		}
		
		$query .= $headers;
		
		// POST ���ϡ�urlencode �����ǡ����Ȥ���
		if (strtoupper($method) == 'POST')
		{
			if (is_array($post))
			{
				$POST = array();
				foreach ($post as $name=>$val)
				{
					$POST[] = $name.'='.urlencode($val);
				}
				$data = join('&',$POST);
				$query .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$query .= 'Content-Length: '.strlen($data)."\r\n";
				$query .= "\r\n";
				$query .= $data;
			}
			else
			{
				$query .= 'Content-Length: '.strlen($post)."\r\n";
				$query .= "\r\n";
				$query .= $post;
			}
		}
		else
		{
			$query .= "\r\n";
		}
		
		$fp = fsockopen(
			$via_proxy ? $proxy_host : $arr['host'],
			$via_proxy ? $proxy_port : $arr['port'],
			$errno,$errstr,30);
		if (!$fp)
		{
			return array(
				'query'  => $query, // Query String
				'rc'     => $errno, // ���顼�ֹ�
				'header' => '',     // Header
				'data'   => $errstr // ���顼��å�����
			);
		}
		
		fputs($fp, $query);
		
		$response = '';
		while (!feof($fp))
		{
			if ($_response = fgets($fp,4096))
			{
				$response .= $_response;
			}
			else
			{
				return array(
					'query'  => $query, // Query String
					'rc'     => 408,    // ���顼�ֹ�
					'header' => '',     // Header
					'data'   => 'Request Time-out' // ���顼��å�����
				);
			}
		}
		fclose($fp);
		
		$resp = explode("\r\n\r\n",$response,2);
		$rccd = explode(' ',$resp[0],3); // array('HTTP/1.1','200','OK\r\n...')
		return array(
			'query'  => $query,             // Query String
			'rc'     => (integer)$rccd[1], // Response Code
			'header' => $resp[0],           // Header
			'data'   => $resp[1]            // Data
		);
	}

	// �ץ������ͳ����ɬ�פ����뤫�ɤ���Ƚ��
	function via_proxy($host)
	{
		$use_proxy =PukiWikiConfig::getParam('use_proxy');
		$no_proxy =PukiWikiConfig::getParam('no_proxy');
		
		static $ip_pattern = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?:\/(.+))?$/';
		
		if (!$use_proxy)
		{
			return FALSE;
		}
		$ip = gethostbyname($host);
		$l_ip = ip2long($ip);
		$valid = (is_long($l_ip) and long2ip($l_ip) == $ip); // valid ip address
		
		foreach ($no_proxy as $network)
		{
			if ($valid and preg_match($ip_pattern,$network,$matches))
			{
				$l_net = ip2long($matches[1]);
				$mask = array_key_exists(2,$matches) ? $matches[2] : 32;
				$mask = is_numeric($mask) ?
					pow(2,32) - pow(2,32 - $mask) : // "10.0.0.0/8"
					ip2long($mask);                 // "10.0.0.0/255.0.0.0"
				if (($l_ip & $mask) == $l_net)
				{
					return FALSE;
				}
			}
			else
			{
				if (preg_match('/'.preg_quote($network,'/').'/',$host))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}
}
?>