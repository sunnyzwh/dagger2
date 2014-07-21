<?php
/**
 * All rights reserved.
 * HTTP基类
 * @author          wangxin <wx012@126.com>
 * @editer          xuyan <xuyan4@staff.sina.com.cn>
 * @time            2013/3/11 19:30
 * @version         Id: 1.1
 */

class BaseModelHttp {

    const DAGGER_HTTP_TIMEOUT = 3; // curl超时设置，单位是秒。基类方法可自定义重试次数，故而如果接口超时，最大重试次数倍此设置时间。
    const DAGGER_HTTP_MAXREDIRECT = 2; // 301、302、303、307最大跳转次数。
    const DAGGER_HTTP_REDO = 0; // 访问失败后的重试次数, 默认0次为不重试。
    const DAGGER_HTTP_USERAGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 Dagger/1.1';// 默认UA头
    const DAGGER_HTTP_MC_SERVER_KEY = 'DAGGER';
    const DAGGER_HTTP_FLASE_LOCK_TIMES = 0;

    private static $dagger_http_useragent = self::DAGGER_HTTP_USERAGENT;
    private static $dagger_http_lock_times = self::DAGGER_HTTP_FLASE_LOCK_TIMES;

    private static $last_header_info;

    private function __construct() {}
    private function __clone() {}
    private function __destruct() {}

    /**
     * 设置请求失败的锁的次数阈值
     * @param int $times
     * @return void
     */
    public static function setLockTimes($times = self::DAGGER_HTTP_FLASE_LOCK_TIMES) {
        self::$dagger_http_lock_times = $times;
    }

    /**
     * 设置User-Agent头信息
     * @param $userAgent string 发送请求url的User-Agent头,default = ''
     * @return void
     */
    public static function setUserAgent($userAgent = self::DAGGER_HTTP_USERAGENT) {
        self::$dagger_http_useragent = $userAgent;
    }

    /**
     * 获取最后一次请求的header头信息
     * @param void
     * @return mix 
     */
    public static function getLastHeader() {
        return self::$last_header_info;
    }

    /**
     * 发送post请求获取结果
     * @param $args['req'] mix 发送请求url，必传参数 **
     * @param $args['post'] mix 发送请求post数据
     * @param $args['header'] array 发送请求自定义header头，$args['header'] = array('Host: www.dagger.com')
     * @param $args['timeout'] int 发送请求超时设定
     * @param $args['cookie'] string 发送请求cookie
     * @param $args['maxredirect'] int 发送请求最大跳转次数
     * @return mix 失败返回false，成功返回array(抓取结果已解析成数组)
     */
    public static function post($req, $post, array $header = array(), $timeout = self::DAGGER_HTTP_TIMEOUT, $cookie = '', $redo = self::DAGGER_HTTP_REDO, $maxredirect = self::DAGGER_HTTP_MAXREDIRECT) {
        $args['req']            = $req;
        $args['post']           = $post;
        $args['header']         = $header;
        $args['timeout']        = $timeout;
        $args['cookie']         = $cookie;
        $args['redo']           = $redo;
        $args['maxredirect']    = $maxredirect;
        return self::_http_exec($args);
    }

    /**
     * 发送get请求获取结果
     * @param $args['req'] mix 发送请求url，必传参数 **
     * @param $args['header'] array 发送请求自定义header头，$args['header'] = array('Host: www.dagger.com')
     * @param $args['timeout'] int 发送请求超时设定
     * @param $args['cookie'] string 发送请求cookie
     * @param $args['maxredirect'] int 发送请求最大跳转次数
     * @param $args['headOnly'] bool 发送请求是否只抓取header头
     * @return mix 失败返回false，成功返回抓取结果
     */
    public static function get($req, array $header = array(), $timeout = self::DAGGER_HTTP_TIMEOUT, $cookie = '', $redo = self::DAGGER_HTTP_REDO, $maxredirect = self::DAGGER_HTTP_MAXREDIRECT) {
        $args['req']            = $req;
        $args['header']         = $header;
        $args['timeout']        = $timeout;
        $args['cookie']         = $cookie;
        $args['redo']           = $redo;
        $args['maxredirect']    = $maxredirect;
        return self::_http_exec($args);
    }

