<?php
/**
 * All rights reserved.
 * Router基类 可兼容原框架apache路由重写
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
 *
 * @modified by     chen shuoshi <shuoshi@staff.sina.com.cn>
 * @time            2011/11/5 11:10
 * @version         Id: 1.0
 * @description     添加createUrl；添加默认controller和默认action；参数配置正则；
 */

class BaseModelRouter{

    public static $get = array();//记录原始传入get参数

    private static function match($rule, $param){
        list($paramKey, $paramVal) = explode('?', rtrim(ltrim($rule, '<'), '>'), 2);
        $prefix = '';
        if(strpos($paramVal, ':') !== false){
            list($regx, $prefix) = explode(':', $paramVal);
        }else{
            $regx = $paramVal;
        }
        preg_match("/^{$regx}$/", $param, $matches);
        if(!empty($matches[0])){
            $param = substr($param, strlen($prefix));
            $_GET[$paramKey] = $param;
            self::$get[$paramsArr] = $_GET[$paramKey];
            return true;
        }
        return false;
    }

    static private function init($app) {
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($app, 'router_choose_app');
        //app的基础目录
        $base = DAGGER_PATH_APP . $app . '/';   
        //app的controller目录
        $controllerPath = $base . 'controller/';
        //app的model目录
        $modelPath = $base . 'model/';
        //app的pagelet目录
        $pageletPath = $base . 'pagelet/';
        //app的templates目录
        $tempaltePath = $base . 'templates/';

        if (DAGGER_PLATFORM == 'sae') {
            //app的templats_c目录，SAE使用MC
            $templateCPath = "saemc://smartytpl/" . $app . '/templates_c/';
        } else {
            //app的templats_c目录
            $templateCPath = DAGGER_PATH_CACHE . $app . '/templates_c/';
            BaseModelCommon::recursiveMkdir($templateCPath);
        }

        define('DAGGER_PATH_APP_CTL', $controllerPath);        //app的controller
        define('DAGGER_PATH_APP_MODEL', $modelPath);           //app的model
        define('DAGGER_PATH_APP_PLT', $pageletPath);           //app的pagelet
        define('DAGGER_PATH_APP_TPL', $tempaltePath);          //app的templates
        define('DAGGER_PATH_APP_TPC', $templateCPath);         //app的templats_c
    }

