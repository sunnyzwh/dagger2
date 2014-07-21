<?php
/**
 * All rights reserved.
 * LOG基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
*/

class BaseModelLog {

    const ERROR_MODEL_ID_DB = 1;        //数据库错误
    const ERROR_MODEL_ID_HTTP = 2;      //http错误
    const ERROR_MODEL_ID_S3 = 3;        //文件存储错误
    const ERROR_MODEL_ID_USER = 4;      //用户自定义错误
    const ERROR_MODEL_ID_MC = 5;        //memcache错误
    const ERROR_MODEL_ID_PHP = 6;       //php错误
    const ERROR_MODEL_ID_DEFAULT = 7;   //dagger框架错误
    const ERROR_MODEL_ID_QUEUE = 8;     //命令行模式错误
    const ERROR_MODEL_ID_PAY = 9;       //支付错误

    protected static $codeName = array(
        90100 => '请求源不允许',
        90101 => '请求方法不允许，框架只允许get或post',
        90102 => 'Controller类中方法为基类方法不能使用',
        90103 => 'Controller类中不存在调用的方法',
        90104 => 'Warning错误',
        90105 => 'Fatal error错误',
        90106 => '脚本运行超默认上限',
        90107 => 'model类名冲突',
        90108 => '模版文件未找到',
        90200 => '找不到对应route信息，请查看config/RouteConfig.php配置',
        90201 => '使用createUrl时未指定controller',
        90202 => '使用createUrl时未指定action',
        90203 => 'delUrlParams函数缺少params参数',
        90204 => 'addUrlParams函数缺少params参数',
        90205 => '没有设置默认controller',
        90206 => '没有设置默认action',
        90207 => '指定action参数时必须指定controller参数',
        90300 => '请求方法不允许，主库操作必须为post',
        90301 => '数据库返回非资源',
        90302 => 'insert或replace或insertOnDuplicate中inser_value或replace_value传参错误',
        90303 => 'insertOnDuplicate中update_keys参数在insert_value中不存在',
        90304 => 'insertOnDuplicate中update_keys参数无有效字段',
        90305 => 'update中update_value传参错误',
        90306 => 'update中where条件错误',
        90307 => 'delete中where条件错误',
        90308 => '字段在配置文件中未定义',
        90309 => '字段在配置文件中禁止修改',
        90310 => '字段值不符合在配置文件定义的类型',
        90311 => '数据库连接失败',
        90312 => 'sql不能为空',
        90313 => '传参不符合拼接规范，无法正确翻译sql语句',
        90314 => '数据库连接超过默认时间',
        90320 => '数据库基类方法不存在',
        90400 => '服务器没有安装curl扩展',
        90401 => '页面抓取请求url缺失',
        90402 => 'url参数错误',
        90403 => '请求连续20次失败',
        90404 => 'CURL内部错误信息',
        90405 => 'http异常code',
        90500 => 'MC无法连接',
        90501 => 'MC连接超过默认时间',
        90600 => 'Redis连接失败',
        90601 => 'Redis操作失败',
        90700 => '请指定有效上传文件',
        90701 => '请设置文件的mime type',
        90702 => '上传失败',
        90703 => '上传失败',
        90704 => '其他错误',
        90800 => '邮件发送失败',
        90900 => 'SAE不支持追加写入操作',
        91000 => '指定开关不存在',
        91100 => 'XML解析错误',
    );

    //用于控制单一脚本中的重复报警问题
    protected static $monitorStatus = array();

    //log文件路径
    protected static $logFilePath = 'admin_log/log';

    /**
     * 日志写入文件路径
     * @param string $path 文件路径
     * @return void
     */
    public static function setLogFilePath($path)  {
        self::$logFilePath = $path;
    }

    /**
     * 日志写入，默认写入文件
     * 各自项目可以创建数据表继承重写本函数
     * @param $user     操作者
     * @param $ip       操作IP
     * @param $pk       操作数据主键
     * @param $action   操作动作
     * @param $status   操作结果状态 0|1
     * @param $desc     描述
     * @return void
     */
    public static function write($user, $ip, $pk, $action, $status, $desc = '') {
        $time = date("Y-m-d H:i:s");
        $str = $time . "\t" . $user . "\t" . $ip . "\t" . $pk . "\t" . $action . "\t" . $status . "\t" . $desc . "\n";
        $file = new BaseModelFile(self::$logFilePath, 'log');
        $file->writeTo($str);
    }

    /**
     * 将数组转为字符串记录日志使用
     * @param $arr array 需要记录日志的数组
     * @return string
     */
    public static function arrayToLog($arr) {
        $str = '';
        if (is_array($arr)) {
            foreach ($arr as $k=>$v) {
                $str .= "[{$k}]:" . $v . " ";
            }
        }
        return $str;
    }

    /**
     * 向监控大厅提交报警
     * @param int $code 报警错误号
     * @param string $msg 报警消息
     * @param string $name 报警类型名称
     * @param int $sysMid 系统错类型
     * @return void
     */
    public static function sendLog($code, $msg, $name = '', $sysMid = self::ERROR_MODEL_ID_USER){
        if (defined('DAGGER_ALARM_LOG_URL')) {
            isset(self::$codeName[$code]) && $name === '' && $name = self::$codeName[$code];
            $key = md5("{$code}{$msg}{$name}{$sysMid}");
            if (isset(self::$monitorStatus[$key]) && self::$monitorStatus[$key] === true) {
                return true;
            } else {
                self::$monitorStatus[$key] = true;
            }
            $script = isset($_SERVER['HTTP_HOST']) ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : implode(' ', $_SERVER['argv']);
            BaseModelHttp::sendRequest(
                DAGGER_ALARM_LOG_URL,
                array(
                    'pid'       => PROJECT_ID,
                    'key'       => PROJECT_KEY,
                    'sys_mid'   => $sysMid,
                    'code'      => $code,
                    'message'   => $msg,
                    'script'    => $script,
                    'name'      => $name,
                    'startTime' => STARTTIME,
                    'client_ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0'),
                    'xhprof_id' => (defined('DAGGER_XHPROF_ID') ? DAGGER_XHPROF_ID : 0)
                )
            );
        }
    }

    /**
     * 临时日志
     * @param string $type 类型
     * @param string $log 日志内容
     * @return void
     */
    public static function sendTmpLog($type, $log){
        if(defined('DAGGER_ALARM_TMPLOG_URL')) {
            BaseModelHttp::sendRequest(DAGGER_ALARM_TMPLOG_URL, array('type'=>$type, 'log'=>$log));
        }
    }
}
