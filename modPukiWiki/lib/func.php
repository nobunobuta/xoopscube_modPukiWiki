<?php
//is_a
//(PHP 4 >= 4.2.0)
//
//is_a --  Returns TRUE if the object is of this class or has this class as one of its parents 

if (!function_exists('is_a'))
{
	function is_a($class, $match)
	{
		if (empty($class))
		{
			return false;
		}
		$class = is_object($class) ? get_class($class) : $class;
		if (strtolower($class) == strtolower($match))
		{
			return true;
		}
		return is_a(get_parent_class($class), $match);
	}
}

//array_fill
//(PHP 4 >= 4.2.0)
//
//array_fill -- Fill an array with values

if (!function_exists('array_fill'))
{
	function array_fill($start_index,$num,$value)
	{
		$ret = array();
		
		while ($num-- > 0)
		{
			$ret[$start_index++] = $value;
		}
		return $ret;
	}
}

//md5_file
//(PHP 4 >= 4.2.0)
//
//md5_file -- Calculates the md5 hash of a given filename

if (!function_exists('md5_file'))
{
	function md5_file($filename)
	{
		if (!file_exists($filename))
		{
			return FALSE;
		}
		$fd = fopen($filename, 'rb');
		$data = fread($fd, filesize($filename));
		fclose($fd);
		return md5($data);
	}
}

?>
