<?php
class MyController extends BaseController
{
    public $user;
    /**
     * construct function
     */
    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        $um  = new UserModel();
        $this->user = $um->loadUser();
        $this->setView('curUser', $this->user);
    }

    protected function isPost(){
        if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
            return true;
        }
        return false;
    }
    protected function putErrorMsg($txt){
        $_SESSION['errorMsg'][] = $txt;
    }

    protected function getErrorMsg(){
        $msg = $_SESSION['errorMsg'];
        $_SESSION['errorMsg'] = array();
        return $msg;
        
    }

    protected function isErrorMsgEmpty(){
        return empty($_SESSION['errorMsg']);
    }

    protected function putMsg($txt){
        $_SESSION['msg'][] = $txt;
    }

    protected function getMsg(){
        $msg = $_SESSION['msg'];
        $_SESSION['msg'] = array();
        return $msg;
        
    }

    protected function isMsgEmpty(){
        return empty($_SESSION['msg']);
    }
}
