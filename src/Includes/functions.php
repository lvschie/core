<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
* Prints Javascript code to refresh the browser output page.
*/
function func_refresh_start()
{
    print <<<EOT
<script typee="text/javascript">
<!--
var loaded = false;

function refresh() {
    window.scroll(0, 10000000);

    if (loaded == false) {
        setTimeout('refresh()', 500);
    }    
}

setTimeout('refresh()', 1000);
-->
</script>
EOT;
}

function func_refresh_end()
{
    print <<<EOT
<script type="text/javascript">
<!--
var loaded = true;
-->
</script>
EOT;
}

/*
* Executable lookup
* Return false if not executable.
*/
function func_find_executable($filename)
{
    $directories = explode(PATH_SEPARATOR, getenv('PATH'));
    array_unshift($directories, './bin', '/usr/bin', '/usr/local/bin');

    $result = false;

    foreach ($directories as $dir) {
        $file = $dir . '/' . $filename;
        if (func_is_executable($file)) {
            $result = @realpath($file);
            break;
        }

        $file .= '.exe';
        if (func_is_executable($file)) {
            $result = @realpath($file);
            break;
        }
    }

    return $result;
}

/*
* Emulator for the is_executable function if it doesn't exists (f.e. under windows)
*/
function func_is_executable($file)
{
    return function_exists('is_executable')
        ? (file_exists($file) && is_executable($file))
        : (is_file($file) && is_readable($file));
}

