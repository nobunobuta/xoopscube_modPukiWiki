<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki�ץ饰����ƽ��ѥ��饹
//
// �������ե����롧PukiWiki 1.4��plugin.php
//
class PukiWikiPlugin {
	// �ץ饰�����Ѥ�̤������ѿ�������
	function set_plugin_messages($messages)
	{
		foreach ($messages as $name=>$val)
		{
			global $$name;
			
			if (!isset($$name))
			{
				$$name = $val;
			}
		}
	}

	//�ץ饰����¸�ߤ��뤫
	function exist_plugin($name)
	{
		$name = strtolower($name);	//Ryuji_edit(2003-03-18) add ��ʸ���Ⱦ�ʸ������̤��ʤ��ե����륷���ƥ��к�
		if (preg_match('/^\w{1,64}$/', $name)
			and file_exists(MOD_PUKI_PLUGIN_DIR . $name . '.inc.php'))
		{
			require_once(MOD_PUKI_PLUGIN_DIR . $name . '.inc.php');
			return TRUE;
		}
		return FALSE;
	}

	//�ץ饰����ؿ�(action)��¸�ߤ��뤫
	function exist_plugin_action($name) {
		return	function_exists('plugin_' . $name . '_action') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_action') : FALSE);
	}

	//�ץ饰����ؿ�(convert)��¸�ߤ��뤫
	function exist_plugin_convert($name) {
		return	function_exists('plugin_' . $name . '_convert') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_convert') : FALSE);
	}

	//�ץ饰����ؿ�(inline)��¸�ߤ��뤫
	function exist_plugin_inline($name) {
		return	function_exists('plugin_' . $name . '_inline') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_inline') : FALSE);
	}

	//�ץ饰����ν������¹�
	function do_plugin_init($name)
	{
		static $check = array();
		
		if (array_key_exists($name,$check))
		{
			return $check[$name];
		}
		
		$func = 'plugin_'.$name.'_init';
		if ($check[$name] = function_exists($func))
		{
			@call_user_func($func);
			return TRUE;
		}
		return FALSE;
	}

	//�ץ饰����(action)��¹�
	function do_plugin_action($name)
	{
		if (!PukiWikiPlugin::exist_plugin_action($name))
		{
			return array();
		}
		
		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func('plugin_'.$name.'_action');
		
		// ʸ�����󥳡��ǥ��󥰸����� hidden �ե�����ɤ���������
		return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"��\" /></div>",$retvar);
	}

	//�ץ饰����(convert)��¹�
	function do_plugin_convert($name,$args='')
	{
		global $digest;
		
		// digest������
		$_digest = $digest;
		
		$aryargs = ($args !== '') ? PukiWikiFunc::csv_explode(',', $args) : array();

		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func_array('plugin_'.$name.'_convert',$aryargs);
		
		// digest������
		$digest = $_digest;
		
		if ($retvar === FALSE)
		{
			return htmlspecialchars('#'.$name.($args ? "($args)" : ''));
		}
		
		// ʸ�����󥳡��ǥ��󥰸����� hidden �ե�����ɤ���������
		return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"��\" /></div>",$retvar);
	}

	//�ץ饰����(inline)��¹�
	function do_plugin_inline($name,$args,$body)
	{
		global $digest;
		
		// digest������
		$_digest = $digest;
		
		$aryargs = ($args !== '') ? PukiWikiFunc::csv_explode(',',$args) : array();
		$aryargs[] =& $body;

		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func_array('plugin_'.$name.'_inline',$aryargs);
		
		// digest������
		$digest = $_digest;
		
		if($retvar === FALSE)
		{
			return htmlspecialchars("&${name}" . ($args ? "($args)" : '') . ';');
		}
		
		return $retvar;
	}
}
?>