    /**
     * 发送请求获取header头信息，推荐使用
     * @param $args['req'] mix 发送请求url，必传参数 **
     * @param $args['post'] mix 发送请求post数据
     * @param $args['header'] array 发送请求自定义header头，$args['header'] = array('Host: www.dagger.com')
     * @param $args['timeout'] int 发送请求超时设定
     * @param $args['cookie'] string 发送请求cookie
     * @param $args['maxredirect'] int 发送请求最大跳转次数
     * @param $args['headOnly'] bool 发送请求是否只抓取header头
     * @return mix 失败返回false，成功返回array(抓取结果已解析成数组)
     */
    public static function head($req, $post = array(), array $header = array(), $timeout = self::DAGGER_HTTP_TIMEOUT, $cookie = '', $redo = self::DAGGER_HTTP_REDO, $maxredirect = self::DAGGER_HTTP_MAXREDIRECT) {
        $args['req']            = $req;
        $args['post']           = $post;
        $args['header']         = $header;
        $args['timeout']        = $timeout;
        $args['cookie']         = $cookie;
        $args['redo']           = $redo;
        $args['maxredirect']    = $maxredirect;
        $args['headOnly']       = true;
        return self::_http_exec($args);
    }

    /**
     * 发送get/post并发请求获取结果
     * by xuyan4
     * @param array  包含urls|post|header|timeout|cookie|redo|maxredirect|callback|headOnly等为key的参数
     * @return array 返回以请求的url为key的数组，失败时该url对应的值是false，成功返回请求结果(header已解析为数据)
     */
    public static function curlMulti(array $args) {
        if (empty($args['urls']) || !is_array($args['urls'])) {
            return self::_error(90401, '页面抓取请求url缺失');
        }
        if(count($args['urls']) === count($args['urls'], true)) {
            $_tmp = array();
            foreach($args['urls'] as $k => $url) {
                $_tmp[$k]['url'] = $url;
            }
            $args['urls'] = $_tmp;
        }
        return self::_multi_http_exec($args);
    }

    /**
     * 发送get/post并发请求获取结果
     * by xuyan4
     * @param array  包含url|post|header|timeout|cookie|redo|maxredirect|headOnly等为key的参数
     * @return mix 失败返回false，成功返回抓取结果(header已解析成数组)
     */
    public static function curl(array $args) {
        return self::_http_exec($args);
    }

    
    /**
     * 发送请求不等待接收（支持post）
     * by xuyan4
     * @param $req string 发送请求url
     * @param $post string or array 发送post数据
     * @param $header array 发送header头, array('Host' => 'test.sina.com.cn', 'Referer' => 'http://test.sina.com.cn')
     * @return boolen
     */
    public static function sendRequest($req, $post = array(), $header = array()) {
        $url = self::_makeUri($req);
        $urlArr = parse_url($url);
        if(empty($urlArr['host'])) {
            return self::_error(90402, 'url参数错误');
        }
        $startRunTime = microtime(true);
        $port = isset($urlArr['port']) ? $urlArr['port'] : 80;
        $fp = @stream_socket_client($urlArr['host'] . ':' . $port, $errno, $error, 0.5); 
        $ret = false;
        if ($fp) {
            $out = array();
            empty($urlArr['path']) && $urlArr['path'] = '';
            $urlArr['query'] = empty($urlArr['query']) ? '' : '?' . $urlArr['query'];
            $out[] = (empty($post) ? 'GET' : 'POST') . " {$urlArr['path']}{$urlArr['query']} HTTP/1.1";
            $out['host'] = "Host: {$urlArr['host']}";
            $out['user-agent'] = "User-Agent: " . self::$dagger_http_useragent;
            if (!empty($header) && is_array($header)) {
                foreach($header as $k => $v) {
                    $out[strtolower($k)] = is_numeric($k) ? $v : "$k: $v";
                }
            }
            if(!empty($post)) {
                defined('DAGGER_DEBUG') && BaseModelCommon::debug($post, 'request_post_data');
                if(is_array($post)) {
                    $post = http_build_query($post);
                }
                $out[] = "Content-type: application/x-www-form-urlencoded";
                $out[] = 'Content-Length: ' . strlen($post);
            }
            $out = implode("\r\n", $out) . "\r\nConnection: Close\r\n\r\n" . (empty($post) ? '' : $post . "\r\n");
            stream_set_timeout($fp, 1);
            fwrite($fp, $out);
            fclose ($fp);
            $ret = true;
        } else {
            defined('DAGGER_DEBUG') && BaseModelCommon::debug('[errno] ' . $errno . ' [error] ' . $error, 'request_send_error');
            $ret = false;
        }
        $runTime = BaseModelCommon::addStatInfo('request', $startRunTime);
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($runTime, 'request_time');
        return $ret;
    }

