#!/usr/bin/php -q
<?php
$cwd = getcwd();
$dirs = array_reverse(explode(DIRECTORY_SEPARATOR, $cwd));

$cdup = null;
foreach ($dirs as $idx => $dir) {
	if ($dir == 'app') {
		$cdup = $idx;
		break;
	}
}

if (is_null($cdup)) {
	$dirs = scandir($cwd);
	if (in_array('app', $dirs)) {
		chdir('app');
		$cdup = 0;
	}
}

$cdstring = '';
for ($i = 0; $i < $cdup; $i++) {
	$cdstring .= '../';
}
if ($cdstring) {
	chdir($cdstring);
}
if ($argc > 1) {
	if ($argv[1] == 'test') {
		$argv[1] = 'Tdd.test';
	}
}
system('echo "PASS" | sudo -u root -S /bin/chown -R '.posix_getuid().':'.posix_getuid().' tmp');
chmodr('tmp', 0777);
include "Console/cake.php";

function chmodr($path, $filemode) {
	if (!is_dir($path))
		return chmod($path, $filemode);

	$dh = opendir($path);
	while (($file = readdir($dh)) !== false) {
		if ($file != '.' && $file != '..') {
			$fullpath = $path . '/' . $file;
			if (is_link($fullpath))
				return FALSE;
			elseif (!is_dir($fullpath) && !chmod($fullpath, $filemode))
				return FALSE;
			elseif (!chmodr($fullpath, $filemode))
				return FALSE;
		}
	}

	closedir($dh);

	if (chmod($path, $filemode))
		return TRUE;
	else
		return FALSE;
}
?>