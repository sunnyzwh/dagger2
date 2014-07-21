<?php
/**
 * All rights reserved.
 * MC基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
*/

class BaseModelMemcache {

    /**
     * 主动刷新时间key后缀
     * @var string
     */
    const CACHE_TIME_CTL = '_@t';

    /**
     * 锁key后缀
     * @var string
     */
    const CACHE_LOCK_CTL = '_@l';

    
    /**
     * MC连接池
     * @var array
     */
    static protected $memcache = array();

    /**
     * 当前MC链接
     * @var resource
     */
    protected $mc;

    /**
     * 是否防雪崩
     * @var resource
     */
    private $snowslide;//不允许子类修改
    
    /**
     * 当前MC集群
     * @var string
     */
    protected $servers;

    /**
     * 当前MC连接状态
     * @var bool
     */
    private $checkStats;

    /**
     * 构造函数
     * @params string $mcName MC名称
     * @params array $mcConfig eg:array('servers'=>'192.168.1.1:7600 192.168.1.2:7700');
     */
    public function __construct($mcName = '', $servers = '', $snowslide = false) {
        $this->snowslide = $snowslide;
        empty($mcName) && $mcName = DAGGER_MC_DEFAULT;
        $this->servers = empty($servers) ? DBConfig::$config['memcache'][$mcName]['servers'] : $servers;
        $mcKey = md5($this->servers);
        if (isset(self::$memcache[$mcKey]) && self::$memcache[$mcKey] instanceof Memcache) {
            $this->mc = self::$memcache[$mcKey];
        } else {
            self::$memcache[$mcKey] = new Memcache();
            $serverArr = explode (' ', $this->servers);
            foreach ($serverArr as $v) {
                list($server, $port) = explode(':', $v);
                self::$memcache[$mcKey]->addServer($server, $port, $persistent = 0);
            }
            $this->mc = self::$memcache[$mcKey];
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($this->servers, 'mc_connect');
            $this->checkStats = $this->checkConnection();
        }
    }

    /**
     * 设置缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $time 缓存时间
     * @param bool $compress 是否启用压缩
     * @retrun bool
     */
    public function set($key, $value, $time = 0, $compress = MEMCACHE_COMPRESSED) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($value, "mc_set({$key}),ttl({$time})");
        if ($this->snowslide === true) {
            $this->mc->set($key.self::CACHE_TIME_CTL, 1, 0, $time);
            $ret = $this->mc->set($key, $value, $compress, $time + 86400);
        } else {
            $ret = $this->mc->set($key, $value, $compress, $time);
        }
        $this->releaseLock($key);
        return $ret;
    }

    /**
     * 增加缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $time 缓存时间
     * @param bool $compress 是否启用压缩
     * @retrun bool
     */
    public function add($key, $value, $time = 0, $compress = MEMCACHE_COMPRESSED) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($value, "mc_add({$key}),ttl({$time})");
        if ($this->snowslide === true) {
            if (!$this->mc->add($key.self::CACHE_TIME_CTL, 1, $compress, $time)) {
                return false;
            }
            $ret = $this->mc->set($key, $value, $compress, $time + 86400);
        } else {
            return $this->mc->add($key, $value, $compress, $time);
        }
        return $ret;
    }


    /**
     * 自增
     * @param string $key 缓存键
     * @param int $incre 自增值
     * @return float
     */
    public function increment($key, $incre=1) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        $ret = $this->mc->increment($key, $incre);
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($ret, "mc_increment({$key})");
        return $ret;
    }


    /**
     * 自减
     * @param string $key 缓存键
     * @param int $incre 自减值
     * @return float
     */
    public function decrement($key, $incre=1) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        $ret = $this->mc->decrement($key, $incre);
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($ret, "mc_decrement({$key})");
        return $ret;
    }

    /**
     * 获取缓存
     * @param string $key 缓存键
     * @param int $lockTime  缓存锁失效时间
     * @return mixed
     */
    public function get($key, $lockTime=3) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        if ($this->snowslide) {
            $outdated = $this->mc->get($key.self::CACHE_TIME_CTL);
            $data = $this->mc->get($key);
            if($data === false || $outdated === false || (isset($_GET['_flush_cache']) && $_GET['_flush_cache'] == 1)){
                if($this->getLock($key, $lockTime)) {
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug(false, "mc_get_not_lock({$key})");
                    return false;
                }
                $attempt = 0;
                do {
                    $dataNew = $this->mc->get($key);
                    if(++$attempt >= 4) {
                        break;
                    }
                    if($dataNew === false) {
                        usleep(100000);
                    } else {
                        return $dataNew;
                    }
                } while($data === false);
            }
        } else {
            $data = $this->mc->get($key);
        }
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($data, "mc_get({$key})");
        return $data;
    }
    
    /**
     * 删除缓存
     * @param string $key 缓存键
     * @return bool 
     */
    public function delete($key) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($key.self::CACHE_TIME_CTL, 'mc_delete');
        if ($this->snowslide) {
            if ($this->mc->delete($key.self::CACHE_TIME_CTL)) {
                return $this->mc->delete($key);
            } else {
                return false;
            }
        } else {
            return $this->mc->delete($key);
        }
    }

    /**
     * 删除全部集群缓存
     * @param string $key 缓存键
     * @return bool
     */
    public static function deleteAll($key) {
        $key = DAGGER_MC_KEY_PREFIX . $key;
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($key, 'mc_delete_all');
        $searchMcArr = explode(' ', SEARCH_MC_ARR);
        foreach($searchMcArr as $searchMc) {
            $mc = new BaseModelMemcache($searchMc);
            $mc->delete($key);
        }
    }

    /**
     * 对资源加锁
     * @param string $key 缓存锁键
     * @param int $lockTime 缓存锁失效时间
     */
    public function getLock($key, $lockTime=3) {
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($lockTime, 'mc_lock_time');
        return $this->mc->add($key.self::CACHE_LOCK_CTL, 1, false, $lockTime);
    }

    /**
     * 释放资源锁
     * @param string $key 缓存锁键
     */
    public function releaseLock($key) {
        $this->mc->delete($key.self::CACHE_LOCK_CTL);
    }

    /**
     * 检测memcache是否正常运行
     */
    private function checkConnection() {
        $timeout = defined('DAGGER_MCCONNECT_TIMEOUT') ? DAGGER_MCCONNECT_TIMEOUT : 0.5;
        $startTime = microtime(true);
        $rs = $this->mc->getVersion();
        $checkTime = microtime(true) - $startTime;
        if($checkTime > $timeout) {
            $errno = 90501;
            $error = "MC连接超{$timeout}秒" . 
                "request_uri[{$_SERVER['REQUEST_URI']}]," . 
                "c/s[{$_SERVER['REMOTE_ADDR']}/{$_SERVER['SERVER_ADDR']}]," . 
                "mc[". $this->servers ."]" . 
                "runtime[{$runTime}s/{$timeout}s]";
            defined('DAGGER_DEBUG') && BaseModelCommon::debug('[errro code] ' . $errno. ' [errro msg] ' . $errormsg . ' [详细说明]:https://github.com/wxkingstar/dagger/wiki/'.$errno, 'mc_error');
            BaseModelLog::sendLog($errno, $error, '', BaseModelLog::ERROR_MODEL_ID_MC);
        }
        if($rs !== false){
            return true;
        }
        BaseModelLog::sendLog(90500, "memcache服务器: {$this->servers} 无法响应", '', BaseModelLog::ERROR_MODEL_ID_MC);
        return false;
    }
}
