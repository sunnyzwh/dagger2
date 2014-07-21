<?php
abstract class BaseController {

    /**
     * 
     * 模板变量 
     */
    protected $view = array();

    /**
     *
     * 控制器
     */
    protected static $controller;

    /**
     *
     * 控制器方法
     */
    protected static $action;

    public function __construct($controller, $action) {
        self::$controller = $controller;
        self::$action = $action;
    }

    /**
     *
     * 控制器执行
     */
    public function runCommand() {
        //请求方法和来源
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                break;
            case 'POST':
                if (BaseModelSwitch::check(BaseModelSwitch::SWITCH_POST_REFERER_CHECK) === true) {
                    $forbid = true;
                    if(!empty($_SERVER['HTTP_REFERER'])) {
                        $parseReferer = parse_url($_SERVER['HTTP_REFERER']);
                        if(!empty($parseReferer['host']) && preg_match("/^[\w-\.]+$/", $parseReferer['host'])) {
                            foreach ($_SERVER['SERVER_ACCEPT_REFERER'] as $referer) {
                                if($referer === $parseReferer['host'] || ('.' . $referer === substr($parseReferer['host'], -(strlen($referer)+1)))) {
                                    $forbid = false;
                                    break;
                                }
                            }
                        }
                    }
                    if ($forbid) {
                        throw new BaseModelException('请求源不允许[' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') .']', 90100, 'controller_trace');
                    }
                }
                break;
            case 'HEAD':
                break;
            default:
                throw new BaseModelException('请求方法不允许', 90101, 'controller_trace');
        }
        $action = BaseModelCommon::getFormatName(self::$action);
        if(in_array($action, array('runCommand', 'setView', 'display', 'fetch'), true)){
            $controllerName = BaseModelCommon::getFormatName(self::$controller, 'class');
            throw new BaseModelException($controllerName .'Controller类中方法'.$action.'为基类方法不能使用，您现在指向的app-controller为：app/'.Configure::$app . '/controller/', 90102, 'controller_trace');
        }
        if(method_exists($this, $action)) {
            call_user_func_array(array(&$this, $action),array());
        } else {
            $controllerName = BaseModelCommon::getFormatName(self::$controller, 'class');
            throw new BaseModelException($controllerName .'Controller类中不存在你调用的方法'.$action.'，您现在指向的app-controller为：app/'.Configure::$app . '/controller/', 90103, 'controller_trace');
        }
    }

    /**
     *
     * 设置模版变量
     * @param string $key  模板变量名
     * @param mixed $value 模板变量值
     */
    protected function setView($key, $value) {
        $this->view[$key] = $value;
    }

    /**
     *
     * 显示模版
     * @param string $tplFile
     * @return 
     */
    protected function display($tplFile) {
        echo $this->fetch($tplFile);
    }

    /**
     *
     * 返回解析内容
     * @param string $tplFile
     * @return html
     */
    protected function fetch($tplFile) {
        if (DAGGER_TEMPLATE_ENGINE == 'smarty') {
            $tpl = new BaseViewSmarty();
        } else {
            $tpl = new BaseView();
        }
        $tpl->assign($this->view);
        return $tpl->fetch($tplFile);
    }

    /**
     * 
     * 重定向后改变url
     * @param string $url 指定的url
     */
    protected function redirectTo($url) {
        header('Location: '.$url);
        exit();
    }

    /**
     *
     * 重定向后不改变url
     * @param string $controller 指定的controller
     * @param string $action 指定的action
     * @param array $params 参数数组
     */
    protected function forward($controller, $action, $params) {
        self::$controller = $controller;
        self::$action = $action;
        $_GET = $params;
        $this->runCommand();
    }



}
