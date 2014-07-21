<?php
/**
 * 查询数据库前先检查MC
 */
class MyDB extends BaseModelDB {
    /**
     * 默认的MC失效时间
     * @var int
     */
    protected $MC_EXPIRE_TIME = 600;

    /**
     * 支持MC的数据查询，只查询从库。因为查询主库不应该读取memcache数据
     */
    public function getDataWithMC($sql, $data = '', $pageSize = '', $expire = '') {
        $mc = new MyMemcache();
        $args = func_get_args();
        $mcResultKey = __FUNCTION__ . '|' . md5(implode('', array('data', serialize($args), $this->DBName)));//结果的mckey
        $mcPageKey = __FUNCTION__ . '|' . md5(implode('', array('page', serialize($args), $this->DBName)));//结果总数的mckey
        $needQueryDB = false;//判断是否需要查询数据库
        $needPageStr = is_numeric($pageSize) && $pageSize > 0;//判断是否需要分页信息
        $result = false;

        if ($needPageStr) {
            $totalNum = $mc->get($mcPageKey);//检查结果总数是否被缓存
            if ($totalNum) {
                $this->pageModel = new Page($totalNum, $pageSize, array(), $this->pName);
                $mcResultKey = __FUNCTION__ . '|' . md5(implode('', array('data', serialize($args), $this->DBName, $this->pageModel->getLimit())));//修改结果的的mckey
            } else {
                $needQueryDB = true;
            }
        }
        if (!$needQueryDB) {
            $result = $mc->get($mcResultKey);//检查结果是否被缓存
            $needQueryDB |= empty($result);//判断是否需要查询数据库
        }
        if ($needQueryDB) {
            $expire = intval($expire);
            if (empty($expire)) {
                $expire = $this->MC_EXPIRE_TIME;
            }
            $result = $this->getData($sql, $data, $pageSize, 'slave');
            $mc->set($mcResultKey, $result, $expire);
            if ($needPageStr) {
                $mc->set($mcPageKey, $this->getCountNum(), $expire);
            }
        } else {
            if ($needPageStr) {
                $this->countNum = $totalNum;
                $this->pageModel = new Page($totalNum, $pageSize, array(), $this->pName);
            }
        }
        return $result;
    }

    /**
     * 返回结果集的第一列数据
     */
    public function getCol($sql, $data = '', $pageSize = '', $master_or_slave = 'slave') {
        $data0 = $this->getData($sql, $data, $pageSize, $master_or_slave);
        if (empty($data0)) {
            return array();
        }
        $data = array();
        foreach ($data0 as $k => $v) {
            $data[] = current($v);
        }
        return $data;
    }

    /**
     * 返回结果集的第一列数据
     * 支持MC的数据查询，只查询从库。因为查询主库不应该读取memcache数据
     */
    public function getColWithMC($sql, $data = '', $pageSize = '', $expire = '') {
        $expire = intval($expire);
        if (empty($expire)) {
            $expire = $this->MC_EXPIRE_TIME;
        }
        $mc = new MyMemcache();
        $args = func_get_args();
        $mcResultKey = __FUNCTION__ . '|' . md5(implode('', array(serialize($args), $this->DBName)));//结果的mckey
        $result = $mc->get($mcResultKey);
        $needQueryDB = ($result === false);
        if ($needQueryDB) {
            $result = $this->getCol($sql, $data, $pageSize, 'slave');
            $mc->set($mcResultKey, $result, $expire);
        } else {
        }
        return $result;
    }

    /**
     * 返回结果集的第一条记录
     * 支持MC的数据查询，只查询从库。因为查询主库不应该读取memcache数据
     */
    public function getRowWithMC($sql, $data = '', $expire = '') {
        $expire = intval($expire);
        if (empty($expire)) {
            $expire = $this->MC_EXPIRE_TIME;
        }
        $mc = new MyMemcache();
        $args = func_get_args();
        $mcResultKey = __FUNCTION__ . '|' . md5(implode('', array(serialize($args), $this->DBName)));//结果的mckey
        $result = $mc->get($mcResultKey);
        $needQueryDB = ($result === false);
        if ($needQueryDB) {
            $result = $this->getRow($sql, $data, 'slave');
            $mc->set($mcResultKey, $result, $expire);
        } else {
        }
        return $result;
    }

    /**
     * 返回结果集的第一条记录的第一个字段。
     * 支持MC的数据查询，只查询从库。因为查询主库不应该读取memcache数据
     */
    public function getFirstWithMC($sql, $data = '', $expire = '') {
        $expire = intval($expire);
        if (empty($expire)) {
            $expire = $this->MC_EXPIRE_TIME;
        }
        $mc = new MyMemcache();
        $args = func_get_args();
        $mcResultKey = __FUNCTION__ . '|' . md5(implode('', array(serialize($args), $this->DBName)));//结果的mckey
        $result = $mc->get($mcResultKey);
        $needQueryDB = ($result === false);
        if ($needQueryDB) {
            $result = $this->getFirst($sql, $data, 'slave');
            $result && $mc->set($mcResultKey, $result, $expire);
        } else {
        }
        return $result;
    }

    /**
     * 增加慢查询提示 
     */
    public function debugResult($result, $type = '') {
        parent::debugResult($result, $type);
        if (defined('DAGGER_DEBUG')) {
            $runTime = floatval($this->runTime);//本来就是毫秒..不用成1000..
            if ($runTime > 100) {//超过100ms  就为慢查询...
                BaseModelCommon::debug('slow sql', 'warn');
            }
        }
    }
}

