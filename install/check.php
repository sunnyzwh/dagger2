<?php
/**
 * All rights reserved.
 * 环境检测
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/9 17:24
 * @version         Id: 0.9
*/
define('DAGGER_PATH_ROOT', rtrim(dirname(__FILE__), '/') . '/../');

include(DAGGER_PATH_ROOT . "config/SysInitConfig.php");
include(DAGGER_PATH_ROOT . "libs/DaggerSysInitConfig.php");
include(DAGGER_PATH_CONFIG . "DBConfig.php");
if (DAGGER_PLATFORM == 'sae') {
    $s = new SaeStorage();
    if ($s->write( 'data' , '_test.test' , 'test!' )) {
        $str .= '<div class="text_green">storage-domian检测成功</div>';
        $s->delete('data', '_test.test');
    } else {
        $str .= '<div class="text_red">需要创建一个storage-domian：data</div>';
    }
    if (@file_put_contents( 'saemc://_test.test' , 'test!')) {
        $str .= '<div class="text_green">Memcache检测成功</div>';
    } else {
        $str .= '<div class="text_red">需要开启SAE的Memcache</div>';
    }
    

} else {
    $str = '<div>基本环境检测：</div>';
    //PHP版本检测
    if (substr(phpversion(), 0, 1) < 5) {
        $str .= '<div class="text_red">PHP版本检测失败，PHP版本必须 >= 5...</div>';
    } else {
        $str .= '<div class="text_green">PHP版本检测成功，当前版本：'.phpversion().'...</div>';
    }
    //magic_quotes_gpc
    if (get_magic_quotes_gpc()) {
        $str .= '<div class="text_red">magic_quotes_gpc扩展检测失败，magic_quotes_gpc必须关闭...</div>';
    } else {
        $str .= '<div class="text_green">magic_quotes_gpc扩展检测通过，当前状态：关闭...</div>';
    }
    $str .= '<div>所需扩展检测：</div>';
    //mysqli
    if (!extension_loaded('mysql')) {
        $str .= '<div class="text_red">mysqli扩展检测失败，mysqli扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">mysqli扩展检测通过，当前状态：已安装...</div>';
    }
    //json
    if (!extension_loaded('json')) {
        $str .= '<div class="text_red">json扩展检测失败，json扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">json扩展检测通过，当前状态：已安装...</div>';
    }
    //curl
    if (!extension_loaded('curl')) {
        $str .= '<div class="text_red">curl扩展检测失败，curl扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">curl扩展检测通过，当前状态：已安装...</div>';
    }
    //SimpleXML
    if (!extension_loaded('SimpleXML')) {
        $str .= '<div class="text_red">SimpleXML扩展检测失败，SimpleXML扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">SimpleXML扩展检测通过，当前状态：已安装...</div>';
    }
    //mbstring
    if (!extension_loaded('mbstring')) {
        $str .= '<div class="text_red">mbstring扩展检测失败，mbstring扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">mbstring扩展检测通过，当前状态：已安装...</div>';
    }
    //memcached
    if (!extension_loaded('memcached')) {
        $str .= '<div class="text_red">memcached扩展检测失败，memcached扩展未安装...</div>';
    } else {
        $str .= '<div class="text_green">memcached扩展检测通过，当前状态：已安装...</div>';
    }
    //环境变量可用性检测
    $str .= '<div>环境变量可用性检测：</div>';
    if (!is_dir(DAGGER_PATH_DATA)) {
        $str .= '<div class="text_red">数据目录检测失败，当前设置：'.DAGGER_PATH_DATA.'不为目录</div>';
    } else {
        $str .= '<div class="text_green">数据目录检测成功，当前设置：'.DAGGER_PATH_DATA.'...</div>';
    }
    if (!is_dir(DAGGER_PATH_CACHE)) {
        $str .= '<div class="text_red">缓存数据目录检测失败，当前设置：'.DAGGER_PATH_CACHE.'不为目录</div>';
    } else {
        $str .= '<div class="text_green">缓存数据目录检测成功，当前设置：'.DAGGER_PATH_CACHE.'...</div>';
    }
    if (!is_dir(DAGGER_PATH_APPLOG)) {
        $str .= '<div class="text_red">日志目录检测失败，当前设置：'.DAGGER_PATH_APPLOG.'不为目录</div>';
    } else {
        $str .= '<div class="text_green">日志目录检测成功，当前设置：'.DAGGER_PATH_APPLOG.'...</div>';
    }

    try {
        $dsn = "mysql:dbname=".DBConfig::$config['mysql']['default']['product']['master']['database'].";port=".DBConfig::$config['mysql']['default']['product']['master']['port'].";host=".DBConfig::$config['mysql']['default']['product']['master']['host'].";user=".DBConfig::$config['mysql']['default']['product']['master']['user'].";pass=".DBConfig::$config['mysql']['default']['product']['master']['pass'];
        $db = @mysqli_connect( DBConfig::$config['mysql']['default']['product']['master']['host'] , DBConfig::$config['mysql']['default']['product']['master']['user'] , DBConfig::$config['mysql']['default']['product']['master']['pass'] , DBConfig::$config['mysql']['default']['product']['master']['database'] , DBConfig::$config['mysql']['default']['product']['master']['port']);
    } catch (Exception $e) {}
    if ($db) {
        $str .= '<div class="text_green">数据库主库检测成功，主库DB设置为：'.$dsn.'...</div>';
    } else {
        $str .= '<div class="text_red">数据库主库检测失败，主库当前DB设置：'.$dsn.'，链接失败</div>';
    }
    try {
        $dsn = "mysql:dbname=".DBConfig::$config['mysql']['default']['product']['slave']['database'].";port=".DBConfig::$config['mysql']['default']['product']['slave']['port'].";host=".DBConfig::$config['mysql']['default']['product']['slave']['host'].";user=".DBConfig::$config['mysql']['default']['product']['slave']['user'].";pass=".DBConfig::$config['mysql']['default']['product']['slave']['pass'];
        $db_r = @mysqli_connect( DBConfig::$config['mysql']['default']['product']['slave']['host'] , DBConfig::$config['mysql']['default']['product']['slave']['user'] , DBConfig::$config['mysql']['default']['product']['slave']['pass'] , DBConfig::$config['mysql']['default']['product']['slave']['database'] , DBConfig::$config['mysql']['default']['product']['slave']['port']);
    } catch (Exception $e) {}
    if ($db_r) {
        $str .= '<div class="text_green">数据库从库检测成功，从库DB设置为：'.$dsn.'...</div>';
    } else {
        $str .= '<div class="text_red">数据库从库检测失败，从库当前DB设置：'.$dsn.'，链接失败</div>';
    }
    try {
        $rs = false;
        $mc = new Memcache();
        $serverArr = explode (" ", DBConfig::$config['memcache']['default']['servers']);
        foreach ($serverArr as $v) {
            list($server, $port) = explode(":", $v);
            if (empty($server)) {
                continue;
            }
            $rs = $mc->addServer($server, $port);
        }
        $rs = @$mc->set('_test_a', '1');
    } catch (Exception $e) {}
    if ($rs) {
        $str .= '<div class="text_green">Memcache检测成功，当前设置为：'.DBConfig::$config['memcache']['default']['servers'].'...</div>';
    } else {
        $str .= '<div class="text_red_1">Memcache检测失败，当前设置为：“'.DBConfig::$config['memcache']['default']['servers'].'”，连接失败，如不用MC缓存对系统无影响</div>';
    }

    if (is_dir(DAGGER_PATH_DATA) && @file_put_contents(DAGGER_PATH_DATA . '_test.test', '') !== false) {
        $str .= '<div class="text_green">数据文件目录可用性检测成功，当前数据目录设置为：'.DAGGER_PATH_DATA.'...</div>';
        unlink(DAGGER_PATH_DATA . '_test.test');
    } else {
        $str .= '<div class="text_red">数据文件目录可用性检测失败，当前数据目录设置为：“'.DAGGER_PATH_DATA.'”，如目录设置正确，请检查目录权限</div>';
    }
    if (is_dir(DAGGER_PATH_CACHE) && @file_put_contents(DAGGER_PATH_CACHE . '_test.test', '') !== false) {
        $str .= '<div class="text_green">cache文件目录可用性检测成功，当前缓存数据目录设置为：'.DAGGER_PATH_CACHE.'...</div>';
        unlink(DAGGER_PATH_CACHE . '_test.test');
    } else {
        $str .= '<div class="text_red">cache文件目录可用性检测失败，当前缓存数据目录设置为：“'.DAGGER_PATH_CACHE.'”，如目录设置正确，请检查目录权限</div>';
    }
    if (is_dir(DAGGER_PATH_APPLOG) && @file_put_contents(DAGGER_PATH_APPLOG . '._test.test', '') !== false) {
        $str .= '<div class="text_green">日志文件目录可用性检测成功，当前LOG目录设置为：'.DAGGER_PATH_APPLOG.'...</div>';
        unlink(DAGGER_PATH_APPLOG . '._test.test');
    } else {
        $str .= '<div class="text_red">日志文件目录可用性检测失败，当前LOG目录设置为：“'.DAGGER_PATH_APPLOG.'”，如目录设置正确，请检查目录权限，不记录日志无影响</div>';
    }
    if (!strpos($str, 'test_red')) {
        $str .= '<div style="font-size:20px;padding-top:20px;">强力建议安装firefox插件firephp，用于调试页面</div>';
    }

    if (!strpos($str, 'test_red')) {
        $str .= '<div style="font-size:20px;padding-top:20px;">测试通过,您可以点击<a href="./admin/index.php" style="color:#f00;">这里</a>创建管理后台DAGGER_APP</div>';
    }
}
?>
<html>
<head>
<title>项目环境监测</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove;width:800px;}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit}
.text_red {color:#FF0000;padding-left:10px;font-size:14px;}
.text_red_1 {color:#999;padding-left:10px;}
.text_green {color:#41B200;padding-left:10px;}
.text_a {color:#999;padding-left:30px;}
</style>
</head>
<body>
<div style="margin:0 auto;width:1000px;text-align:center;">
    <table align="center">
        <tr><th>项目环境监测结果</th></tr>
        <tr>
        	<td>
                <div style="width:100px;height:20px;background-color:#f00;border:solid 1px #fff;float:left;text-align:center;color:#fff;">必须处理错误</div>
                <div style="width:100px;height:20px;background-color:#999;border:solid 1px #fff;float:left;margin-left:30px;text-align:center;color:#fff;">可以或略错误</div>
                <div style="width:100px;height:20px;background-color:#41B200;border:solid 1px #fff;float:left;margin-left:30px;text-align:center;color:#fff;">测试通过</div>
    </td>
        </tr>
        <tr>
        	<td><?php echo $str?></td>
        </tr>
    </table>
</div>
</body>
</html>
