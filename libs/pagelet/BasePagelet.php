<?php

interface _pagelet {
    public function run($params);
}

class BasePagelet implements _pagelet {

    /**
     * @var $view 向pagelet中注册的变量数组
     */
    public $view = array();

    /**
     * @var $stack pagelet栈
     */
    protected static $stack = array();

    /**
     * pagelet逻辑执行函数,需要继承实现
     */
    public function run($params){
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function setView($key, $value = '') {
        if (is_array($key)) {
            $this->view = array_merge($this->view, $key);
        } else {
            $this->view[$key] = $value;
        }
    }

    /**
     * @param string $tplFile
     */
    protected function display($tplFile) {
        echo $this->fetch($tplFile);
    }

    /**
     * @param string $tplFile
     */
    protected function fetch($tplFile) {
        if (DAGGER_TEMPLATE_ENGINE === 'smarty') {
            $tpl = new BaseViewSmarty();
        } else {
            $tpl = new BaseView();
        }
        $tpl->assign($this->view);
        return trim($tpl->fetch($tplFile));
    }

    /**
     * @params array $params
     */
    public static function factory($pagelet, $params) {
        $pageletId = BaseModelCommon::getFormatName($pagelet.'_pagelet', 'class');
        //if(!DEBUG && !isset($_GET['nojs'])) {
        //暂时关闭bigpipe
        if(false) {
            self::$stack[$pageletId] = $params;
            echo '<div id="pagelet_'.strtolower($pageletId).'"></div>';
        } else {
            self::render($pageletId, $params);
        }
    }

    /**
     * @params string $pageletId
     * @params array $params
     */
    public static function render($pageletId, $params=array()) {
        $pagelet = new $pageletId;
        $pagelet->run($params);
    }

    /**
     * bigpipe 执行函数
     */
    public static function bigpipe() {
        //pop stack and call pagelet render
        foreach(self::$stack as $pageletId=>$params){
            ob_start();
            self::render($pageletId, $params);
            $content = ob_get_contents();
            ob_end_clean();
            echo $content;
        }
    }
}