    /**
     * 发送请求获取结果
     * @param $args['req'] mix 发送请求url，必传参数 **
     * @param $args['post'] mix 发送请求post数据
     * @param $args['header'] array 发送请求自定义header头，$args['header'] = array('Host: www.dagger.com')
     * @param $args['timeout'] int 发送请求超时设定
     * @param $args['cookie'] string 发送请求cookie
     * @param $args['maxredirect'] int 发送请求最大跳转次数
     * @param $args['headOnly'] bool 发送请求是否只抓取header头
     * @return mix 失败返回false，成功返回抓取结果
     */
    private static function _http_exec($args) {

        if (!extension_loaded('curl')) {
            return self::_error(90400, '服务器没有安装curl扩展！');
        }

        // $args['req'] = isset($args['req']) ? $args['req'] : array(); // 必传
        $args['post'] = isset($args['post']) ? $args['post'] : array();
        $args['header'] = isset($args['header']) ? $args['header'] : array();
        $args['timeout'] = isset($args['timeout']) && is_numeric($args['timeout']) && $args['timeout'] > 0 ? intval($args['timeout']) : self::DAGGER_HTTP_TIMEOUT;
        $args['cookie'] = isset($args['cookie']) ? $args['cookie'] : '';
        $args['redo'] = isset($args['redo']) ? $args['redo'] : self::DAGGER_HTTP_REDO;
        $args['maxredirect'] = isset($args['maxredirect']) ? intval($args['maxredirect']) : null;
        $args['headOnly'] = isset($args['headOnly']) ? $args['headOnly'] : false;

        $url = self::_makeUri($args['req']);
        if (empty($url)) {
            return self::_error(90401, '页面抓取请求url缺失');
        }

        // mc处理
        if(self::$dagger_http_lock_times > 0) {
            $mcd = new BaseModelMemcached('', '', $native = true);
            $mc_http_key_suffix = md5(strpos($url, '?') ? substr($url, 0, strpos($url, '?')) : $url);
            $mc_http_false_key = "http_false_{$mc_http_key_suffix}";// 存放最近连续失败累计时间、次数、最后一次正确结果。
            $mc_http_lock_key = "http_lock_{$mc_http_key_suffix}";
            $mc_lock = $mcd->getByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_lock_key);
            if ($mcd->getResultCode() === Memcached::RES_SUCCESS) {
                if (defined('DAGGER_DEBUG')) {
                    BaseModelCommon::debug('接口在10秒内出现'. self::$dagger_http_lock_times .'次错误，锁定30秒返回false', 'request_return');
                }
                self::_error(90403, "请求连续" . self::$dagger_http_lock_times . "次失败[{$url}]");
                return false;
            }
        }

        $args['header'][] = 'Expect:'; // 解决100问题
        $ch = curl_init();
        self::_set_curl_opts($ch, $args);
        $rs = curl_setopt($ch, CURLOPT_URL, $url);

        $startRunTime = microtime(true);
        $header = $ret = false;
        do {
            $ret = self::_get_content($ch, $args['maxredirect']);
            if(strpos($ret, "\r\n\r\n") !== false) {
                list($header, $ret) = explode("\r\n\r\n", $ret, 2);
                break;
            }
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($url, 'request_redo');
        } while ($args['redo']-- > 0);
        curl_close($ch);
        $runTime = BaseModelCommon::addStatInfo('request', $startRunTime, 0);

        self::$last_header_info = $header;
        // 抓取header时，解析header头
        if ($args['headOnly'] && $header !== false) {
            $ret = self::_parse_header($header);
        }