function func_define($name, $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

function get_php_execution_mode() {
    
    $options = XLite::getInstance()->getOptions();

    return isset($options['filesystem_permissions']['permission_mode'])
        ? $options['filesystem_permissions']['permission_mode']
        : 0;
}

// define actual permissions
// mode - one of 0777, 0755, 0666, 0644
function get_filesystem_permissions($mode, $file = null) {
    static $mode0777, $mode0755, $mode0666, $mode0644, $mode0666_fnp, $mode0644_fnp;
 
    // try to setup values from config
    if (
        (!isset($mode0777) || !isset($mode0755) || !isset($mode0666) || !isset($mode0644))
        && XLite::getInstance()->getOptions('filesystem_permissions')
    ) {

        $options = XLite::getInstance()->getOptions('filesystem_permissions');
        $phpExecutionMode = get_php_execution_mode();

        // 0777
        if (!isset($mode0777)) {
            if ($phpExecutionMode != 0) {
                if (isset($options['privileged_permission_dir'])) {
                    $mode0777 = base_convert(
                        $options['privileged_permission_dir'],
                        8,
                        10
                    );
                }

            } elseif (isset($options['nonprivileged_permission_dir_all'])) {
                $mode0777 = base_convert($options['nonprivileged_permission_dir_all'], 8, 10);
            }
        }

        // 0755
        if (!isset($mode0755)) {
            if ($phpExecutionMode != 0) {
                if (isset($options['privileged_permission_dir'])) {
                    $mode0755 = base_convert(
                        $options['privileged_permission_dir'],
                        8,  
                        10
                    );
                }

            } elseif (isset($options['nonprivileged_permission_dir'])) {
                $mode0755 = base_convert($options['nonprivileged_permission_dir'], 8, 10);
            }
        }

        // 0666
        if (!isset($mode0666)) {
            if ($phpExecutionMode != 0) {
                if (isset($options['privileged_permission_file'])) {
                    $mode0666 = base_convert($options['privileged_permission_file'], 8, 10);

                    if (isset($options['privileged_permission_file_nonphp'])) {
                        $mode0666_fnp = base_convert($options['privileged_permission_file_nonphp'], 8, 10);

                    } else {
                        $mode0666_fnp = $mode0666;
                    }
                }

            } elseif (isset($options['nonprivileged_permission_file_all'])) {
                $mode0666 = base_convert($options['nonprivileged_permission_file_all'], 8, 10);
                $mode0666_fnp = $mode0666;
            }
        }

        // 0644
        if (!isset($mode0644)) {
            if ($phpExecutionMode != 0) {
                if (isset($options['privileged_permission_file'])) {
                    $mode0644 = base_convert($options['privileged_permission_file'], 8, 10);
                    if (isset($options['privileged_permission_file_nonphp'])) {
                        $mode0644_fnp = base_convert($options['privileged_permission_file_nonphp'], 8, 10);
                    } else {
                        $mode0644_fnp = $mode0644;
                    }
                }

            } elseif (isset($options['nonprivileged_permission_file'])) {
                $mode0644 = base_convert($options['nonprivileged_permission_file'], 8, 10);
                $mode0644_fnp = $mode0644;
            }
        }
    }


    if (($mode == 0777) && (isset($mode0777))) {
        $mode = $mode0777;

    } elseif (($mode == 0755) && (isset($mode0755))) {
        $modet = $mode0755;

    } elseif (($mode == 0666) && (isset($mode0666))) {

        if (isset($file) && @is_file($file)) {
            $path_parts = @pathinfo($file);
            $mode = 'php' == strtolower($path_parts['extension'])
                ? $mode0666
                : $mode0666_fnp;

        } else {
            $mode = $mode0666;
        }

    } elseif (($mode == 0644) && (isset($mode0644))) {
        if (isset($file) && @is_file($file)) {
            $path_parts = @pathinfo($file);
            $mode = 'php' == strtolower($path_parts['extension'])
                ? $mode0644
                : $mode0644_fnp;

        } else {
            $mode = $mode0644;
        }
    }
    
    return $mode;
}


// copy single file and set permissions
function copyFile($from, $to, $mode = 0666)
{
    
    if ($mode == 0666) {
        $mode = get_filesystem_permissions(0666, $from);

    } elseif ($mode == 0644) {
        $mode = get_filesystem_permissions(0644, $from);
    }
    
    $result = false;
    
    if (@is_file($from)) {
        $result = @copy($from, $to);
        if (!$result) {
            \Includes\Utils\FileManager::mkdirRecursive(dirname($to));
            $result = @copy($from, $to);
        }
        @umask(0000);
        $result = $result && @chmod($to, $mode);
    }
    
    return $result;
}

function copyRecursive($from, $to, $mode = 0666, $dir_mode = 0777)
{
    $orig_dir_mode = $dir_mode;

    if ($dir_mode == 0777) {
        $dir_mode = get_filesystem_permissions(0777);

    } elseif ($dir_mode == 0755) {
        $dir_mode = get_filesystem_permissions(0755);
    }

    $orig_mode = $mode;

    if ($mode == 0666) {
        $mode = get_filesystem_permissions(0666, $from);

    } elseif ($mode == 0644) {
        $mode = get_filesystem_permissions(0644, $from);
    }

    if (@is_file($from)) {
        @copy($from, $to);
        @umask(0000);
        @chmod($to, $mode);

    } elseif (@is_dir($from)) {
        if (!@file_exists($to)) {
            @umask(0000);
            $attempts = 5;
            while (!@mkdir($to, $dir_mode)) {
                \Includes\Utils\FileManager::unlinkRecursive($to);
                $attempts --; 
                if ($attempts < 0) {
                    if($_REQUEST['target'] == "wysiwyg") {
                        echo "<font color='red'>Warning: Can't create directory $to: permission denied</font>";
                        echo '<br /><br /><a href="admin.php?target=wysiwyg">Click to return to admin interface</a>';
                    } else {
                        echo "Can't create directory $to: permission denied";
                    }
                    die;
                }
            }
        }

        if ($handle = @opendir($from)) {
            while (false !== ($file = @readdir($handle))) {
                if (!($file == "." || $file == "..")) {
                    copyRecursive($from . '/' . $file, $to . '/' . $file, $orig_mode, $orig_dir_mode);
                }
            }
            @closedir($handle);
        }

    } else {
        return 1;
    }
}

/**
* Parses the hostname specification. Converts the FQDN hostname
* to dotted hostname, for example
*
*    www.hosting.com:81 -> .hosting.com
*
*/
function func_parse_host($host)
{
    // parse URL
    if (substr(strtolower($host), 0, 7) != 'http://') {
        $host = 'http://' . $host;
    }

    $url_details = func_parse_url($host);
    $host = isset($url_details["host"]) ? $url_details["host"] : $host;
    
    // strip WWW hostname
    if (substr(strtolower($host), 0, 4) == 'www.') {
        $host = substr_replace($host, '', 0, 3);
    }

    return $host;
}

function func_parse_url($url)
{
   
    $options = XLite::getInstance()->getOptions();
 
    $parts_default = array(
        'scheme'   => 'http',
        'host'     => $options['host_details']['http_host'],
        'port'     => '', 
        'user'     => '', 
        'pass'     => '', 
        'path'     => $options['host_details']['web_dir'],
        'query'    => '', 
        'fragment' => ''
    );

    $parsed_parts = @parse_url($url);
    if (!is_array($parsed_parts)) {
        $parsed_parts = array();
    }

    return array_merge($parts_default, $parsed_parts);
}

/**
* Uploads SQL patch into the database. If $connection is not defined, uses
* mysql_query($sql) syntax, otherwise mysql_query($sql, $connection);
* If $ignoreErrors is true, it will display all SQL errors and proceed.
*/
function query_upload($filename, $connection = null, $ignoreErrors = false, $is_restore = false)
{
    $fp = @fopen($filename, 'rb');
    if (!$fp) {
        echo '<font color="red">[Failed to open $filename]</font></pre>' . "\n";
        return false;
    }

    $command = '';
    $counter = 1;

    while (!feof($fp)) {
        $c = '';

        // read SQL statement from file
        do {
            $c .= fgets($fp, 1024);
            $endPos = strlen($c) - 1;
        } while (substr($c, $endPos) != "\n" && !feof($fp));
        $c = chop($c);

        // skip comments
        if (substr($c, 0, 1) == '#' || substr($c, 0, 2) == '--') {
            continue;
        }

        // parse SQL statement
        $command .= $c;
        if (substr($command, -1) == ';') {
            $command = substr($command, 0, strlen($command)-1);

            $table_name = '';
            if (preg_match('/^CREATE TABLE ([_a-zA-Z0-9]*)/i', $command, $matches)) {
                $table_name = $matches[1];
                echo 'Creating table [' . $table_name . '] ... ';

            } elseif (preg_match('/^ALTER TABLE ([_a-zA-Z0-9]*)/i', $command, $matches)) {
                $table_name = $matches[1];
                echo 'Altering table [' . $table_name . '] ... ';

            } elseif (preg_match('/^DROP TABLE IF EXISTS ([_a-zA-Z0-9]*)/i', $command, $matches)) {
                $table_name = $matches[1];
                echo 'Deleting table [' . $table_name . '] ... ';

            } else {
                $counter ++;
            }    

            // execute SQL
            if (is_resource($connection)) {
                mysql_query($command, $connection);

            } else {
                mysql_query($command);
            }

            if (is_resource($connection)) {
                $myerr = mysql_error($connection);

            } else {
                $myerr = mysql_error();
            }

            // check for errors
            if (!empty($myerr)) {
                query_upload_error($myerr, $ignoreErrors);
                if (!$ignoreErrors) {
                    break;
                }    

            } elseif ($table_name != "") {
                echo '<font color="green">[OK]</font><br />' . "\n";

            } elseif (!($counter % 20)) {
                echo '.';
            }

            $command = '';
            flush();
        }
    }

    fclose($fp);
    if ($counter>20) {
        print "\n";
    }

    return (!$is_restore && $ignoreErrors) ? true : empty($myerr);
}

function query_upload_error($myerr, $ignoreErrors)
{
    if (empty($myerr)) {
        echo "\n";
        echo '<font color="green">[OK]</font>' . "\n";

    } elseif ($ignoreErrors) {
        echo '<font color="blue">[NOTE: ' . $myerr . ']</font>' . "\n";

    } else {
        echo '<font color="red">[FAILED: ' . $myerr . ']</font>' . "\n";
    }
}

/**
* Generates a code consisting of $length characters from the set [A-Z0-9].
* Used as GC & discount coupon code, as well as installation auth code, etc.
*/
function generate_code($length = 8)
{
    $salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    srand(microtime(true) * 1000000);
    $i = 0;

    $code = '';
    while ($i < $length) {
        $num = rand() % 35;
        $tmp = substr($salt, $num, 1);
        $code = $code . $tmp;
        $i++;
    }

    return $code;
}

/**
* Strips slashes and trims the specified array values 
* (strips from strings only)
*
* @access private
* @param  array $array The array to strip slashes
*/
function func_strip_slashes(&$array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            func_strip_slashes($array[$key]);

        } elseif (is_string($value)) {
            $array[$key] = trim(stripslashes($value));
        }
    }
}

