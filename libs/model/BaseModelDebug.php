<?php
/**
 * All rights reserved.
 * @abstract        调试类
 * @author          xuyan <xuyan4@staff.sina.com.cn>
 * @since           2013/2/20 12:20
 * @version         1.0
 */

class BaseModelDebug {

    public static function debug($value, $type = 'DEBUG', $verbose = false, $encoding = 'UTF-8') {
        if (strtoupper($encoding) != 'UTF-8') {
            $value = BaseModelCommon::convertEncoding($value, 'utf-8', $encoding);
            $type = BaseModelCommon::convertEncoding($type, 'utf-8', $encoding);
        }
        // mc 信息统计
        if ((strpos($type, 'mc_') === 0) && !in_array($type, array('mc_connect'), true)) {
            BaseModelCommon::addStatInfo('mc');
        }
        //调试时正则匹配需要输出的内容
        if(null === BaseModelCommon::$debugTypeFilter) {
            BaseModelCommon::$debugTypeFilter = isset($_GET[DAGGER_DEBUG_ARG_NAME]) ? $_GET[DAGGER_DEBUG_ARG_NAME] : (isset($_COOKIE['dagger_debug_type']) ? $_COOKIE['dagger_debug_type'] : '');
        }
        $debugArgs = array_filter(explode(';', BaseModelCommon::$debugTypeFilter));
        if (empty($debugArgs)) {
            $output = true;
        } else {
            foreach ($debugArgs as $arg) {
                $output = ($arg{0} === '!') && $arg = ltrim($arg, '!');
                if (preg_match("/^{$arg}/", $type)) {
                    $output = !$output;
                    break;
                }
            }
        }
        if ($type === 'filter') {
            $output = true;
        }
        if ($output) {
            if (defined('QUEUE')) {
                self::queueOut($type, $value, $verbose);
            } elseif (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'FirePHP') !== false) {
                if ($type === 'db_sql_master' || substr($type, -4) === 'warn') {
                    FirePHP::getInstance(true)->warn($value, $type);
                } elseif (in_array($type, array('db_sql_result', 'request_return', 'request_multi_return', 'all_info', 'dagger_error_trace'), true) || strpos($type, 'redis_call_') === 0) {
                    FirePHP::getInstance(true)->table($type, $value);
                } elseif (substr($type, -5) === 'trace') {
                    FirePHP::getInstance(true)->trace($value, $type);
                } elseif (substr($type, -5) === 'error') {
                    FirePHP::getInstance(true)->error($value, $type);
                } elseif (substr($type, -4) === 'info') {
                    FirePHP::getInstance(true)->info($value, $type);
                } else {
                    FirePHP::getInstance(true)->log($value, $type);
                }
            } else {
                // 预留debug信息输出
            }
        }
    }

    public static function getXhprofUrl() {
        if (defined('DAGGER_ALARM_XHPROF_SHOW_URL') && defined('DAGGER_ALARM_XHPROF_API')) {
            static $_xhprofUrl;
            if($_xhprofUrl) {
                return $_xhprofUrl;
            }
            $data = xhprof_disable();
            $runtime = floatval(BaseModelCommon::getRunTime());
            $script = isset($_SERVER['HTTP_HOST']) ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : implode(' ', $_SERVER['argv']);
            $post = array(
                'id'        => DAGGER_XHPROF_ID,
                'data'      => serialize($data),
                'clientIp'  => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0'),
                'startTime' => STARTTIME,
                'runTime'   => $runtime,
                'script'    => $script,
                'pid'       => PROJECT_ID,
                'key'       => PROJECT_KEY
            );
            BaseModelHttp::sendRequest(DAGGER_ALARM_XHPROF_API, $post);
            $_xhprofUrl = DAGGER_ALARM_XHPROF_SHOW_URL . '?' . http_build_query(array('run' => DAGGER_XHPROF_ID, 'source' => 'xhprof'));
            return $_xhprofUrl;
        }
    }

    /**
     * 队列输出
     *
     */
    public static function queueOut($type, $msg = '', $verbose = false) {
        echo date("[Y-m-d H:i:s]") . " {$type}: ";
        if (is_array($msg)) {
            echo "\n";
            PrintArr::out($msg, $verbose);
        } else {
            echo $msg;
        }
        echo "\n";
    }

    /**
     * trace debug数据
     *
     */
    public static function  showTrace($code, $msg, $trace) {
        ob_clean();
        ob_start();
        $html = <<<OUT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>提示信息_新浪网</title>
<meta name="keywords" content="提示信息" />
<style type="text/css">
<!--
body { background-color: white; color: black; font: 9pt/11pt verdana, arial, sans-serif;}
#container { width: 1024px;margin:auto;}
#message   { width: 1024px; color: black; }
.red  {color: red;}
a:link     { font: 16pt/16pt verdana, arial, sans-serif; color: red; }
a:visited  { font: 16pt/16pt verdana, arial, sans-serif; color: #4e4e4e; }
h1 { color: #FF0000; font: 18pt "Verdana"; margin-bottom: 0.5em;}
.strong { color: #FF0000; font: 16pt "Verdana"; margin-bottom: 0.5em;}
.bg1{ background-color: #FFFFCC;}
.bg2{ background-color: #EEEEEE;}
.bg3{ background-color: #FFFFCC;color: #FF0000;}
.table {background: #AAAAAA; font: 11pt Menlo,Consolas,"Lucida Console";table-layout:fixed;word-wrap: break-word; word-break: break-all; }
.info {
    background: none repeat scroll 0 0 #F3F3F3;
    border: 0px solid #aaaaaa;
    border-radius: 10px 10px 10px 10px;
    color: #000000;
    font-size: 11pt;
    line-height: 160%;
    margin-bottom: 1em;
    padding: 1em;
}
-->
</style>
<script type="text/javascript" src="http://www.sinaimg.cn/jslib/js/jquery.1.8.2.js"></script>
<script type="text/javascript" src="http://news.sina.com.cn/deco/2012/0911/scripts/XRegExp.js"></script>
<script type="text/javascript" src="http://news.sina.com.cn/deco/2012/0911/scripts/shCore.js"></script>
<script type="text/javascript" src="http://news.sina.com.cn/deco/2012/0911/scripts/shBrushPhp.js"></script>
<link type="text/css" rel="stylesheet" href="http://news.sina.com.cn/deco/2012/0911/styles/shCoreDefault.css"/>
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    SyntaxHighlighter.highlight();
});
</script>
<div id="container">
<div class='info'>
<p>ERROR CODE</p><a href="https://github.com/wxkingstar/dagger/wiki/{$code}" target="_blank">{$code}</a>
<p>ERROR MSG</p><span class="strong">{$msg}</span>
<p>ERROR TRACE</p>{$trace}
</div>
</body>
</html>
OUT;
        print($html);
        exit;
    }
}

/**
 *
 * @abstract        队列数组输出
 */
class PrintArr
{
    static private $layer = array(0);

    public static function out($value, $verbose=false){
        ob_start();
        $arr = '';
        $deep = 0;
        $k2 = array();
        if(!empty($value) && is_array($value)) {
            foreach($value as $v) {
                if(is_array($v)) {
                    // 二维数组，第二维key一样
                    if(empty($k2)) {
                        $k2 = array_keys($v);
                    } else if($k2 !== array_keys($v)){
                        $deep = 0;
                        break;
                    }
                    foreach($v as $kk => $vv) {
                        if(is_array($vv)) {
                            $deep = 0;
                            break 2;
                        }
                    }
                    if(empty($deep)) {
                        $deep = 2;
                    } else {
                        if($deep !== 2) {
                            $deep = 0;
                            break;
                        }
                    }
                } else {
                    if(empty($deep)) {
                        $deep = 1;
                    } else {
                        if($deep !== 1) {
                            $deep = 0;
                            break;
                        }
                    }
                }
            }

            // 一维数组，二维数组(第二维key一样)走表格输出
            if($deep === 1 || $deep === 2) {
                self::t($value, $verbose);
                $arr = ob_get_contents();
            }
        }
        // 其他情况
        if(empty($arr)) {
            self::p($value, $verbose);
            $arr = ob_get_contents();
            if(function_exists('posix_isatty')){
                if(posix_isatty(STDOUT)){
                    $arr = str_replace(array('[', ']', '{', '}'), array("\033[0;32;1m[\033[0m", "\033[0;32;1m]\033[0m", "\033[0;37;44m{\033[0m", "\033[0;37;44m}\033[0m"), $arr);
                }
            }
        }
        ob_end_clean();
        echo $arr;
    }

    /**
     * 最多支持2维数组
     */
    private static function t($value, $verbose=false) {
        $width = defined('QUEUE_DEBUG_LEN') ? QUEUE_DEBUG_LEN : (is_numeric($verbose) ? $verbose : 20);
        $tableStr = '';
        // 获取第一个单元
        list($_k, $_v) = each($value);
        // 表格宽度
        $count = count($_v);
        // 表格分割线
        $tableLine = str_repeat(sprintf("+%'-{$width}s", '-'), $count+1) . "+\n";

        $tableStr .= $tableLine;
        $tableStr .= sprintf("|%{$width}s", 'key\value');

        if(is_array($_v)) {
            $_k = array_keys($_v);
            foreach($_k as $_key) {
                $tableStr .= sprintf("|%{$width}s", $_key);
            }
        } else {
            $tableStr .= sprintf("|%{$width}s", ' ');
        }
        $tableStr .= "|\n";
        reset($value);
        foreach($value as $k =>$v) {
            $tableStr .= $tableLine;
            $tableStr .= sprintf("|%{$width}s", $k);
            if(is_array($v)) {
                // 二维数组
                if(empty($v)) {
                    $len = $width;
                    $tableStr .= sprintf("|%{$len}s", '');
                } else {
                    foreach($v as $kk => $vv) {
                        $len = $width;
                        if(preg_match("/[\x7f-\xff]+/", $vv, $m)) {
                            $vv = mb_substr($vv, 0, $width/2, 'utf-8');
                            // utf8占3个字符，页面中文占2个字符，一个中文字需要补1个长度
                            $len = ceil(strlen($m[0])/3) + $width;
                        } else {
                            $vv = mb_substr($vv, 0, $width, 'utf-8');
                        }
                        $tableStr .= sprintf("|%{$len}s", $vv);
                    }
                }
            } else {
                // 一维数组
                $len = $width;
                if(preg_match("/[\x7f-\xff]+/", $v, $m)) {
                    $v = mb_substr($v, 0, $width/2, 'utf-8');
                    // utf8占3个字符，页面中文占2个字符，一个中文字需要补1个长度
                    $len = strlen($m[0])/3 + $width;
                } else {
                    $v = mb_substr($v, 0, $width, 'utf-8');
                }
                is_string($v) && strlen($v) > 2000 && $v = (substr($v, 0, 2000) . '......超长，截取2000字节');
                $tableStr .= sprintf("|%{$len}s", $v);

            }
            $tableStr .= "|\n";

        }
        $tableStr .= $tableLine;
        print($tableStr);
    }

    private static function p($value, $verbose=false) {
        if(is_array($value)){
            $i = array_pop(self::$layer);
            $i++;
            array_push(self::$layer, $i);
            foreach($value as $k=>$v) {
                for($j=1; $j<$i; $j++) {
                    echo '     ';
                }
                if(!is_array($v)) {
                    if(is_numeric($verbose)) {
                        $v = mb_substr($v, 0, $verbose, 'utf-8');
                    } else if($verbose === false) {
                        $v = mb_substr($v, 0, 30, 'utf-8');
                    }
                    echo "[{$k}]=>[{$v}]\n";
                } else {
                    echo "[{$k}]=>{\n";
                    self::p($v);
                    for($j=1; $j<$i; $j++) {
                        echo '     ';
                    }
                    echo "}\n";
                }
            }
            $i = array_pop(self::$layer);
            $i--;
            array_push(self::$layer, $i);
        }
    }
}
