<?php
/**
 * All rights reserved.
 * @abstract        数据操作基类
 * @author          wangxin <wx012@126.com>
 * @time            2012/7/6 22:14
 * @editer          shuoshi@ xuyan4@
 * @version         Id: 1.0
 */

class BaseModelDB {
    /**
     * 指定数据库配
     * @var array
     */
    protected $DBConfig = array();

    /**
     * 数据库名
     * @var string
     */
    protected $DBName;

    /**
     * 数据库连接资源描述符
     * @var resource
     */
    protected $link = null;

    /**
     * 最后一次执行的查询语句
     * @var string
     */
    protected $sql;

    /**
     * 数据库返回数据集行数
     * @var int
     */
    protected $countNum;

    /**
     * 是否开启调试模式
     * @var bool
     */
    protected $debug = null;

    /**
     * 禁止更改字段
     * @var array
     */
    protected $disableField = array();

    /**
     * 可忽略的语句执行错误
     * @var array
     */
    protected $ignoreErrorArr = array();

    /**
     * 数据表表名
     * @var string
     */
    protected $tableName;

    /**
     * 翻页实例
     * @var object
     */
    public $pageModel;

    /**
     * 翻页参数名
     * @var string
     */
    public $pName = 'page';

    /**
     * mc缓存实例
     * @var object
     */
    public $mcd;

    /**
     * mc缓存时间
     * @var int
     */
    protected $cacheTimeout = 120;

    /**
     * 语句执行时间
     * @var int
     */
    protected $runTime = 0;

    /**
     * 是否在事务中
     * @var bool
     */
    static protected $inTransaction = false;

    /**
     * 需要重连接的错误代码
     2006 MySQL server has gone away              mysql服务器主动断开
     2013 Lost connection to MySQL server during query  查询时连接中断
     1317 ER_QUERY_INTERRUPTED     查询被打断
     1046 ER_NO_DB_ERROR     无此数据库
     * @var array
     */
    static protected $reConnectErrorArr = array(2006, 1317, 2013, 1046);

    /**
     * @param string $DBName 数据名名称,在config/DBConfig.php中配置
     * @param array $DBConfig 数据库配置，可单独修改主库或从库host、port、user、pass、database、charset
     */
    public function __construct($DBName = '', $DBConfig = array()) {
        $this->DBName = $DBName;
        $this->DBConfig = $DBConfig;
    }

    /**
     * 魔术方法，主要增加了默认缓存支持
     * @param string $ttl 过期时间，默认值为120秒
     * @param mix $ttl 过期时间，默认值为120秒
     * @return void
     */
    public function __call($name, $args) {
        $suffix = strrchr($name, '_');
        if($suffix !== "" || $suffix !== false) {
            $funcName = substr($name, 0, -strlen($suffix));
            if($suffix === '_cache') {
                empty($this->mcd) && $this->mcd = new BaseModelMemcached();
                $mc_key = "db_{$name}_" . md5(__CLASS__. $funcName . serialize($args) . (isset($_GET[$this->pName]) ? $_GET[$this->pName] : ''));
                $ret = $this->mcd->get($mc_key);
                if($this->mcd->getResultCode() !== Memcached::RES_SUCCESS) {
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($funcName, 'db_call_nocache');
                    $ret = call_user_func_array(array($this, $funcName), $args);
                    $this->mcd->set($mc_key, $ret, $this->cacheTimeout);
                }
                return $ret;
            }
        }
        return $this->_error(90320, '数据库基类方法不存在: ' . $name);
    }

    /**
     * 设置默认缓存的过期时间
     * @param string $pName 分页参数名，默认为page
     * @return void
     */
    public function setPName($pName = 'page') {
        $this->pName = $pName;
    }

    /**
     * 设置默认缓存的过期时间
     * @param string $ttl 过期时间，默认值为120秒
     * @return void
     */
    public function setCacheTime($ttl = 120) {
        $this->cacheTimeout = $ttl;
    }

    /**
     * 设置表名
     * @param string $tableName 表名
     * @return void
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * 获取表名
     * @return string 表名
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * 设置分页样式
     * @return void
     */
    public function setPageStyle($pageStyle) {
        is_object($this->pageModel) ? $this->pageModel->setStyle($pageStyle) : '';
    }