function func_htmldecode($encoded)
{
    return strtr($encoded, array_flip(get_html_translation_table(HTML_ENTITIES)));
}

function func_starts_with($str, $start)
{
    return 0 === strncmp($str, $start, strlen($start));
}

//
// This function create file lock in temporaly directory
// It will return file descriptor, or false.
//
function func_lock($lockname, $ttl = 15, $cycle_limit = 0)
{
    global $_lock_hash;

    $options = XLite::getInstance()->getOptions(); 

    if (empty($lockname)) {
        return false;
    }

    if (!empty($_lock_hash[$lockname])) {
        return $_lock_hash[$lockname];
    }

    $lockDir = $options["decorator_details"]["lockDir"];
    // remove last '/'
    if ($lockDir{strlen($lockDir) - 1} == '/') {
        $lockDir = substr($lockDir, 0, strlen($lockDir)-1);
    }
    if (!is_dir($lockDir)) {
        \Includes\Utils\FileManager::mkdirRecursive($lockDir);
    }
    $fname = $lockDir."/".$lockname.".lock";

    // Generate current id
    $id = md5(uniqid(rand(0, substr(floor(microtime(true) * 1000), 3)), true));
    $_lock_hash[$lockname] = $id;

    $file_id = false;
    $limit = $cycle_limit;
    while (($limit-- > 0 || $cycle_limit <= 0)) {
        if (!file_exists($fname)) {

            # Write locking data
            $fp = @fopen($fname, "w");
            if ($fp) {
                @fwrite($fp, $id.time());
                fclose($fp);
            }
        }

        $fp = @fopen($fname, "r");
        if (!$fp)
            return false;

        $tmp = @fread($fp, 43);
        fclose($fp);

        $file_id = substr($tmp, 0, 32);
        $file_time = (int) substr($tmp, 32);

        if ($file_id == $id)
            break;

        if ($ttl > 0 && time() > $file_time+$ttl) {
            @unlink($fname);
            continue;
        }

        sleep(1);
    }

    return $file_id == $id ? $id : false;
}

