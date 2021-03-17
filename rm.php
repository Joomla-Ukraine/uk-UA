<?php

rm('administrator');
rm('language');
rm('installation');

function rm($name)
{
	if($name == 'administrator')
	{
		$name = 'administrator/language';
	}

	if($name == 'installation')
	{
		$name = 'installation/language';
	}

	$from = __DIR__ . '/s4/' . $name . '/uk-UA';
	$to   = __DIR__ . '/uk-UA_4.x/' . $name . '/uk-UA';

	_makeDir($to);
	_recursive_copy($from, $to);

	_rename($to, [
		'uk-UA\.xml'           => 'langmetadata.xml',
		'uk-UA\.localise\.php' => 'localise.php',
		'uk-UA\.ini'           => 'joomla.ini',
		'uk-UA\.(.*?)\.ini'    => '\\1.ini'
	]);
}

function _makeDir($dir, $mode = 0777)
{
	if(is_dir($dir))
	{
		return true;
	}

	if(mkdir($dir, $mode, true) || is_dir($dir))
	{
		return true;
	}

	if(!_makeDir(dirname($dir)))
	{
		return false;
	}

	return mkdir($dir, $mode, true);
}

function _rename($path, $rename)
{
	$files = scandir($path);
	foreach($files as $file)
	{
		if($file === 'index.html' || $file == '.' || $file == '..')
		{
			continue;
		}

		foreach($rename as $k => $v)
		{
			$replace = preg_replace("#$k#m", "$v", $file);

			if(file_exists($path . '/' . $file))
			{
				rename($path . '/' . $file, $path . '/' . $replace);
			}
		}
	}
}

function _recursive_copy($src, $dst)
{
	$dir = opendir($src);
	@mkdir($dst);
	while(($file = readdir($dir)))
	{
		if(($file != '.') && ($file != '..'))
		{
			$text = file_get_contents($src . '/' . $file);
			$pos  = strpos($text, '4.0.0');

			if($pos === false)
			{
				echo "Строка '4.0.0' не найдена\n\n";
			}
			else
			{
				if(is_dir($src . '/' . $file))
				{
					_recursive_copy($src . '/' . $file, $dst . '/' . $file);
				}
				else
				{
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
	}

	closedir($dir);
}