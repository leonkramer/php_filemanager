<?php
/*
 * Copyright (C) 2013 Leon Kramer <mail@leonkramer.com>
 *  
 * This file is part of php_filemanager.
 * 
 * php_filemanager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php_filemanager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php_filemanager. If not, see <http://www.gnu.org/licenses/>.
 */



/*
 *
 * No need to edit
 *
 */

session_start();
#session_destroy();
#exit;

#echo "<pre>";
#print_r($_SERVER);
#echo "</pre>";

require_once("settings.php");
require_once("functions.php");
require_once("class/classautoloader.php");

$autoloader = new classAutoLoader();
$c_path = new path();
$c_phpass = new PasswordHash(8, FALSE);

define('PRODUCTION', 1);
if (!PRODUCTION) {
    error_reporting(E_ALL);
    ini_set('display_error', 1);
} else {
    error_reporting(0);
    ini_set('display_error', 0);
}

define('REQUEST_URI', $_SERVER['REQUEST_URI']);
define('REQUEST_URI_NO_BASE', $c_path->say(REQUEST_URI, "DROP_BASE"));
define('PHP_DIR_PATH', getcwd());
define('PHP_DIR_NAME', basename(__DIR__));
define('ABSOLUTE_BASE', dirname(PHP_DIR_PATH)); // absolute path of php_filemanager's parent dir
define('HOSTNAME', $_SERVER['HTTP_HOST']);
define('SCHEME', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
define('PATH_TEMPLATES', 'html/');
define('PATH_CSS', PATH_TEMPLATES .'css/');
define('PATH_IMG', PATH_TEMPLATES .'img/');



/*
 * Allow certain files from php_filemanager dir
 * like css, js, imgs, etc. */

if (preg_match('~^/css/(.*\.css)~', REQUEST_URI_NO_BASE, $match)) {
    if (file_exists(PATH_CSS . $match[1])) {
        header('content-type: text/css');
        echo file_get_contents(PATH_CSS . $match[1]);
        exit;
    }
}
if (preg_match('~^/img/(.*\.(?:gif|jpg|png|jpeg))$~', REQUEST_URI_NO_BASE, $match)) {
    if (file_exists(PATH_IMG . $match[1])) {
        header('content-type: ' . mime_content_type(PATH_IMG . $match[1]));
        echo file_get_contents(PATH_IMG . $match[1]);
        exit;
    }
}


/* 
 * Create Password Arrays */

if (!isset($_SESSION['passwd'])) {
    $_SESSION['passwd'] = array();
}


$passwd = array();
$passwdFile = file("passwd");

foreach ($passwdFile as $pwd) {
    $split = strrpos($pwd, ':');
    $passwd[trim(substr($pwd, 0, $split))] = trim(substr($pwd, $split+1));
}

/*
 * refresh / clean up session passwds */
foreach ($_SESSION['passwd'] as $key => $value) {
    if (time() - $value > PASSWD_TIMEOUT || !isset($passwd[$key])) {
        unset($_SESSION['passwd'][$key]);
    } else {
        $_SESSION['passwd'][$key] = time();
    }
}


/*
 * Check if password match */

if ($_SERVER['REQUEST_METHOD'] === "POST" && $_POST['form'] === "passwd") {
    if ($c_phpass->CheckPassword($_POST['passwd'], $passwd[REQUEST_URI_NO_BASE])) {
        $_SESSION['passwd'][REQUEST_URI_NO_BASE] = time();
        if (isset($_SESSION['refresh'])) {
            $refresh = $_SESSION['refresh'];
            unset($_SESSION['refresh']);
            header('Location: '. URI_BASE . (substr($refresh, 1)));
            exit;
        } 
    } else {
        $passwd_wrong = true;
    }
} else {
    /* unset refresh path if user interrupts
     * password input */
    unset($_SESSION['refresh']);
}


/*
 * Change directory to folder or set error flag
 * if file/directory does not exist */

// start at our base, e.g. /var/www/files/
chdir(ABSOLUTE_BASE);

// build also an absolute path starting from our relative base, e.g. /files/
$path = URI_BASE;

// set an error flag
$error = false; 


$pathArr = explode('/', substr(REQUEST_URI_NO_BASE, 1)); // skip first /, or first element will be empty

for ($i=0; $i<count((array) $pathArr)-1; $i++) {
    if (!is_dir($pathArr[$i])) {
        $error = sprintf("directory '%s' does not exist", $path . $pathArr[$i]);
        break;
    }
    if ($pathArr[$i] == ".." OR $pathArr[$i] == PHP_DIR_NAME) {
        $error = sprintf("cannot change directory to '%s' - forbidden", $pathArr[$i]);
        break;
    }
    if (chdir($pathArr[$i])) {
        $path .= $pathArr[$i] . "/";
    }
    if (isset($passwd[$pathTmp = $c_path->say($path, "DROP_BASE")])) {
        if (!isset($_SESSION['passwd'][$pathTmp])) {
            $passwd_required = $path;
            if (REQUEST_URI != $path) {
                $_SESSION['refresh'] = REQUEST_URI;
                header('Location: '. $path);
                exit;
            }
        }
    }
}

$list_dir = $path;
$file = end($pathArr);

if (isset($_SESSION['download']) && $_SESSION['download'] === REQUEST_URI && !is_dir($_SESSION['download'])) {
    unset($_SESSION['download']);
    header('Content-Type: '.mime_content_type($file));
    header('Content-Disposition: attachment; filename="'. basename($file) .'"');
    header('Expires: 0');
    header('Pragma: public');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Length: '.filesize($file));
                                                                            
    ob_clean();
    flush();
    readfile($file);
    exit();
}

if (!$error) {
    if (!file_exists($file)) {
        $error = sprintf("file %s not found", $file);
    }
    $path .= $file;
    if (!empty($file)) {
        if (isset($_SESSION['passwd'][$c_path->say($path, "DROP_BASE")]) || !isset($passwd[$c_path->say($path, "DROP_BASE")])) {
            $_SESSION['download'] = $path;
        } else {
            $passwd_required = $path;
        }
    }
}

$action = "list";

switch ($action) {
    case "list": // list directory
        if (isset($passwd_required)) {
            $files = "false";
            break;
        }
        $file_array = scandir("./");

        $files = array();
        foreach ($file_array as $file)
        {
            if (is_dir($file)) {
                $file .= '/';
            }
            if ($file === './' || $file === '.htaccess' || $file === PHP_DIR_NAME . '/') {
                continue;
            }
            if (($file === '../') && getcwd() === ABSOLUTE_BASE) {
                continue;
            }

            $files = array_merge_recursive($files, array($file => 
                array("type" => mime_content_type($file),
                      "href" => $file,
                      "path" => $path . $file,
                      "size" => number_format(filesize($file)/1024),
                      "protected" => (isset($passwd[$c_path->say($path . $file, "DROP_BASE")]) ? 1 : 0),
                      "locked" => (isset($_SESSION['passwd'][$c_path->say($path . $file, "DROP_BASE")]) ? 0 : 1),
                      "time" => filemtime($file))));
        }
        uasort($files, "usort_dir");
    break;

    default:
        $files = "hidden";
    break;
}


/*
 * Prepare variables for easier use in templates */

$data = array();

$data['files'] = $files;
if (isset($passwd_required)) {
    $data['passwd_required'] = $passwd_required;
}


if (isset($_SESSION['download']) && $_SESSION['download'] === REQUEST_URI) {
    if (strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4)) === "wget" || strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4)) === "curl") {
        header("Location: {$_SESSION['download']}", true, 302);
        exit;
    }
    header("refresh:2;url={$_SESSION['download']}", true, 302);
}
/*
 * Import of templates / Start of HTML */

include(PATH_TEMPLATES . "html_header.tpl");
include(PATH_TEMPLATES . "html_opening_tags.tpl");

if (isset($passwd_required)) {
    include(PATH_TEMPLATES . "password_input.tpl");
}

if ($action == "list") {
    include(PATH_TEMPLATES . "directory_list.tpl");
}


include(PATH_TEMPLATES . "html_closing_tags.tpl");
?>