    /**
     * 设置禁止修改的字段
     * @return void
     */
    public function setDisableField(array $disable_field) {
        $this->disableField = $disable_field;
    }

    /**
     * 设置或略报错错误号
     * @return void
     */
    public function setIgnoreErrorArr(array $ignoreErrorArr) {
        $this->ignoreErrorArr = $ignoreErrorArr;
    }

    /**
     * 获取返回结果总数
     * @return void
     */
    public function getCountNum() {
        return $this->countNum;
    }

    /**
     * 获取分页器html片段
     * @return void
     */
    public function getPageStr() {
        return is_object($this->pageModel) ? $this->pageModel->getPageStr() : '';
    }

    /**
     * 过滤数据
     * @return void
     */
    public function escape_string($string, $master_or_slave = 'slave') {
        $this->checkLink($master_or_slave);
        if (!$this->link) {
            return $this->_error(90311, "数据库连接失败");
        }
        return $this->link->real_escape_string($string);
    }

    /**
     *
     */
    public function getPageJump() {
        // page类中函数删除，暂时返回空字符串处理
        return '';
        // return is_object($this->pageModel) ? $this->pageModel->getPageJump() : '';
    }

    /**
     * 获取查询出错信息
     */
    public function getErrorInfo() {
        if (!$this->link) {
            return $this->link->error;
        } else {
            return '';
        }
    }

    /**
     * 获取查询出错代号
     */
    public function getErrorCode() {
        if (!$this->link) {
            return $this->link->errno;
        } else {
            return -1;
        }
    }

    /**
     * 执行查询语句
     * @param string @sql 需要执行的查询语句
     * @param array $data 查询语句中以'?'替代的变量值
     * @param int $pageSize 每页结果数
     * @param string $master_or_slave 指定从主库还是从库查询
     * @return array
     */
    public function getData($sql, $data = '', $pageSize = '', $master_or_slave = 'slave') {
        if (!is_array($data) && !is_numeric($pageSize)) {
            $pageSize = $data;
            $data = '';
        }
        if (is_numeric($pageSize) && $pageSize > 0) {
            //获取读出记录数（用于翻页计算）
            $count_sql = "SELECT count(*) AS num " . substr($sql, stripos($sql, "from"));
            $count_sql = preg_replace("/\s*ORDER\s*BY.*/i", "", $count_sql);
            $query = $this->_sendQuery($count_sql, $data, $master_or_slave);

            if ($query->num_rows == 1) {
                $row = $query->fetch_row();
                $this->countNum = $row[0];
            } else {
                $this->countNum = $query->num_rows;
            }
            $this->debugResult($this->countNum);

            $this->pageModel = new BaseModelPage($this->countNum, $pageSize, array(), $this->pName);
            $sql .= $this->pageModel->getLimit();
        }

        $query = $this->_sendQuery($sql, $data, $master_or_slave);
        $arr = array();
        if (!is_object($query)) {
            return $this->_error(90301, '数据库返回非资源');
        }
        while ($row = $query->fetch_assoc()) {
            empty($row) || $arr[] = $row;
        }
        $this->debugResult($arr);
        return $arr;
    }

    /**
     * 执行查询语句
     * @param string @sql 需要执行的查询语句,获得单列的一维数组
     * @param array $data 查询语句中以'?'替代的变量值
     * @param string $master_or_slave 指定从主库还是从库查询
     * @return array
     */
    public function getColumn($sql, $data = '', $master_or_slave = 'slave') {
        $query = $this->_sendQuery($sql, $data, $master_or_slave);
        if (!is_object($query)) {
            return $this->_error(90301, '数据库返回非资源');
        }
        $arr = array();
        while ($row = $query->fetch_row()) {
            empty($row) || $arr[] = $row[0];
        }
        $this->debugResult($arr);
        return $arr;
    }

    /**
     * 执行SQL 返回一行记录
     * @param string @sql 需要执行的查询语句
     * @param array $data 查询语句中以'?'替代的变量值，默认为空
     * @param string $master_or_slave 指定从主库还是从库查询，默认为从库
     * @return array
     */
    public function getRow($sql, $data = '', $master_or_slave = 'slave') {
        $query = $this->_sendQuery($sql, $data, $master_or_slave);
        if (!is_object($query)) {
            return $this->_error(90301, '数据库返回非资源');
        }
        $row = $query->fetch_assoc();
        $row = is_null($row) ? array() : $row;
        $this->debugResult($row);
        return $row;
    }

