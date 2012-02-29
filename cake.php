#!/usr/bin/php -q
<?php
$cwd = getcwd();
$dirs = array_reverse(explode(DIRECTORY_SEPARATOR, $cwd));
$uid = getmyuid();
$user = get_current_user();
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
$tmp_files = array();
exec('ls -Arl tmp', $tmp_files);
$require_chown = false;

array_shift($tmp_files);
foreach ($tmp_files as $f) {
	$f_parts = explode(' ', $f);
	$f_user = $f_parts[2];
	if ($f_user != $user) {
		$require_chown = true;
		echo "Incorrect user detected for some of the temporary files\n";
		break;
	}
}

if ($require_chown) {
	echo "Your root password is required to run chown on the temporary directory\n";
	$pass = null;
	while (is_null($pass)) {
		$pass = passwordPrompt();
	}
	$retval = 0;
	$op = array();
	exec('echo "' . $pass . '" | sudo -u root -S /bin/chown -R ' . $uid . ':' . $uid . ' tmp 2>/dev/null', $op, $retval);
	if ($retval != 0) {
		die("Invalid root password\n");
	}
	chmodr('tmp', 0777);
}
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

function passwordPrompt() {
	passthru('set /p pass=Password: ');
	echo 'Password: ';
	$pwd = preg_replace('/\r?\n$/', '', `stty -echo; head -n1 ; stty echo`);
	echo "\n";
	echo "Your password was: {$pwd}.\n";
	if (strlen($pwd)) {
		return $pwd;
	} else {
		return null;
	}
}
?>