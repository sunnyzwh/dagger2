<?php
/**
 * All rights reserved.
 * 数据操作DB扩展类，负责连接数据库。
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
 */

class BaseModelDBConnect
{
    static private $links = array();//数据库连接
    static private $linkConfig = array();

    private function __construct()
    {
        return;
    }

    /**
     * 连接数据库，返回连接上的PDO对象
     * @param int $DBName    数据库名称
     * @param string $master_or_slave   master;主库|slave:从库
     * @return master db handle;
     */
    public static function connectDB($DBName, $master_or_slave = 'slave', $DBConfig = array(), $reConnect = false) {
        $master_or_slave === 'master' || $master_or_slave = 'slave';
        if ($master_or_slave === 'master' && !empty($_SERVER['HTTP_HOST'])
            //防止CSRF
            && isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST'
            && BaseModelSwitch::check(BaseModelSwitch::SWITCH_MASTERDB_POST_ONLY) === true) {
            throw new BaseModelDBException('请求方法不允许，主库操作必须为post', 90300);
        }
        $DBType = DAGGER_ENV;
        in_array($DBType, array('dev', 'test', 'product'), true) || $DBType = 'product';
        empty($DBName) && $DBName = DAGGER_DB_DEFAULT;

        if (empty(DBConfig::$config['mysql'][$DBName][$DBType]['master']['host'])) {
            $DBType = 'product';
        }

        $username = empty($DBConfig[$master_or_slave]['user']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['user'] : $DBConfig[$master_or_slave]['user'];
        $password = empty($DBConfig[$master_or_slave]['pass']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['pass'] : $DBConfig[$master_or_slave]['pass'];
        $hostspec = empty($DBConfig[$master_or_slave]['host']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['host'] : $DBConfig[$master_or_slave]['host'];
        $port = !isset($DBConfig[$master_or_slave]['port']) || !is_numeric($DBConfig[$master_or_slave]['port']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['port'] : $DBConfig[$master_or_slave]['port'];
        $database = empty($DBConfig[$master_or_slave]['database']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['database'] : $DBConfig[$master_or_slave]['database'];
        $charset = empty($DBConfig['charset']) ? DBConfig::$config['mysql'][$DBName][$DBType][$master_or_slave]['charset'] : strtolower($DBConfig['charset']);
        $db_key = md5(implode('-', array($hostspec, $port, $username, $database, $charset)));
        self::$linkConfig = array('host' => $hostspec, 'port' => $port, 'db' => $database, 'charset' => $charset);

        if (isset(self::$links[$db_key]) && !$reConnect) {
            return self::$links[$db_key];
        }

        // self::$links[$db_key] = new PDO($dsn, $username, $password);
        $dsn = "mysql:dbname=$database;port=$port;host=$hostspec";
        $connectType = $reConnect ? 'db_reconnect' : 'db_connect';
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($dsn."|username:$username|pw:***", $connectType);
        $mysqli = mysqli_init();
        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 4);
        if ($mysqli->real_connect($hostspec, $username, $password, $database, $port)) {
            $mysqli->set_charset($charset);
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($charset, "db_set_charset");
            self::$links[$db_key] = $mysqli;
            return self::$links[$db_key];
        } else {
            return false;
        }
    }

    /**
     * 关闭数据库连接
     * @return voild;
     */
    public static function close_db(&$dbh) {
        if($dbh) {
            @mysqli_close($dbh);
        }
    }

    /**
     * 获取当前数据库连接信息
     * @return voild;
     */
    public static function getLinkInfo() {
        return self::$linkConfig;
    }
}