    /*
     * 静态化路由函数，重写请在继承类中修改
     * @return void
     */
    public static function route() {
        if (isset($_GET[DAGGER_APP]) && preg_match('/^\w*$/i', $_GET[DAGGER_APP])) {
            Configure::$app = $_GET[DAGGER_APP];
        } else {
            Configure::getDefaultApp();
        }
        if (DAGGER_ROUTER == 1 && !empty(RouterConfig::$config[Configure::$app])) {
            $uri = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);
            //对于中文，已经变为urlencode，参数化的时候需要先decode出来
            $uri = urldecode($uri);
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($uri, 'router_request_uri');
            //从uri中过滤掉key value查询串
            $uri = explode('?', $uri);
            $uri = array_shift($uri);
            $uriArr = explode('/', trim($uri, '/'));
            $uriArrWithoutApp = array();
            //判断是否选择了app
            foreach ($uriArr as $uripart) {
                if (strpos($uripart, DAGGER_APP_PREFIX) === 0) {
                    Configure::$app = substr($uripart, strlen(DAGGER_APP_PREFIX));
                    self::$get[DAGGER_APP] = Configure::$app;
                } else if (strlen($uripart) > 0) {
                    $uriArrWithoutApp[] = $uripart;
                }
            }
            $uri = '/'.implode('/', $uriArrWithoutApp);
            //从URI中去除baseurl中的多级目录
            $baseUrlArr = explode('/', RouterConfig::$baseUrl[Configure::$app], 2);
            if(!empty($baseUrlArr[1])){
                $baseUrl = '/'.trim($baseUrlArr[1], '/');
                if (strpos($uri, $baseUrl) === 0) {
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug('url规则匹配上BaseUrl：'.$baseUrl, 'router_base_url');//匹配RouterConfig中设置的对应BaseUrl
                    $uri = substr($uri, strlen($baseUrl));
                    $uri === false && $uri = "";
                }
            }
            //将uri变为参数数组
            $paramsArr = explode('/', trim($uri, '/'));
            self::$get = array_merge(self::$get, $_GET);

            $configArr = RouterConfig::$config[Configure::$app];
            if (isset($configArr[$paramsArr[0]])) {
                /**
                * 从URI的第一个参数开始，搜索RouterConfig中的配置项。
                * 不能匹配的URI参数尝试匹配下一个RouterConfig配置项
                */
                $_GET[DAGGER_CONTROLLER] = $paramsArr[0];
                self::$get[DAGGER_CONTROLLER] = $_GET[DAGGER_CONTROLLER];
                array_shift($paramsArr);
                $configArr = $configArr[$_GET[DAGGER_CONTROLLER]];

                if (!isset($_GET[DAGGER_ACTION])) {
                    if (isset($paramsArr[0]) && isset($configArr[$paramsArr[0]])) {
                        $_GET[DAGGER_ACTION] = $paramsArr[0];
                        self::$get[DAGGER_ACTION] = $_GET[DAGGER_ACTION];
                        array_shift($paramsArr);
                    } elseif (isset(RouterConfig::$defaultRouter) && isset(RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[DAGGER_CONTROLLER]])) {
                        $_GET[DAGGER_ACTION] = RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[DAGGER_CONTROLLER]];
                    } else {
                        throw new BaseModelException("APP:".Configure::$app.",controller:".$_GET[DAGGER_CONTROLLER]."没有设置默认action", 90206, 'router_trace');
                    }
                }
                $configArr = explode('/', $configArr[$_GET[DAGGER_ACTION]]);
            } else {
                //检测是否有controller参数，没有使用默认设置
                if (!isset($_GET[DAGGER_CONTROLLER])) {
                    if (isset(RouterConfig::$defaultRouter) && isset(RouterConfig::$defaultRouter[Configure::$app]['default_controller'])) {
                        $_GET[DAGGER_CONTROLLER] = RouterConfig::$defaultRouter[Configure::$app]['default_controller'];
                    } else {
                        throw new BaseModelException("APP:".Configure::$app."没有设置默认Controller", 90205, 'router_trace');
                    }
                    if (isset($_GET[DAGGER_ACTION])) {
                        throw new BaseModelException("指定action参数时必须指定controller参数", 90207, 'router_trace');
                    }
                }
                if (!isset($_GET[DAGGER_ACTION])) {
                    //使用默认action设置
                    if (isset(RouterConfig::$defaultRouter) && isset(RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[DAGGER_CONTROLLER]])) {
                        $_GET[DAGGER_ACTION] = RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[DAGGER_CONTROLLER]];
                    } else {
                        throw new BaseModelException("APP:".Configure::$app.",controller:".$_GET[DAGGER_CONTROLLER]."没有设置默认action", 90206, 'router_trace');
                    }
                }
            }
            while (!empty($paramsArr[0])) {
                if (empty($configArr)) {
                    self::init(Configure::$app);
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug(RouterConfig::$config, "router_RouterConfig");
                    throw new BaseModelException("[app]:".Configure::$app." [controller]:{$_GET[DAGGER_CONTROLLER]} [action]:{$_GET[DAGGER_ACTION]} ，不识别“/".implode("/", $paramsArr)."”，请配置路由规则", 90200, 'router_trace');
                }
                if (self::match($configArr[0], $paramsArr[0])) {
                    array_shift($paramsArr);
                }
                array_shift($configArr);
            }
            defined('DAGGER_DEBUG') && BaseModelCommon::debug($_GET, 'router_$_GET');
        }
        $_GET[DAGGER_APP] = Configure::$app;
        self::init(Configure::$app);
    }

    private static function createNoRouterUrl($baseUrl, $controller, $action, $params=array(), $project='') {
        $paramsArr = array();
        if (!empty($project)) {
            $paramsArr[DAGGER_APP] = $project;
        }
        $paramsArr[DAGGER_CONTROLLER]   = $controller;
        $paramsArr[DAGGER_ACTION]  = $action;
        return $baseUrl.'?'.http_build_query(array_merge($paramsArr, (array)$params));
    }

    public static function createUrl($controller, $action, $params=array(), $project='', $baseUrl='') {
        $projectSpecified = $hidecontroller = $hideAction = false;
	if (!$params) {
		$params = array();
	}
        if (empty($project)) {
            if (!isset($params[DAGGER_APP])) {
                $app = Configure::$app;
            } else {
                $projectSpecified = true;
                $app = $params[DAGGER_APP];
                unset($params[DAGGER_APP]);
            }
        } else {
            $projectSpecified = true;
            $app = $project;
        }
        if (empty($baseUrl)) {
            if (!isset($params['baseUrl'])) {
                if (!empty(RouterConfig::$baseUrl[$app])) {
                    $baseUrl = RouterConfig::$baseUrl[$app];
                } else {
                    $baseUrl = $_SERVER['HTTP_HOST'];
                }
            } else {
                $baseUrl = $params['baseUrl'];
                unset($params['baseUrl']);
            }
        }
        if (strpos($baseUrl, 'http://') !== 0) {
            $baseUrl = rtrim("http://{$baseUrl}", '/').'/';
        }

        if (DAGGER_ROUTER == 0) {
            return self::createNoRouterUrl($baseUrl, $controller, $action, $params, $app);
        } else {
            if (empty($controller)) { /*将三元式变为if else，三元式效率较低
                $controller = isset($params[DAGGER_CONTROLLER]) ? $params[DAGGER_CONTROLLER] : $defaultcontroller;*/
                if (!isset($params[DAGGER_CONTROLLER])) {
                    $hidecontroller = true;
                    $controllers = RouterConfig::$config[$app];
                    if (empty($controllers)) {
                        throw new BaseModelException("使用createUrl时未指定controller", 90201, 'router_trace');
                    }
                    $controller = array_pop(array_keys($controllers));
                } else {
                    $controller = $params[DAGGER_CONTROLLER];
                    unset($params[DAGGER_CONTROLLER]);
                }
            }
            if (empty($action)) {
                /*$action = isset($params[DAGGER_ACTION]) ? $params[DAGGER_ACTION] : $defaultAction;*/
                if (!isset($params[DAGGER_ACTION])) {
                    $hideAction = true;
                    $actions = RouterConfig::$config[$app][$controller];
                    if (empty($actions)) {
                        throw new BaseModelException("使用createUrl时未指定action", 90202, 'router_trace');
                    }
                    $action = array_pop(array_keys($actions)); 
                } else {
                    $action = $params[DAGGER_ACTION];
                    unset($params[DAGGER_ACTION]);
                }
            }
            $url = '';
            if (isset(RouterConfig::$config[$app][$controller][$action])) {
                if ($projectSpecified === true) {
                    $url .= '/'.urlencode(DAGGER_APP_PREFIX.$app);
                }
                if ($hidecontroller === false) {
                    $url .= '/'.urlencode($controller);
                }
                if ($hideAction === false) {
                    $url .= '/'.urlencode($action);
                }
                $configArr = explode('/', RouterConfig::$config[$app][$controller][$action]);
                if (!empty($configArr[0])) {
                    $confParamArr = array(); 
                    foreach ($configArr as $config) {
                        list($paramKey, $paramVal) = explode('?', rtrim(ltrim($config, '<'), '>'), 2);
                        if (isset($params[$paramKey])) {
                            if(strpos($paramVal, ':')) {
                                list($regx, $prefix) = explode(':', $paramVal);
                                $confParamArr[] = urlencode($prefix.$params[$paramKey]);
                            } else {
                                $confParamArr[] = urlencode($params[$paramKey]);
                            }
                            unset($params[$paramKey]);
                        }
                    }
                    if (!empty($confParamArr)) {
                        $url .= '/'.implode('/', $confParamArr);
                    }
                }
                //'?'开始的key value查询串前以'/'结尾
                $url .= '/';
                if (!empty($params)) {
                    $url .= '?'.http_build_query($params);
                }
                return $baseUrl.ltrim($url, '/');
            } else {
                return self::createNoRouterUrl($baseUrl, $controller, $action, $params, $app);
            }
        }
    }

    public static function delUrlParams($controller, $action, $params, $project){
        $delParams = $_GET;
        if (is_array($params)) {
            foreach ($params as $param) {
                unset($delParams[$param]);
            }
        } else {
            throw new BaseModelException('delUrlParams函数缺少params参数', 90203, 'router_trace');
        }
        /*
         * 在渲染页面时，$_GET[DAGGER_CONTROLLER]和$_GET[DAGGER_ACTION]将使用本次请求计算出的DAGGER_CONTROLLER和DAGGER_ACTION
         * 此时如果$controller/$action不为空，则需要将$delParams[DAGGER_CONTROLLER]/$delParams[DAGGER_ACTION]去掉
         * 因为在createUrl时，将优先使用addParams中的$delParams[DAGGER_CONTROLLER]/$delParams[DAGGER_ACTION]
         * 作为DAGGER_CONTROLLER/DAGGER_ACTION
         */
        if (!empty($project)) {
            unset($delParams[DAGGER_APP]);
        }
        if (!empty($controller)) {
            unset($delParams[DAGGER_CONTROLLER]);
        }
        if (!empty($action)) {
            unset($delParams[DAGGER_ACTION]);
        }
        return self::createUrl($controller, $action, $delParams, $project);
    }

    public static function addUrlParams($controller, $action, $params, $project) {
        $addParams = $_GET;
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $addParams[$k] = $v;
            }
        } else {
            throw new BaseModelException('addUrlParams函数缺少params参数', 90204, 'router_trace');
        }
        /*
         * 在渲染页面时，$_GET[DAGGER_CONTROLLER]和$_GET[DAGGER_ACTION]将使用本次请求计算出的DAGGER_CONTROLLER和DAGGER_ACTION
         * 此时如果$controller/$action不为空，则需要将$addParams[DAGGER_CONTROLLER]/$addParams[DAGGER_ACTION]去掉
         * 因为在createUrl时，将优先使用addParams中的$addParams[DAGGER_CONTROLLER]/$addParams[DAGGER_ACTION]
         * 作为DAGGER_CONTROLLER/DAGGER_ACTION
         */
        if (!empty($project)) {
            unset($addParams[DAGGER_APP]);
        }
        if(!empty($controller)){
            unset($addParams[DAGGER_CONTROLLER]);
        }
        if(!empty($action)){
            unset($addParams[DAGGER_ACTION]);
        }
        return self::createUrl($controller, $action, $addParams, $project);
    }
}
