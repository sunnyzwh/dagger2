<?php

class QueueTaskCtl {

    const dbHost = 'localhost:3360';

    const dbUser = 'root';
    
    const dbPass = '';

    const dbName = 'dbName';

    const mcServer = 'mc.cronserver.com:7699';//muti: 10.54.40.21:7600 10.210.227.172:7600

    static private $link;

    static private $key;
    
    static private $logId;

    static private $status = false;

    public static function begin() {
        if (self::$status === true) {
            return;
        }
        $_SERVER['SERVER_ADDR'] = self::getIp();
        $project_id = PROJECT_ID;
        array_shift($_SERVER['argv']);
        $path = $_SERVER['PWD'] . "/" . $_SERVER['SCRIPT_NAME'] . " " . implode(" ", $_SERVER['argv']);
        self::$key = md5('project_id:'.$project_id .'path:'. $path);
        //mc 50s锁控制
        $mc = new Memcache;
        $serverArr = explode (" ", self::mcServer);
        foreach ($serverArr as $v) {
            if (empty($v)) {
                continue;
            }
            list($server,$port) = explode(":",$v);
            $mc->addServer($server, $port);
        }
        $rs = $mc->add(self::$key, 1, 0, 50);//锁定50s，避免两台服务器时间差导致重复运行
        if ($rs === false) {
            echo date("[Y-m-d H:i:s]")."get mc lock fail\n";
            if ($mc->getVersion() === false) {
                //MC异常
                $url = "http://".DAGGER_ALARM_URL."/alarm.php?pid=".PROJECT_ID."&key=".PROJECT_KEY."&sys_mid=4&code=100&message=".urlencode("队列MC连接异常,可能影响所有后台任务执行")."&name=".urlencode("队列机MC异常");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_URL, $url);
                //$result = curl_exec($ch);
                echo date("[Y-m-d H:i:s]").$url."\n";
                curl_close($ch);
            }
            exit;
        }
        echo date("[Y-m-d H:i:s]")."get mc lock succ\n";
        //数据库锁
        self::query("INSERT INTO `queue_runtime` (`project_id`,`task`,`begin_time`,`key`,`ip`) VALUES ('{$project_id}','{$path}','".time()."','".self::$key."','".self::getIp()."')");
        $rows = mysql_affected_rows(self::$link);
        var_dump($rows);
        if ($rows === 1) {
            self::query("INSERT INTO `queue_task_log` (`project_id`,`task`,`begin_time`,`ip`) VALUES ('{$project_id}','{$path}','".time()."','".self::getIp()."')");
            self::$logId = mysql_insert_id(self::$link);
            echo date("[Y-m-d H:i:s]")."succ run\n";
            self::$status = true;
            return true;
        } elseif ($rows === false) {
            //故障时候短信报警，不接入监控大厅，应为出故障的库就是监控大厅的库
            $rs = $mc->add(self::$key, 1, 0, 60);
            if ($rs === true) {
                // 需要修改
                $url = 'http://mix.sina.com.cn/guard/***user=abc&password=abc&phone=';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_URL, $url);
                //$result = curl_exec($ch);
                echo date("[Y-m-d H:i:s]").$url."\n";
                curl_close($ch);
            }
        }
        exit;
    }

    /*
     * @param $status 程序运行状态，正确运行为0，错误为1
     * @param $log 运行完成后的log信息，主要收集报错信息
     */
    public static function end($status = 0, $log = '') {
        if (self::$status === true) {
            if ($status != 0) {
                $url = 'http://'.DAGGER_ALARM_URL.'/alarm.php?pid='.PROJECT_ID.'&key='.PROJECT_KEY."&sys_mid=8&code=0&message={$_SERVER['SCRIPT_NAME']}运行出错&name=队列异常";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                curl_close($ch);
	    }
            $status = intval($status);
            $log = addslashes($log);
            self::query("UPDATE `queue_task_log` SET `end_time`= '".time()."',`status`='{$status}',`log`='{$log}' WHERE `id` = '".self::$logId."'");
            self::query("DELETE FROM `queue_runtime` WHERE `key` = '".self::$key."'");
            self::$status = false;//用于控制老项目和dagger外引重复定义shutdown
        }
    }

    static private function query($sql) {
        if (!self::$link) {
            echo date("[Y-m-d H:i:s]")."connect db\n";
            self::$link = mysql_connect(self::dbHost, self::dbUser, self::dbPass);
            mysql_select_db(self::dbName, self::$link);
        }
        $rs = mysql_query($sql, self::$link);
        if (!$rs && mysql_errno(self::$link) != 1062) {
            echo date("[Y-m-d H:i:s]")."re connect db\n";
            self::$link = mysql_connect(self::dbHost, self::dbUser, self::dbPass, true);
            mysql_select_db(self::dbName, self::$link);
            $rs = mysql_query($sql, self::$link);
            var_dump($rs);
        }
        echo date("[Y-m-d H:i:s]").$sql . "\n";
    }

    static private function getIp() { 
        exec("/sbin/ifconfig | awk -F: '/inet addr/{split($2,a,\" \");print a[1];}'", $arr);
        return $arr[0];
    }
}
/*
example
QueueTaskCtl::begin();
sleep(10);
QueueTaskCtl::end(1,'abcd');
*/
