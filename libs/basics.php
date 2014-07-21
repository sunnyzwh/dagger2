<?php
class DaggerAutoLoad{

    /**
     * 框架核心组件
     */
    private static $coreClass = array(
        'PEAR_Error'                    =>  array('filename'=>'PEAR'),
        'PHPUnit_Framework_TestCase'    =>  '',
        'FirePHP'                       =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'FirePHPCore/'), 'postfix'=>'.class'),
        'ChromePhp'                     =>  DAGGER_PATH_LIBS_MODEL,
        'PHPExcel'                      =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'PHPExcel/')),
        'PHPMailer'                     =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'PHPMailer/'), 'postfix'=>'.class'),
        'Smarty'                        =>  array('path'=>array(DAGGER_PATH_LIBS_VIEW), 'postfix'=>'.class'),
        'Configure'                     =>  DAGGER_PATH_CONFIG,
        'SysInitConfig'                 =>  DAGGER_PATH_CONFIG,
        'DBConfig'                      =>  DAGGER_PATH_CONFIG,
        'RouterConfig'                  =>  DAGGER_PATH_CONFIG,
        'DictConfig'                    =>  DAGGER_PATH_CONFIG,
        'RedisConfig'                   =>  DAGGER_PATH_CONFIG,
        'SSOConfig'                     =>  DAGGER_PATH_CONFIG,
        'EncryptConfig'                 =>  DAGGER_PATH_CONFIG,
        'BaseModelCommon'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelDebug'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelCookie'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelCrypt'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelDB'                   =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelDBConnect'            =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelEncrypt'              =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelExcel'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelFile'                 =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelFilter'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelHttp'                 =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelIp'                   =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelImage'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelLog'                  =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelMailer'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelMemcache'             =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelMemcached'            =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelMessage'              =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelPage'                 =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelRedis'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelRouter'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelRouterCompatible'     =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelSession'              =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelSimpleStorage'        =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelSwitch'               =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelUnitTest'             =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelValidate'             =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelXML'                  =>  DAGGER_PATH_LIBS_MODEL,
        'BaseModelXPath'                =>  DAGGER_PATH_LIBS_MODEL,
        'BaseView'                      =>  DAGGER_PATH_LIBS_VIEW,
        'BaseViewSmarty'                =>  DAGGER_PATH_LIBS_VIEW,
        'BaseController'                =>  DAGGER_PATH_LIBS_CTL,
        'BasePagelet'                   =>  DAGGER_PATH_LIBS_PLT,
        'BaseModelDBException'          =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'exceptions/')),
        'BaseModelHTTPException'        =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'exceptions/')),
        'BaseModelMCException'          =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'exceptions/')),
        'BaseModelS3Exception'          =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'exceptions/')),
        'BaseModelException'            =>  array('path'=>array(DAGGER_PATH_LIBS_MODEL, 'exceptions/')),
        'SaeTOAuthV2'    				=>	array('path'=>array(''), 'name'=>'saetv2', 'postfix'=>'.ex.class'), //only for SAE platform
        //'SaeTOAuthV2'					=>	array('path'=>array(DAGGER_PATH_MODEL), 'name'=>'saetv2', 'postfix'=>'.ex.class'), //non-SAE
        );

    /**
     * 框架加载器，用以结合其它模块的autoloader。
     * 具体使用方法可以参考框架的Smarty.class.php
     * @param string $autoloader 其它autoloader函数名
     */
    public static function register($autoloader){
        $loaders = spl_autoload_functions();
        foreach($loaders as $loader){
            spl_autoload_unregister($loader);
        }
        spl_autoload_register($autoloader);
        foreach($loaders as $loader){
            spl_autoload_register($loader);
        }
    }

    /**
     * 文件引用器
     * @param string $prefixPath 文件所在文件夹绝对路径
     * @param string $filename 文件名
     * @param strint $postfix 文件后缀
     */
    public static function includeFile($prefixPath, $filename, $postfix = '.php') {
        $_file = $prefixPath.$filename.$postfix;
        if (is_file($_file)) {
            include($_file);
        } else if(!include($_file)) {
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($filename . '类include文件：' . $_file . '不存在，您现在指向的app为：app/'.Configure::$app.'/', 'error');
            $trace = debug_backtrace();
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($trace, __METHOD__);
            $filename = htmlspecialchars($filename);
            if ($filename == 'Controller') {
                defined('DAGGER_DEBUG') && BaseModelCommon::debug(RouterConfig::$config, 'router_config');
                BaseModelMessage::showError('请配置RouteConfig.php中' . htmlspecialchars(Configure::$app, ENT_QUOTES, 'UTF-8') . '的DAGGER_APP路由信息');
            } else {
                BaseModelMessage::showError('class:'.$filename.' not found in app:'.htmlspecialchars(Configure::$app, ENT_QUOTES, 'UTF-8'));
            }
        }
    }

    /**
     * 框架autoloader
     * @param string $classname 要加载的类名
     */
    public static function loader($classname) {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $classname)) {
            exit();
        }
        if (isset(self::$coreClass[$classname])) {
            $path = '';
            $filename = $classname;//默认文件名称和类名称相同
            if(!is_array(self::$coreClass[$classname])) {
                $path = self::$coreClass[$classname];
            } else {
                if(isset(self::$coreClass[$classname]['path'])) {
                    $path = implode('', self::$coreClass[$classname]['path']);
                }
                if(isset(self::$coreClass[$classname]['filename'])) {
                    $filename = self::$coreClass[$classname]['filename'];//用手工配置的文件名称覆盖默认的
                }
                if(isset(self::$coreClass[$classname]['postfix'])) {
                    $filename .= self::$coreClass[$classname]['postfix'];
                }
            }
            self::includeFile($path, $filename);
        } else {
            /**
             * 对于外部使用Dagger，不要去找文件结尾为Controller、
             * DB或者其它任何没有被加载的文件
             */
            if (preg_grep('/.*?Controller$/', array($classname))) {
                self::includeFile(DAGGER_PATH_APP_CTL, $classname);
            } else if (preg_grep('/.*?ModelDB$/', array($classname))) {
                self::includeFile(DAGGER_PATH_MODEL . 'db/', $classname);
            } else if (preg_grep('/.*?Config$/', array($classname))) {
                self::includeFile(DAGGER_PATH_CONFIG, $classname);
            } else if (preg_grep('/.*?Pagelet$/', array($classname))) {
                self::includeFile(DAGGER_PATH_APP_PLT, $classname);
            } else if (defined('DAGGER_PATH_APP_MODEL') && file_exists(DAGGER_PATH_APP_MODEL."{$classname}.php")) {
                if (file_exists(DAGGER_PATH_MODEL."{$classname}.php")) {
                    BaseModelLog::sendLog(90107, "{$classname}.php文件在/app/" . htmlspecialchars(Configure::$app, ENT_QUOTES, 'UTF-8')."/model目录和/model目录重名", '', BaseModelLog::ERROR_MODEL_ID_PHP);
                    BaseModelMessage::showError("{$classname}.php文件在/app/" . htmlspecialchars(Configure::$app, ENT_QUOTES, 'UTF-8')."/model目录和/model目录重名");
                }
                self::includeFile(DAGGER_PATH_APP_MODEL, $classname);
            } else {
                self::includeFile(DAGGER_PATH_MODEL, $classname);
            }
        }
    }
}

