<?php
/**
 * All rights reserved.
 * @abstract        通用函数
 * @author          wangxin <wx012@126.com>
 * @since           2011/3/2 11:48
 * @version         1.0
 */
class BaseModelCommon {

    private function __construct() {}
    private function __destruct() {}
    private function __clone() {}

    protected static $onlineDebugData = array();

    public static $debugTypeFilter = null;

    /**
     * 统计信息
     */
    private static $statInfo = array(
            'mc'=>array('count'=>0, 'time'=>0),
            'db'=>array('count'=>0, 'time'=>0),
            'request'=>array('count'=>0, 'time'=>0),
            'redis'=>array('count'=>0, 'time'=>0),
            );

    /**
     * 获取统计信息
     * @param void
     * @return array
     */
    public static function getStatInfo() {
        return self::$statInfo;
    }

    /**
     * 累计统计信息
     * @param string $type 统计类型mc|db|request
     * @param int $startTime 开始时间，默认0为不统计时长
     * @param int $offset 增加的大小，默认为1
     * @return mix
     */
    public static function addStatInfo($type, $startTime = 0, $offset = 1) {
        self::$statInfo[$type]['count'] += $offset;
        if($startTime > 0) {
            $runTime = sprintf("%0.2f", (microtime(true) - $startTime) * 1000);
            self::$statInfo[$type]['time'] += $runTime;
            return $runTime . " ms";
        }
        return true;
    }

    /**
     * 二维数组根据自动字段排序
     * @param array $data 需要排序的数组
     * @param string $orderby_key 依据字段
     * @param string $type 排序方式，desc|asc
     * @return array 排序完成数组
     */
    public static function arrayOrderBy($arr, $orderby_key, $type = 'ASC') {
        $col = array();
        foreach($arr as $key => $value) {
            $col[$key] = $value[$orderby_key];
        }

        $type = (strtoupper($type) == "ASC" ? SORT_ASC : SORT_DESC);

        array_multisort($col, $type, $arr);
        return $arr;
    }

    /**
     * 转码函数
     * @param Mixed $data 需要转码的数组
     * @param String $dstEncoding 输出编码
     * @param String $srcEncoding 传入编码
     * @param bool $toArray 是否将stdObject转为数组输出
     * @return Mixed
     */
    public static function convertEncoding($data, $dstEncoding, $srcEncoding, $toArray=false) {
        if ($toArray && is_object($data)) {
            $data = (array)$data;
        }
        if (!is_array($data) && !is_object($data)) {
            $data = mb_convert_encoding($data, $dstEncoding, $srcEncoding);
        } else {
            if (is_array($data)) {
                foreach($data as $key=>$value) {
                    if (is_numeric($value)) {
                        continue;
                    }
                    $keyDstEncoding = self::convertEncoding($key, $dstEncoding, $srcEncoding, $toArray);
                    $valueDstEncoding = self::convertEncoding($value, $dstEncoding, $srcEncoding, $toArray);
                    unset($data[$key]);
                    $data[$keyDstEncoding] = $valueDstEncoding;
                }
            } else if(is_object($data)) {
                $dataVars = get_object_vars($data);
                foreach($dataVars as $key=>$value) {
                    if (is_numeric($value)) {
                        continue;
                    }
                    $keyDstEncoding = self::convertEncoding($key, $dstEncoding, $srcEncoding, $toArray);
                    $valueDstEncoding = self::convertEncoding($value, $dstEncoding, $srcEncoding, $toArray);
                    unset($data->$key);
                    $data->$keyDstEncoding = $valueDstEncoding;
                }
            }
        }
        return $data;
    }

    /**
     * 递归创建目录，SAE平台不生效
     * @param string $pathname 需要创建的目录路径
     * @param int $mode 创建的目录属性，默认为755
     * @return void
     */
    public static function recursiveMkdir($pathname, $mode = 0755) {
        if (DAGGER_PLATFORM === 'sae') {
            return false;
        } else {
            return is_dir($pathname) ? true : mkdir($pathname, $mode, true);
        }
    }

    /**
     * 返回程序开始到调用函数处的执行时间
     * @return string 运行此函数调用的时间
     */
    public static function getRunTime() {
        // 兼容老项目DAGGER_STARTTIME为microtime()
        if(strpos(DAGGER_STARTTIME, ' ')) {
            $time = explode(' ', DAGGER_STARTTIME);
            $startTime = (double)$time[1] + (double)$time[0];
            return sprintf("%0.3f", microtime(true) - $startTime) . " s";
        }
        return sprintf("%0.3f", microtime(true) - DAGGER_STARTTIME) . " s";
    }

    /**
     * 调试信息打印
     * @param mixed $value 需要打印的调试信息
     * @param string $type 需要打印的调试信息的类型，默认为：DEBUG
     * @param bool/int $verbose 是否缩略输出，默认为false，但可以制定缩略长度
     * @param string $encoding 指定传入编码
     * @return void
     */
    public static function debug($value, $type = 'DEBUG', $verbose = false, $encoding = 'UTF-8') {
        if (defined('DAGGER_DEBUG') && defined('DAGGER_ENV') && DAGGER_ENV !== 'product') {
            BaseModelDebug::debug($value, $type, $verbose, $encoding);
        }

        if (1 === DAGGER_ONLINE_DEBUG) {
            self::$onlineDebugData[$type] = $value;
        }
    }


    /**
     * online debug推送到监控中心
     * @return void
     */
    public static function sendOnlineDebug() {
        if (1 === DAGGER_ONLINE_DEBUG && defined("DAGGER_ALARM_DEBUG_API")) {
            $post = array(
                'pid' => defined('PROJECT_ID') ? PROJECT_ID : 1,
                'domain' => $_SERVER['HTTP_HOST'],
                'debug_msg' => json_encode(self::$onlineDebugData),
                'html' => ob_get_contents(),
                'request' => json_encode(apache_request_headers()),
                'response' => json_encode(apache_response_headers()),
                'client_ip' => BaseModelIp::getClientIp(),
                'remote_addr' => $_SERVER['REMOTE_ADDR']
            );
            BaseModelHttp::sendRequest(DAGGER_ALARM_DEBUG_API, $post);
        }
    }

    /**
     * 类/函数名构造
     * @param $name String 传入名
     * @param $type String 类型：function | class
     * @return string 类/函数名
     */
    public static function getFormatName($name, $type = 'function') {
        $name = array_map('ucfirst', explode('_', $name));
        if('function' === strtolower($type)) {
            $name[0] = strtolower($name[0]);
        }
        return implode('', $name);
    }


    /**
     * checkbox选框值格式化
     * @param $arr arr 传入多选数组
     * @return string 格式化后的字符串
     */
    public static function checkboxStrEncode($arr) {
        $str = '';
        if(is_array($arr) && !empty($arr)){
            $str = implode("','", $arr);
        }
        return "SELECTED:['{$str}']";
    }

    /**
     * checkbox选框值反格式化
     * @param string $str传入格式化后的字符串
     * @return array 多选数组
     */
    public static function checkboxStrDecode($str) {
        return explode("','", substr($str, 11, -2));
    }

    /**
     * 根据二维数组字段生成一维数组
     * @param array $arr 传入的二位数组
     * @param string $key 作为key的字段
     * @param string $value 作为value的字段
     * @return string 构造完成的一维数组
     */
    public static function createArr($arr, $key, $value) {
        $newArr = array();
        foreach ($arr as $v) {
            isset($v[$key]) && isset($v[$value]) && $newArr[$v[$key]] = $v[$value];
        }
        return $newArr;
    }
}
