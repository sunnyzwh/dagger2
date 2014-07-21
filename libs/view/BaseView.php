<?php
class BaseView {

    protected $view = array();

    protected $tplDir;

    public function __construct () {
        $this->tplDir = DAGGER_PATH_APP_TPL;
    }

    public function setView($key, $value = "", $allowXss = false) {
        $this->assign($key, $value, $allowXss);
    }

    public function assign($key, $value = "", $allowXss = false) {
        if (is_array($key)) {
            if ($allowXss === false) {
                foreach ($key as $k=>$v){
                    if(is_string($v))
                        $key[$k] = htmlspecialchars($v, ENT_QUOTES, 'utf-8');
                }
            }
            $this->view = array_merge($this->view, $key);
        } else {
            if ($allowXss === false) {
                $value = htmlspecialchars($value, ENT_QUOTES, 'utf-8');
            }
            $this->view[$key] = $value;
        }
    }

    public function fetch($tplFile) {
        $tplFile = $this->tplDir . $tplFile;
        if (!is_file($tplFile)) {
            throw new BaseModelException("模版文件：{$tplFile} 未找到", 90108, 'dagger_trace');
        }
        extract($this->view);
        ob_start();
        include($tplFile);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    public function display($tplFile) {
        echo $this->fetch($tplFile);
    }
}