//
// This function releases file lock which is previously created by func_lock
//
function func_unlock($lockname) {
    global $_lock_hash;       

    $options = XLite::getInstance()->getOptions(); 

    if (empty($lockname) || empty($_lock_hash[$lockname])) {
        return false;
    }

    $lockDir = $options["decorator_details"]["lockDir"];
    // remove last '/'
    if ($lockDir{strlen($lockDir)-1} == '/') {
        $lockDir = substr($lockDir, 0, strlen($lockDir)-1);
    }
    if (!is_dir($lockDir)) {
        \Includes\Utils\FileManager::mkdirRecursive($lockDir);
    }
    $fname = $lockDir."/".$lockname.".lock";
    if (!file_exists($fname)) {
        return false;
    }

    $fp = fopen($fname, "r");
    if (!$fp) {
        return false;
    }

    $tmp = fread($fp, 43);
    fclose($fp);

    $file_id = substr($tmp, 0, 32);
    $file_time = (int) substr($tmp, 32);

    if ($file_id == $_lock_hash[$lockname]) {
        @unlink($fname);
    }

    unset($_lock_hash[$lockname]);

    return true;
}

//
// This function checks, whether the lock is active
//
function func_is_locked($lockname, $ttl = 15) {
    global $_lock_hash;        

    $options = XLite::getInstance()->getOptions();

    if (empty($lockname)) {
        return false;
    }

    $lockDir = $options["decorator_details"]["lockDir"];
    // remove last '/'
    if ($lockDir{strlen($lockDir)-1} == '/') {
        $lockDir = substr($lockDir, 0, strlen($lockDir)-1);
    }
    $fname = $lockDir."/".$lockname.".lock";
    if (!file_exists($fname)) {
        if (!file_exists($fname)) {
            return false;
        }
    }

    $fp = fopen($fname, "r");
    if (!$fp) {
        return false;
    }

    $tmp = fread($fp, 43);
    fclose($fp);

    $file_id = substr($tmp, 0, 32);
    $file_time = (int) substr($tmp, 32);

    if ($ttl > 0 && time() > $file_time+$ttl) {
        @unlink($fname);
        return false;
    }

    return true;
}