    /**
     * 执行SQL 返回二维数组
     */
    public function getFirst($sql, $data = '', $master_or_slave = "slave") {
        $query = $this->_sendQuery($sql, $data, $master_or_slave);
        if (!is_object($query)) {
            return $this->_error(90301, '数据库返回非资源');
        }
        $row = $query->fetch_row();
        $row[0] = is_null($row[0]) ? '' : $row[0];
        $this->debugResult($row[0]);
        return $row[0];
    }

    /**
     * 插入数据
     * @param array $insert_arr array('key1' => $value1, 'key2' => $value2)
     * @param string $affix default is '' LOW_PRIORITY|DELAYED|HIGH_PRIORITY|IGNORE
     * @param array &$result default is array()
     * @param string $sqlType default is INSERT INSERT|REPLACE
     * @return bool
     */
    public function insert($insert_value, $affix = '', &$result = array(), $sqlType = 'INSERT') {
        $sqlType = strtoupper($sqlType) !== 'REPLACE' ? 'INSERT' : 'REPLACE';
        if (!is_array($insert_value) || empty($insert_value)) {
            return $this->_error(90302, $sqlType !== 'REPLACE' ? 'insert中insert_value传参错误' : 'replace中replace_value传参错误');
        }
        if (!in_array($affix, array('LOW_PRIORITY', 'DELAYED', 'HIGH_PRIORITY', 'IGNORE'), true)) {
            $affix = '';
        }
        $inKeyArr = $inValArr = array();
        foreach ($insert_value as $key => $value) {
            if($this->checkField($key, $value)) {
                $inKeyArr[] = ' `' . $key . '` ';
                $inValArr[] = ' ? ';
            } else {
                return false;
            }
        }
        if (empty($inKeyArr)) {
            return $this->_error(90302, $sqlType !== 'REPLACE' ? 'insert中insert_value传参错误' : 'replace中replace_value传参错误');
        }
        $sql = "{$sqlType} {$affix} INTO `" . $this->getTableName() . "` (" . implode(',', $inKeyArr) . ") VALUE (" . implode(',', $inValArr) . ")";
        $this->_sendQuery($sql, array_values($insert_value), 'master', $result);
        $this->debugResult($result, 'db_affected_num');
        if (is_int($result['affected_num']) && $result['affected_num'] >= 0) {
            return true;
        }
        return false;
    }