spl_autoload_register(array('DaggerAutoLoad', 'loader'));

/**
 * 在firephp中捕获fatal异常，用以display_error
 * 不开的情况下调试网站
 */
function daggerShutDown() {
    if(!defined('QUEUE')) {
        // 超时报警
        $runtime = floatval(BaseModelCommon::getRunTime());
        $timeout = DAGGER_TIMEOUT;
        if($runtime > $timeout) {
            $statInfo = BaseModelCommon::getStatInfo();
            BaseModelLog::sendLog(90106,
                    "[run:{$runtime}s]" . 
                    "[db:({$statInfo['db']['count']}次{$statInfo['db']['time']}ms)]" . 
                    "[mc:({$statInfo['mc']['count']}次{$statInfo['mc']['time']}ms)]" . 
                    "[request:({$statInfo['request']['count']}次{$statInfo['request']['time']}ms)]" . 
                    "[redis:({$statInfo['redis']['count']}次{$statInfo['redis']['time']}ms)]",
                    '',
                    BaseModelLog::ERROR_MODEL_ID_PHP);
            1 === DAGGER_DEBUG && BaseModelCommon::debug("当前脚本运行[{$runtime}]，大于{$timeout}秒", 'timeout_error');
        }
    }
    //xhprof
    if(defined('DAGGER_XHPROF_ID')) {
        BaseModelCommon::debug(BaseModelDebug::getXhprofUrl(), 'xhprof_url');
    }
    // all_info的debug信息
    if(1 === DAGGER_DEBUG) {
        $statInfo = BaseModelCommon::getStatInfo();
        BaseModelCommon::debug(
            array(
                array('资源',       '次数',                                 '消耗时间(ms)'                      ),
                array('sql',        $statInfo['db']['count'].' 次',         $statInfo['db']['time'] . ' ms'     ),
                array('request',    $statInfo['request']['count'].' 次',    $statInfo['request']['time'] . ' ms'),
                array('mc',         $statInfo['mc']['count'].' 次',         $statInfo['mc']['time'] . ' ms'     ),
                array('redis',      $statInfo['redis']['count'].' 次',      $statInfo['redis']['time'] . ' ms'  ),
                array('总运行时间', '',                                     BaseModelCommon::getRunTime()       )
            ),
            'all_info'
        );
        empty(BaseModelCommon::$debugTypeFilter) || BaseModelCommon::debug('部分debug信息被隐藏，规则：'.BaseModelCommon::$debugTypeFilter, 'filter_info');
        BaseModelCommon::debug(DAGGER_VERSION, 'dagger_version');
    }

    if(!defined('QUEUE')) {
        // 在线调试
        if (1 === DAGGER_ONLINE_DEBUG) {
            BaseModelCommon::sendOnlineDebug();
        }
        ob_end_flush();
    }
    $error = error_get_last();
    if (empty($error) || !(E_ERROR & $error['type'])) {
        // This error code is not included in error_reporting
        defined('QUEUE_CTL') && QueueTaskCtl::end(0, '');
        return false;
    }
    formatErrorInfo($error['type'], $error['message'], $error['file'], $error['line']);
    defined('QUEUE_CTL') && QueueTaskCtl::end(1, $error['message']);
    return true;
}