        if(self::$dagger_http_lock_times > 0) {
            // mc缓存处理
            // 10秒钟内连续失败指定次数，30秒钟锁定，入口直接返回false;
            if ($ret === false) {
                if (!$mcd->addByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_false_key, 1, 10)) {
                    $falseCount = $mcd->incrementByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_false_key, 1, 1);
                    if ($falseCount > self::$dagger_http_lock_times - 1) {
                        $mcd->addByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_lock_key, 1, 30);
                    }
                }
            } else {
                $mcd->deleteMultiByKey(self::DAGGER_HTTP_MC_SERVER_KEY, array($mc_http_false_key, $mc_http_lock_key));
            }
        }

        if (defined('DAGGER_DEBUG')) {
            is_string($ret) && strlen($ret) > 2000 && $tmpret = (substr($ret, 0, 2000) . '......超长，截取2000字节');
            BaseModelCommon::debug(array(array('运行时间', '执行结果'), array($runTime, (empty($tmpret) ? $ret : $tmpret))), 'request_return');
        }
        return $ret;
    }

    private static function _makeUri($req) {
        $url = '';
        if (is_array($req)) {
            switch (count($req)) {
                case 1:
                    $url = $req[0];
                    break;
                case 2:
                    list($url, $params) = $req;
                    $paramStr = http_build_query($params);
                    $url .= strpos($url, '?') !== false ? "&{$paramStr}" : "?{$paramStr}";
                    break;
                default:
                    return self::_error(90402, 'url参数错误');
            }
        } else if(is_string($req)) {
            $url = $req;
        } else {
            return self::_error(90402, 'url参数错误');
        }
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($url, 'request_url');
        return $url;
    }

    private static function _multi_http_exec($args) {
        if (!extension_loaded('curl')) {
            return self::_error(90400, '服务器没有安装curl扩展！');
        }
        if (empty($args['urls']) || !is_array($args['urls'])) {
            return self::_error(90401, '页面抓取请求url缺失');
        }

        $args['post'] = isset($args['post']) ? $args['post'] : array();
        $args['header'] = isset($args['header']) ? $args['header'] : array();
        $args['timeout'] = isset($args['timeout']) ? intval($args['timeout']) : self::DAGGER_HTTP_TIMEOUT;
        $args['cookie'] = isset($args['cookie']) ? $args['cookie'] : '';
        $args['redo'] = isset($args['redo']) ? $args['redo'] : self::DAGGER_HTTP_REDO;
        $args['maxredirect'] = isset($args['maxredirect']) ? intval($args['maxredirect']) : null;
        $args['headOnly'] = isset($args['headOnly']) ? $args['headOnly'] : false;
        $args['callback'] = isset($args['callback']) ? $args['callback'] : null;
        $urls = array_filter($args['urls']);
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($urls, 'request_multi_urls');

        $ch = curl_init();
        self::_set_curl_opts($ch, $args);

        $header = $ret = $_ch = self::$last_header_info = array();

        if(self::$dagger_http_lock_times > 0) {
            // mc锁处理
            $mcd = new BaseModelMemcached('', '', $native = true);
            $mc_http_false_keys = $mc_http_lock_keys = array();
            foreach($urls as $k => $urlinfo) {
                $url = is_array($urlinfo) ? $urlinfo['url'] : $urlinfo;
                $mc_http_key_suffix = md5(($pos = strpos($url, '?')) ? substr($url, 0, $pos) : $url);
                $mc_http_false_keys[$k] = "http_false_" . $mc_http_key_suffix;// 存放最近连续失败累计时间、次数、最后一次正确结果。
                $mc_http_lock_keys[$k] = "http_lock_" . $mc_http_key_suffix;
            }
            $http_lock_values = $mcd->getMultiByKey(self::DAGGER_HTTP_MC_SERVER_KEY, array_unique($mc_http_lock_keys));
        }

        foreach($urls as $k => $urlinfo) {
            if(self::$dagger_http_lock_times > 0) {
                // mc锁处理
                if(is_array($http_lock_values) && isset($http_lock_values[$mc_http_lock_keys[$k]])) {
                    $url = is_array($urlinfo) ? $urlinfo['url'] : $urlinfo;
                    if (defined('DAGGER_DEBUG')) {
                        BaseModelCommon::debug('接口[' . $url . ']在10秒内出现'. self::$dagger_http_lock_times .'次错误，锁定30秒返回false', 'request_multi_warn');
                    }
                    self::_error(90403, "请求连续" . self::$dagger_http_lock_times ." 次失败[{$url}]");
                    self::$last_header_info[$k] = $header[$k] = $ret[$k] = false;
                    continue;
                }
            }
            $_ch[$k] = curl_copy_handle($ch);
            if(is_array($urlinfo)) {
                //== multi时，不同请求的私有化参数设置 ==//
                self::_set_curl_opts($_ch[$k], $urlinfo, $first = false);
                // url
                curl_setopt($_ch[$k], CURLOPT_URL, $urlinfo['url']);
            } else {
                curl_setopt($_ch[$k], CURLOPT_URL, $urlinfo);
            }
            if(!isset($mh)) {
                $mh = curl_multi_init();
            }
            curl_multi_add_handle($mh, $_ch[$k]);
            BaseModelCommon::addStatInfo('request');
        }

        if(isset($mh)) {
            $startRunTime = microtime(true);
            do {
                $redoUrls = array();
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc === CURLM_CALL_MULTI_PERFORM);
                while($active && $mrc === CURLM_OK) {
                    do {
                        $mrc = curl_multi_exec($mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                    if($mhinfo = curl_multi_info_read($mh, $queue)) {
                        $k = array_search($mhinfo['handle'], $_ch);
                        if($mhinfo['result'] === CURLE_OK) {
                            $ret[$k] = curl_multi_getcontent($mhinfo['handle']);
                            $code = curl_getinfo($mhinfo['handle'], CURLINFO_HTTP_CODE);
                            // $chinfo = curl_getinfo($mhinfo['handle']);
                            if (in_array($code, array(301, 302, 303, 307), true)) {
                                $redirect[$k] = empty($redirect[$k]) ? 1 : ++$redirect[$k];
                                if($redirect[$k] > $args['maxredirect']) {
                                    $msg = "redirect larger than {$maxredirect} [{$k}][{$urls[$k]['url']}]";
                                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($msg , 'request_redirect_warn');
                                    self::_error(90406, $msg);
                                } else {
                                    defined('DAGGER_DEBUG') && BaseModelCommon::debug("redirect times:{$redirect[$k]},url:{$urls[$k]['url']}", 'request_redirect_info');
                                    preg_match('/Location:(.*?)\n/i', $ret[$k], $matches);
                                    $newurl = trim($matches[1]);
                                    if($newurl{0} === '/') {
                                        preg_match("@^([^/]+://[^/]+)/@", curl_getinfo($mhinfo['handle'], CURLINFO_EFFECTIVE_URL), $matches);
                                        $newurl = $matches[1] . $newurl;
                                    }
                                    $redoUrls[$k] = $urls[$k];
                                    $redoUrls[$k]['url'] = $newurl;
                                    defined('DAGGER_DEBUG') && BaseModelCommon::debug("[{$k}][old]{$urls[$k]['url']}[new]{$newurl}", 'request_redirect_url');
                                }
                            } else if($code !== 200) {
                                $msg = "http code unnormal : [{$code}] [{$k}][{$urls[$k]['url']}]";
                                defined('DAGGER_DEBUG') && BaseModelCommon::debug($msg, 'request_http_warn');
                                self::_error(90405, $msg);
                            }
                            if(strpos($ret[$k], "\r\n\r\n") !== false) {
                                if(empty($args['callback'])) {
                                    list($header[$k], $ret[$k]) = explode("\r\n\r\n", $ret[$k], 2);
                                    self::$last_header_info[$k] = $header[$k];
                                    // 抓取header时，解析header头
                                    if($args['headOnly']) {
                                        $ret[$k] = self::_parse_header($header[$k]);
                                    }
                                } else {
                                    call_user_func($args['callback'], $k, $ret[$k]);
                                }
                            } else {
                                $header[$k] = $ret[$k] = false;
                                empty($args['callback']) || call_user_func($args['callback'], $k, $ret[$k]);
                            }
                            if(in_array($code, array(403,404), true)) {
                                $ret[$k] = false;
                            }
                        } else {
                            $redo[$k] = empty($redo[$k]) ? 1 : ++$redo[$k];
                            if($redo[$k] <= $args['redo']) {
                                $redoUrls[$k] = $urls[$k];
                            } else {
                                self::$last_header_info[$k] = $header[$k] = $ret[$k] = false;
                                is_callable($args['callback']) && call_user_func($args['callback'], $k, $ret[$k]);
                            }
                            $curl_error = curl_error($mhinfo['handle']);
                            defined('DAGGER_DEBUG') && BaseModelCommon::debug("[errno] {$mhinfo['result']} [error] {$curl_error}",'request_curl_error');
                            self::_error(90404, "curl内部错误信息[{$mhinfo['result']}][{$urls[$k]['url']}]");
                        }
                        curl_multi_remove_handle($mh, $mhinfo['handle']);
                        curl_close($mhinfo['handle']);
                    }
                };
                // 添加需要再次请求的句柄，包括redirect和redo
                if(!empty($redoUrls)) {
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($redoUrls, 'request_redoUrl_info');
                    foreach($redoUrls as $k => $urlinfo) {
                        $_chs = curl_copy_handle($ch);
                        curl_setopt($_chs, CURLOPT_URL, $urlinfo['url']);
                        $_ch[$k] = $_chs;
                        curl_multi_add_handle($mh, $_ch[$k]);
                        BaseModelCommon::addStatInfo('request');
                    }
                }
            } while(!empty($redoUrls));
            curl_multi_close($mh);
            $runTime = BaseModelCommon::addStatInfo('request', $startRunTime, 0);

            // mc缓存处理
            if(self::$dagger_http_lock_times > 0) {
                foreach($ret as $k => $_r) {
                    if($_r === false) {
                        // 10秒钟内连续失败20次，30秒钟锁定，直接返回false;
                        if (!$mcd->addByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_false_keys[$k], 1, 10)) {
                            $falseCount = $mcd->incrementByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_false_keys[$k], 1, 1);
                            if ($falseCount > self::$dagger_http_lock_times - 1) {
                                $mcd->addByKey(self::DAGGER_HTTP_MC_SERVER_KEY, $mc_http_lock_keys[$k], 1, 30);
                            }
                        }
                    } else {
                        $mcd->deleteMultiByKey(self::DAGGER_HTTP_MC_SERVER_KEY, array($mc_http_false_keys[$k], $mc_http_lock_keys[$k]));
                    }
                }
            }

            if (defined('DAGGER_DEBUG')) {
                $d = array();
                foreach($ret as $k => $v) {
                    is_string($v) && ($len = strlen($v)) > 2000 && $v = (substr($v, 0, 2000) . '......超长('.$len.')，截取2000字节');
                    $d[$k] = array($k, $urls[$k]['url'], $v);
                }
                asort($d);
                array_unshift($d, array('序号', '请求地址', '执行结果'));
                $d[] = array('', '运行时间', $runTime);
                BaseModelCommon::debug($d, 'request_multi_return');
            }
        }
        return $ret;
    }

    private static function _error($errno, $error) {
        if(!in_array($errno, array(90401,90402), true) || defined('QUEUE')) {
            defined('DAGGER_DEBUG') && BaseModelCommon::debug("[errro code] {$errno} [errro msg] {$error} [详细说明]:https://github.com/wxkingstar/dagger/wiki/{$errno}", 'request_error');
            BaseModelLog::sendLog($errno, $error, '', BaseModelLog::ERROR_MODEL_ID_HTTP);
        } else {
            throw new BaseModelHTTPException($error, $errno);
        }
        return false;
    }

    private static function _parse_header(&$header) {
        if($header !== false) {
            $ret = array();
            $_headers = explode("\n", str_replace("\r", '', $header));
            foreach ($_headers as $value) {
                $_header = array_map('trim', explode(':', $value, 2));
                if (!empty($_header[0])) {
                    $_header[0] = trim($_header[0]);
                    $_header[0] = trim($_header[0]);
                    if (empty($_header[1])) {
                        $ret['status'] = $_header[0];
                    } else {
                        $ret[$_header[0]] = isset($ret[$_header[0]]) ? $ret[$_header[0]] . '; ' . $_header[1] : $_header[1];
                    }
                }
            }
            $header = $ret;
        }
        return $header;
    }

    private static function _set_curl_opts(&$ch, $args, $first = true) {
        // 本函数不设置url
        $opt = array();
        if($first) {
            $opt[CURLOPT_RETURNTRANSFER] = true;
            $opt[CURLOPT_SSL_VERIFYPEER] = false;
            $opt[CURLOPT_SSL_VERIFYHOST] = false;
            $opt[CURLOPT_MAXCONNECTS] = 100;
            $opt[CURLOPT_HEADER] = true;
            $opt[CURLOPT_TIMEOUT] = $args['timeout'];
            // useragent头
            if(!empty(self::$dagger_http_useragent)) {
                $opt[CURLOPT_USERAGENT] = self::$dagger_http_useragent;
            }
            // 只抓header头
            if ($args['headOnly']) {
                $opt[CURLOPT_NOBODY] = true;
            }
        }
        if($first || !empty($args['header'])) { 
            // header头
            $setheader = array();
            if (!empty($args['header']) && is_array($args['header'])) {
                foreach($args['header'] as $k => $v) {
                    if(is_numeric($k)) {
                        if($pos = strpos($v, ':')) {
                            $setheader[strtolower(substr($v, 0, $pos))] = $v;
                        }
                    } else {
                        $setheader[strtolower($k)] = "$k: $v";
                    }
                }
            }
            $setheader['expect'] = 'Expect:'; // 解决100问题
            $opt[CURLOPT_HTTPHEADER] = $setheader;
        }
        // post数据
        if (!empty($args['post'])) {
            //多维数组用http_build_query强制转换，curl不支持多维，转换后，无法支持文件提交
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($args['post'], 'request_post_data');
            if (is_array($args['post']) && count($args['post']) !== count($args['post'], COUNT_RECURSIVE)) {
                $args['post'] = http_build_query($args['post']);
            }
            $opt[CURLOPT_POST] = true;
            $opt[CURLOPT_POSTFIELDS] = $args['post'];
        }
        // cookie设置
        if (!empty($args['cookie'])) {
            $opt[CURLOPT_COOKIE] = $args['cookie'];
        }
        return curl_setopt_array($ch, $opt);
    }

    private static function _get_content($ch, $maxredirect) {
        $redirect = 0;
        do {
            $retry = false;
            $ret = curl_exec($ch);
            BaseModelCommon::addStatInfo('request');
            if(!self::_curl_check($ch)) {
                return false;
            }
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            if(in_array($code, array(301, 302, 303, 307), true)) {
                if(++$redirect <= $maxredirect) {
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($redirect, 'request_redirect_times');
                    preg_match('/Location:(.*?)\n/i', $ret, $matches);
                    $newurl = trim($matches[1]);
                    if($newurl{0} === '/') {
                        preg_match("@^([^/]+://[^/]+)/@", $url, $matches);
                        $newurl = $matches[1] . $newurl;
                    }
                    curl_setopt($ch, CURLOPT_URL, $newurl);
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($newurl, 'request_redirect_url');
                    $retry = true;
                } else {
                    $msg = "redirect larger than {$maxredirect} [{$url}]";
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($msg , 'request_redirect_warn');
                    self::_error(90406, $msg);
                }
            } else if($code !== 200) {
                $msg = "http code unnormal : [{$code}] [{$url}] [{$ret}]";
                defined('DAGGER_DEBUG') && BaseModelCommon::debug($msg, 'request_http_warn');
                self::_error(90405, $msg);
                if(in_array($code, array(403,404), true)) {
                    return false;
                }
            }
        } while($retry);
        return $ret;
    }

    private static function _curl_check($ch) {
        $curl_errno = curl_errno($ch);
        if ($curl_errno) {
            $curl_error = curl_error($ch);
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            defined('DAGGER_DEBUG') && BaseModelCommon::debug("[errno] {$curl_errno} [error] {$curl_error}", 'request_curl_error');
            self::_error(90404, "curl内部错误信息[{$curl_errno}][{$curl_error}][{$url}]");
            return false;
        }
        return true;
    }

}
