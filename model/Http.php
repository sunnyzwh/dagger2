<?php
/**
 * 有bug请自行修改
 * @author 
 */
class Http extends BaseModelHttp {
	/**
	 * 默认的MC失效时间
	 * @var int
	 */
	protected static $MC_EXPIRE_TIME = 600;

	/**
	 * 输出读取MC数据的代码信息
	 * @author 
	 */
	protected static function debugMCInfo() {
		if (defined("DAGGER_DEBUG")) {
			$trace = debug_backtrace(false);
			$trace = $trace[2];
			BaseModelCommon::debug($trace['file'] . '_' . $trace['line'], 'mc_' . $trace['class'] . '_' . $trace['function']);
		}
	}

	/**
	 * 请求http地址前先查MC是否缓存了结果
	 * @param string $req 请求url，必传参数
	 * @param int $expire MC失效时间，默认10分钟。
	 * @param array $header 默认为空。示例$header = array('Host: www.dagger.com');
	 * @param int $timeout 超时设定,默认为5秒
	 * @param string $cookie 默认为空。示例 $cookie="fruit=apple; colour=red";
	 * @param int $redo 请求失败后重试次数，默认为0
	 * @param int $maxredirect 如果遇上跳转，跳转几次后返回。默认为2
	 * @return string
	 * @author 
	 */
	public static function getWithMC($req, $expire = '', $header, $timeout = BaseModelHttp::DAGGER_HTTP_TIMEOUT, $cookie = '', $redo = 1, $maxredirect = BaseModelHttp::DAGGER_HTTP_MAXREDIRECT) {
		self::debugMCInfo();
		$mc = new MyMemcache();
		$mcResultKey = __FUNCTION__ . '|' . md5(serialize(func_get_args()));
		$result = $mc->get($mcResultKey);
		if (empty($result)) {
			$result = self::get($req, $header, $timeout, $cookie, $redo, $maxredirect);
			$expire = intval($expire);
			if (empty($expire)) {
				$expire = Http::$MC_EXPIRE_TIME;
			}
			$mc->set($mcResultKey, $result, $expire);
		}
		return $result;
	}
}