    /**
     * 插入数据唯一键更新
     * @param array $insert_arr array('key1' => $value1,'key2' => $value2);
     * @param array $update_keys('key1','key2');
     * @param string $affix default is '' LOW_PRIORITY|DELAYED|HIGH_PRIORITY|IGNORE
     * @param array &$result default is array()
     * @return bool
     */
    public function insertOnDuplicate($insert_value, $update_keys, $affix = '', &$result = array()) {
        if (!is_array($insert_value) || empty($insert_value) || !is_array($update_keys) || empty($update_keys)) {
            return $this->_error(90302, 'insertOnDuplicate中insert_value传参错误');
        }
        if (!in_array($affix, array('LOW_PRIORITY', 'DELAYED', 'HIGH_PRIORITY', 'IGNORE'), true)) {
            $affix = '';
        }
        $inKeyArr = $inValArr = array();
        foreach ($insert_value as $key => $value) {
            if($this->checkField($key, $value)) {
                $inKeyArr[] = ' `' . $key . '` ';
                $inValArr[] = ' ? ';
            } else {
                return false;
            }
        }
        if (empty($inKeyArr)) {
            return $this->_error(90302, 'insertOnDuplicate中insert_value传参错误');
        }
        $upKeyArr = $upValArr = array();
        foreach ($update_keys as $key) {
            if (array_key_exists($key, $insert_value)) {
                if($this->checkField($key, $insert_value[$key])) {
                    $upKeyArr[] = ' `' . $key . '` = ?';
                    $upValArr[] = $insert_value[$key];
                } else {
                    return false;
                }
            } else {
                return $this->_error(90303, 'insertOnDuplicate中update_keys参数在insert_value中不存在');
            }
        }
        if (empty($upKeyArr)) {
            return $this->_error(90304, 'insertOnDuplicate传参update无有效字段');
        }
        $sql = "INSERT {$affix} INTO `" . $this->getTableName() . "` (" . implode(',', $inKeyArr) . ") VALUE (" . implode(',', $inValArr) . ") ON DUPLICATE KEY UPDATE " . implode(',', $upKeyArr);
        $this->_sendQuery($sql, array_merge(array_values($insert_value), $upValArr), 'master', $result);
        $this->debugResult($result, 'db_affected_num');
        if (is_int($result['affected_num']) && $result['affected_num'] >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 更新数据
     * @param array * $update_value('key1' => $value1,'key2' => $value2);
     * @param array||string $where
     * @param array &$result default is array()
     * @return bool
     */
    public function update($update_value, $where, &$result = array()) {
        if (!is_array($update_value)) {
            return $this->_error(90305, 'update中update_value传参错误');
        }
        $whereStr = '';
        $whereArr = array();
        if (is_string($where)) {
            $tmp_where = strtolower($where);
            if (!strpos($tmp_where, "=") && !strpos($tmp_where, 'in') && !strpos($tmp_where, 'like')) {
                return $this->_error(90306, 'update中where条件错误');
            }
            $whereStr = $where;
        } elseif (is_array($where)) {
            $tmp = $whereArr = array();//条件，对应key=value
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $tmp[] = "`" . $key . "` in ? ";
                } else {
                    $tmp[] = "`" . $key . "` = ? ";
                }
                $whereArr[] = $value;
            }
            $whereStr = implode(' AND ', $tmp);
        } else {
            return $this->_error(90306, 'update中where条件错误');
        }
        $upArr = array();
        foreach ($update_value as $key => $value) {
            if($this->checkField($key, $value)) {
                if ($key{0} === "#") {// 用于特殊操作。有注入漏洞
                    $upArr[] = " `" . substr($key, 1) . "` = {$value} ";
                    unset($update_value[$key]);
                } else {
                    $upArr[] = ' `' . $key . '` = ? ';
                }
            } else {
                return false;
            }
        }
        $sql = "UPDATE `" . $this->getTableName() . "` SET " . implode(',', $upArr) . " WHERE {$whereStr}";
        $this->_sendQuery($sql, array_merge(array_values($update_value), $whereArr), 'master', $result);
        $this->debugResult($result, 'db_affected_num');
        if (is_int($result['affected_num']) && $result['affected_num'] >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 删除指定的数据
     * @param string||array $where
     * @param array &$result default is array()
     * @return bool
     */
    public function delete($where, &$result = array()) {
        if (is_array($where)) {
            $tmp = $whereArr = array();//条件，对应key=value
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $tmp[] = "`" . $key . "` in ? ";
                } else {
                    $tmp[] = "`" . $key . "` = ? ";
                }
                $whereArr[] = $value;
            }
            $whereStr = implode(' AND ', $tmp);
        } else {
            $tmp_where = strtolower($where);
            if (!strpos($tmp_where, "=") && !strpos($tmp_where, 'in') && !strpos($tmp_where, 'like')) {
                return $this->_error(90307, 'delete中where条件错误');
            }
            $whereStr = $where;
            $whereArr = '';
        }
        $sql = "DELETE FROM `" . $this->getTableName() . "` WHERE {$whereStr}";
        $this->_sendQuery($sql, $whereArr, 'master', $result);
        $this->debugResult($result, 'db_affected_num');
        if (is_int($result['affected_num']) && $result['affected_num'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * 执行给出的SQL语句
     * @param string $sql               sql statement
     * @param array &$result            result data
     * @param string $master_or_slave   master db / slave db
     * @return the number of this->_affected rows
     */
    public function exec($sql, $data = '', &$result = array(), $master_or_slave = 'master') {
        switch (strtoupper(trim($sql))) {
            case 'START TRANSACTION':
            case 'BEGIN':
                self::$inTransaction = true;
                break;
            case 'COMMIT':
            case 'ROLLBACK':
                self::$inTransaction = false;
                break;
        }
        defined('DAGGER_DEBUG') && BaseModelCommon::debug(intval(self::$inTransaction), 'db_inTransaction');
        $this->_sendQuery($sql, $data, $master_or_slave, $result);
        $this->debugResult($result, 'db_affected_num');
        if (is_int($result['affected_num']) && $result['affected_num'] >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 获取插入数据id
     */
    public function insertId() {
        $sql = 'SELECT last_insert_id()';
        return $this->getFirst($sql, '', 'master');
    }

    /**
     * 确保数据库连接
     * @param string $master_or_slave   检查主库还是从库
     * @return void
     */
    protected function checkLink($master_or_slave = 'slave', $reConnect = false) {
        $timeout = defined('DAGGER_DBCONNECT_TIMEOUT') ? DAGGER_DBCONNECT_TIMEOUT : 1;
        $startTime = microtime(true);
        $this->link = BaseModelDBConnect::connectDB($this->DBName, $master_or_slave, $this->DBConfig, $reConnect);
        $runTime = microtime(true) - $startTime;
        if($runTime > $timeout) {
            $this->_error(90314, "m/s[{$master_or_slave}],runtime[{$runTime}s/{$timeout}s]");
        }
    }

    /**
     * 设定是否对字段进行检测
     * @param string $key       要设定的key
     * @param string $validate  要设定的检测方法
     * 可能的值包括 (0_date, 1, 0, 1_date)等
     * @return void
     */
    public function setValidate($key, $validate) {
        $this->field_arr[$key]['validate'] = $validate;
    }

    /**
     * 获取查询字段名
     */
    public function getFields() {
        return array_keys($this->field_arr);
    }

    /**
     *
     */
    public function getFieldArr() {
        return $this->field_arr;
    }

    /**
     * 验证字段&&数据
     */
    protected function checkField($key, $value) {
        if (defined('EXTERN')) {
            return true;
        }
        $key{0} === "#" && $key = substr($key, 1);
        if (empty($this->field_arr[$key])) {
            return $this->_error(90308, "{$key}：字段不存在", array('field' => $key, 'table' => $this->tableName));
        }
        if (in_array($key, $this->disableField, true)) {
            return $this->_error(90309, "{$key}：字段禁止修改", array('field' => $key, 'table' => $this->tableName));
        }
        $msg = BaseModelValidate::check($value, $this->field_arr[$key]['type'], $this->field_arr[$key]['validate'], $this->field_arr[$key]['max_length']);
        if ($msg !== true) {
            return $this->_error(90310, "{$this->field_arr[$key]['name']}：" . $msg, array('field' => $key, 'table' => $this->tableName));
        }
        return true;
    }

    /**
     * 执行SQL语句
     * @param string $sql 需要执行的语句
     * @param array $data 执行的语句中以'?'替代的变量值
     * @param string $master_or_slave   主从选择master或者slave
     * @param array &$result            result data
     * @return mixed
     */
    protected function _sendQuery($sql, $data = '', $master_or_slave = 'slave', &$result = array()) {
        $this->checkLink($master_or_slave);
        if (!$this->link) {
            return $this->_error(90311, "数据库连接失败");
        }

        $this->setSql($sql, $data, $master_or_slave);

        if (defined('DAGGER_DEBUG')) {
            BaseModelCommon::debug($this->sql, 'db_sql' . ($master_or_slave !== 'master' ? '' : '_master'));
        }
        if (empty($this->sql)) {
            return $this->_error(90312, "sql不能为空");
        }
        $this->runTime = microtime(true);
        $retry = 0;
        do {
            if ($retry) {
                $this->checkLink($master_or_slave, true);
                if (!$this->link) {
                    return $this->_error(90311, "数据库连接失败");
                }
            }
            $query = $this->link->query($this->sql);
            if (strtoupper(substr(ltrim($this->sql), 0, 6)) !== "SELECT") {
                $result['affected_num'] = $this->link->affected_rows;
            }
            if (in_array($this->link->errno, self::$reConnectErrorArr, true)) {
                $retry++;
            } elseif ($this->link->errno !== 0) {
                return $this->_error();
            } elseif ($query === false && $this->link->errno === 0) {
                //TODO处理不可能的错误
                $retry++;
            } elseif ($retry) {
                $retry++;
            }
        } while ($retry === 1 && !self::$inTransaction);
        return $query;
    }

    /**
     * 错误处理
     * @param int $errno 错误号
     * @param string $error 错误信息
     * @param data $data 相关提示数据
     * @return void
     * @author wangxin3
     * error code
     * 90301 数据库返回非资源
     * 90302 insert或replace入库数据错误
     * 90303 insertOnDuplicate中update_keys参数在insert_value中不存在
     * 90304 insertOnDuplicate中update_keys参数无有效字段
     * 90305 update中update_value传参错误
     * 90306 update中where条件错误
     * 90307 delete中where条件错误
     * 90308 字段在配置文件中未定义
     * 90309 字段在配置文件中禁止修改
     * 90310 字段值不符合在配置文件定义的类型
     * 90311 据库连接失败
     * 90312 sql不能为空
     * 90313 传参不符合拼接规范，无法正确翻译sql语句
     * 90314 数据库连接超过指定时间
     * 90320 数据库基类方法不存在
     **/
    protected function _error($errno = 0, $error = '', $data = array()) {
        // mysql错误忽略
        if (!$this->link && in_array($this->link->errno, $this->ignoreErrorArr, true)) {
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($this->link->errno, 'db_ignoreErrno_info');
            return false;
        }
        $errno = empty($errno) ? $this->link->errno : $errno;
        $error = empty($error) ? $this->link->error : $error;
        if(in_array($errno, array(90314), true) || defined('QUEUE') || defined('EXTERN')) {
            defined('DAGGER_DEBUG') && BaseModelCommon::debug('[errro code] ' . $errno. ' [errro msg] ' . $error . ' [详细说明]:https://github.com/wxkingstar/dagger/wiki/'.$errno, 'db_error');
            BaseModelLog::sendLog($errno, $error, '', BaseModelLog::ERROR_MODEL_ID_DB);
            return false;
        }
        throw new BaseModelDBException($error, $errno, $data);
    }

    /**
     * 构造sql语句
     * @param string $sql
     * @param array $data
     * @return void
     */
    protected function setSql($sql, $data = '', $master_or_slave = 'slave') {
        $this->sql = $sqlShow = '';
        if (strpos($sql, '?') && is_array($data) && count($data) > 0) {
            if (substr_count($sql, '?') != count($data)) {
                return $this->_error(90313, '传参不符合拼接规范，无法正确翻译sql语句! [sql] ' . $sql . ' [data] ' . var_export($data, true));
            }
            $sqlArr = explode('?', $sql);
            $last = array_pop($sqlArr);
            foreach ($sqlArr as $k => $v) {
                if (!empty($v) && isset($data[$k])) {
                    if (!is_array($data[$k])) {
                        $value = "'" . $this->escape_string($data[$k], $master_or_slave) . "'";
                    } else {
                        $valueArr = array();
                        foreach ($data[$k] as $val) {
                            $valueArr[] = "'" . $this->escape_string($val, $master_or_slave) . "'";
                        }
                        $value = '(' . implode(', ', $valueArr) . ')';
                    }
                    $sqlShow .= $v . $value;
                } else {
                    return $this->_error(90313, '传参不符合拼接规范，无法正确翻译sql语句! [sql] ' . $sql . ' [data] ' . var_export($data, true));
                }
            }
            $sqlShow .= $last;
        } else {
            $sqlShow = $sql;
        }
        $this->sql = $sqlShow;
    }

    /**
     * 调试结果
     * @param string $sql
     * @param array $data
     * @return void
     */
    protected function debugResult($result, $type = '') {
        $this->runTime = BaseModelCommon::addStatInfo('db', $this->runTime);
        if (defined('DAGGER_DEBUG')) {
            $arr = empty($type) ? array(array('运行时间', '查询结果'), array($this->runTime, $result)) : array(array('运行时间', '影响条目'), array($this->runTime, $result['affected_num']));
            BaseModelCommon::debug($arr, 'db_sql_result');
        }
    }

    /**
     * 析构释放内存
     */
    public function __destruct() {
        unset($this->tableName);
        unset($this->master_or_slave);
        unset($this->sql);
    }
}
