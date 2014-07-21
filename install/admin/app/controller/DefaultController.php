<?php
//后台基类
//@author wangxin3@
//virsion 1.0

class DefaultController extends BaseController{

    protected $adminUserName;   //管理员帐号

    protected $ip;              //客户端IP

    public function __construct($controller, $action) {
        parent::__construct($controller, $action);
        $this->setView('basePath', empty(RouterConfig::$baseUrl[Configure::$app]) ? "." : "http://".RouterConfig::$baseUrl[Configure::$app]."/");
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }
}
?>
