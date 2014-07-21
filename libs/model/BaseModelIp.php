<?php
/**
 * All rights reserved.
 * IP基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
*/

class BaseModelIp {

    private function __construct() {
        return false;
    }

    /**
     *   * 获取客户端IP地址
     *   * @return String
     */
    public static function getClientIp() {
        $onlineip = '';
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = empty($onlineipmatches[0]) ? '0.0.0.0' : $onlineipmatches[0];
        return $onlineip;
    }

    /**
     *   * 获取客户端真实IP地址
     *   * @return String
     */
    public static function getRealClientIp() {
        return $_SERVER['REMOTE_ADDR'];
    }
}

