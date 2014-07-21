<?php
/**
 * All rights reserved.
 * 示例程序
 * @author          wangxin <wangxin3@staff.sina.com.cn>
 * @time            2011/3/2 15:03
 * @version         Id: 0.9
*/

require(dirname(__FILE__) . "/global.php");
/*
$mcd = new MyMemcached();
$urls = array('http://baidu.com', 'http://www.google.com.hk', 'http://search.sina.com.cn');
$r = Http::multiHeader($urls);
// var_dump($r);
exit;
*/
        $db = new BaseModelDB();
        $rs = $db->getData("SELECT * FROM `test`", 10);
        exit;




$starttime = microtime(true);
$mcd = new MyMemcached();
$a = array();
for($i = 0;$i < 100;$i++) {
    $a['key' . $i] = $i;
}
$mcd->setMulti($a,10);
$t1 = (microtime(true) - $starttime);

$starttime = microtime(true);
$mc = new MyMemcache();
for($i = 0;$i < 100;$i++) {
    $a['key' . $i] = $i;
    $mc->set('key' . $i, $i, 10);
}
$t2 = (microtime(true) - $starttime);
var_dump($t1, $t2, $t2 - $t1);
exit;

var_dump($mc->get('test'));
var_dump($mcd->setMulti('test',1, 5));
var_dump($mc->getMulti(array('test')));
sleep(5);
var_dump($mc->get('test'));
var_dump($mc->getMulti(array('test')));

exit;
Log::sendLog(100, "[{$absDataPath}]看了还看数据推送异常。联系zhixiong1@，6883，15810834486。");
exit;

echo 1;
sleep(30);
$mc = new MyMemcache;
var_dump($mc->add("aa", 1, 10));
var_dump($mc->add("aa", 1, 10));
var_dump($mc->get("aa"));
var_dump($mc->delete("aa"));
var_dump($mc->get("aa"));
var_dump($mc->add("aa", 1, 10));