function func_parse_csv($line, $delimiter, $q, &$error) {
    $line = trim($line);
    if (empty($q)) {
        return explode($delimiter, $line);
    }

    $arr = array();
    $state = "outside";
    $field = "";
    $error = "";
    for ($i=0; $i<=strlen($line); $i++) {
        if ($i==strlen($line)) $char = "EOL";
        else $char = $line{$i};
        if ($state == "outside") {
            if ($char == $q) {
                $state = "inside";
                $field = "";
            } elseif ($char == $delimiter || $char == "EOL") {
                // empty field
                $arr[] = "";
            } else {
                $state = "field";
                $field = $char;
            }
        } elseif ($state == "inside") {
            if ($char == $q) {
                $state = "quote inside";
            } else if ($char == "EOL") {
                $error = "Unexpected end of line; $q expected";
                return null;
            } else {
                $field .= $char;
            }
        } elseif ($state == "quote inside") {
            if ($char == $q) { // double-quote
                $state = "inside";
                $field .= $q;
            } elseif ($char == $delimiter || $char == "EOL") {
                $arr[] = $field;
                $state = "outside";
            } else {
                $error = "Unexpected character $char outside quotes: $q expected (pos $i)";
                return null;
            }
        } elseif ($state == "field") {
            if ($char == $delimiter || $char == "EOL") {
                $state = "outside";
                $arr[] = $field;
            } else {
                $field .= $char;
            }
        }
    }
    return $arr;
}

function func_construct_csv($fields, $delimiter, $q) {
    $test = '';
    $fs = array();
    foreach ($fields as $f) {
        if (empty($q)) {
            $fs[] = strtr($f, "\n\r", "  ");

        } else {
            $fs[] = $q . strtr(str_replace($q, $q . $q, $f), "\n\r", "  ").$q;
        }
    }
    return implode($delimiter, $fs);
}

function func_convert_to_byte($file_size) { 
    $val = trim($file_size);
    $last = strtolower(substr($val, -1));

    switch ($last) {
        case 'g':
            $val *= 1024;

        case 'm':
            $val *= 1024;

        case 'k':
            $val *= 1024;
    }

    return $val;
}

function func_check_memory_limit($current_limit, $required_limit) { 
    $limit = func_convert_to_byte($current_limit);
    $required = func_convert_to_byte($required_limit);
    if ($limit < $required) {
        # workaround for http://bugs.php.net/bug.php?id=36568
        if (LC_OS_IS_WIN && version_compare(phpversion(), '5.1.0') < 0) {
            return true;
        }

        @ini_set('memory_limit', $required_limit);
        $limit = @ini_get('memory_limit');
        return 0 === strcasecmp($limit, $required_limit);
    }

    return true; 
}

function func_set_memory_limit($new_limit) { 
    $current_limit = @ini_get('memory_limit');

    return func_check_memory_limit($current_limit, $new_limit);
}

function func_is_timezone_changable() {
    return function_exists('date_default_timezone_set') && class_exists('DateTimeZone');
}

function func_get_timezone() {
    return function_exists('date_default_timezone_get') ? @date_default_timezone_get() : null;
}

function func_get_timezones() {
    return class_exists('DateTimeZone') ? DateTimeZone::listIdentifiers() : null;
}

function func_htmlspecialchars($str) {
    $str = preg_replace(
        '/&(?!(?:amp|#\d+|#x\d+|euro|copy|pound|curren|cent|yen|reg|trade|lt|gt|lte|gte|quot);)/Ss',
        '&amp;',
        $str
    );

    return str_replace(
        array('"', '\'', '<', '>'),
        array('&quot;', '&#039;', '&lt;', '&gt;'),
        $str
    );
}

/**
 * Check if LiteCommerce installed
 * 
 * @return bool
 * @since  3.0
 */
function isLiteCommerceInstalled()
{
    $checkResult = file_exists(LC_SKINS_DIR . 'admin/en/welcome.tpl')
        && file_exists(LC_CONFIG_DIR . 'config.php');

    if ($checkResult) {

        $data = XLite::getInstance()->getOptions('database_details');

        if (is_array($data)) {
            $checkResult = !empty($data['hostspec'])
                && !empty($data['database'])
                && !empty($data['username']);

            if ($checkResult) {

                if (!empty($data['socket'])) {
                    $host = $data['hostspec'] . ':' . $data['socket'];

                } elseif (!empty($data['port'])) {
                    $host = $data['hostspec'] . ':' . $data['port'];

                } else {
                    $host = $data['hostspec'];
                }

                $checkResult = @mysql_connect($host, $data['username'], $data['password']) 
                    && @mysql_select_db($data['database']);

                if ($checkResult) {
                    if ($res = @mysql_query('SELECT login from xlite_profiles LIMIT 1')) {
                        $data = mysql_fetch_row($res);
                        $checkResult = !empty($data[0]);

                    } else {
                        $checkResult = false;
                    }
                }
            }
        }
    }
    
    return $checkResult;
}