/**
 * 调试输出错误信息
 */
function formatErrorInfo($errno, $errstr, $errfile, $errline) {
    $errstr = strip_tags($errstr);
    $myerror = "$errstr in $errfile on line $errline";

    switch ($errno) {
        case E_WARNING:
            $myerror = "==Warning==" . $myerror;
            BaseModelLog::sendLog(90104, $myerror, '', BaseModelLog::ERROR_MODEL_ID_PHP);
            break;
        case E_ERROR:
            $myerror = "==Fatal error==" . $myerror;
            BaseModelLog::sendLog(90105, $myerror, '', BaseModelLog::ERROR_MODEL_ID_PHP);
            break;
        case E_NOTICE:
            $myerror = "==Notice==" . $myerror;
            break;
        case E_USER_ERROR:
            $myerror = "==My error==" . $myerror;
            break;
        case E_USER_WARNING:
            $myerror = "==My warning==" . $myerror;
            break;
        case E_USER_NOTICE:
            $myerror = "==My notice==" . $myerror;
            break;
        default:
            $myerror = "==Unknown error type [$errno]==" . $myerror;
            break;
    }

    if (1 === DAGGER_DEBUG) {
        if(defined('QUEUE') || E_NOTICE & $errno) {
            // echo $myerror . "\n";
        } else if(empty($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'FirePHP') === false) {
            echo '<table cellspacing="0" cellpadding="1" border="1" dir="ltr" class="xdebug-error"><tbody>'
                .'<tr><th bgcolor="#f57900" align="left" colspan="5">'
                .'<span style="background-color: #cc0000; color: #fce94f; font-size: x-large;">( ! )</span>'.$myerror.'</th></tr>'
                .'</tbody></table><br /><br />';
        }
        BaseModelCommon::debug($myerror, 'error');
    }
}

register_shutdown_function('daggerShutDown');

/**
 * 在firephp中捕获异常，用以display_error
 * 不开的情况下调试网站
 */
function daggerErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    formatErrorInfo($errno, $errstr, $errfile, $errline);

    /* Don't execute PHP internal error handler */
    return true;
}

set_error_handler("daggerErrorHandler");

