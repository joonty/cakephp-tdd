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
if (!is_dir('tmp')) {
	create_tmp_dirs();
}
$tmp_files = array();
exec('ls -ARl tmp', $tmp_files);
$require_chown = false;

array_shift($tmp_files);
foreach ($tmp_files as $f) {
	$f_parts = explode(' ', $f);
	if (count($f_parts) < 3) {
		continue;
	}
	$f_user = $f_parts[2];
	if ($f_user != $user) {
		$require_chown = true;
		fwrite(STDERR,"Incorrect user detected for some of the temporary files\n");
		break;
	}
}

$sudo_attempts = 0;
if ($require_chown) {
	while ($sudo_attempts < 3) {
		fwrite(STDERR,"Your root password is required to run chown on the temporary directory\n");
		$pass = null;
		while (is_null($pass)) {
			$pass = passwordPrompt();
		}
		$retval = 0;
		$op = array();
		exec('echo "' . $pass . '" | sudo -u root -S /bin/chown -R ' . $uid . ':' . $uid . ' tmp 2>/dev/null', $op, $retval);
		if ($retval != 0) {
			fwrite(STDERR,"Invalid root password\n");
			$sudo_attempts++;
		} else {
			break;
		}
	}
	if ($sudo_attempts == 3) {
		die("Too many failed password attempts: ending\n");
	}
}
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

function passwordPrompt() {
	passthru('set /p pass=Password: ');
	fwrite(STDERR,'Password: ');
	$pwd = preg_replace('/\r?\n$/', '', `stty -echo; head -n1 ; stty echo`);
	fwrite(STDERR,"\n");
	if (strlen($pwd)) {
		return $pwd;
	} else {
		return null;
	}
}

function create_tmp_dirs() {
	fwrite(STDERR,"Creating temporary directories\n");
	mkdir('tmp') or die("Failed to create temporary directory\n");
	mkdir('tmp/cache');
	mkdir('tmp/cache/view');
	mkdir('tmp/cache/models');
	mkdir('tmp/cache/persistent');
	mkdir('tmp/logs');
	mkdir('tmp/sessions');
}
?>
