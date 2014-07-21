<?php
class UserModel {
    /**
     * attribute
     */
    protected $uid;
    protected $email;
    protected $ip;
    protected $ua;
    protected $role = 'guest';

    /**
     * construct function
     */
    public function __construct(){
        $this->ip = $this->getUserIp();
        $this->ua = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * get user ip
     */
    public function getUserIp() {
        return Ip::getClientIp();
    }

    /**
     * set cookie value
     */
    public function setUserCookie($userInfo=array()) {
        if(empty($userInfo)){
            $userInfo = array(
                'uid'=>$this->uid,
                'email'=>$this->email,
            );
        }
        $userInfo['ip'] =  $this->ip;
        $userInfo['ua'] = substr($this->ua, 0, 10);
        $userInfo['time'] = time();

        $userInfoStr = serialize($userInfo);
        $encryptModel = new BaseModelEncrypt();
        $x = $encryptModel->rsa_public_encode($userInfoStr);
        //set encrypt cookie to user browser
        $isSuccess = setcookie('nbnlp', $x, strtotime('+10 days'),'/');
        if(!$isSuccess) {
            //TODO::Log set cookie failed
            return false;
        }
        return ($isSuccess && !empty($x)) ? true : false;
    }

    /**
     * get cookie value
     */
    private function getUserCookie() {
        $userCookie = isset($_COOKIE['nbnlp']) ? trim($_COOKIE['nbnlp']) : '';
        if(empty($userCookie)) {
            return false;
        }
        return $userCookie;
    }

    /**
     * load user, read cookie login
     */
    public function loadUser() {
        //read cookie
        $userCookie = $this->getUserCookie();
        if(!empty($userCookie)) {
            //decrept cookie
            $encryptModel = new BaseModelEncrypt();
            $userCookieSerialStr = $encryptModel->rsa_private_decode($userCookie);
            $userCookieArr = unserialize($userCookieSerialStr);
            //print_r($userCookieArr);
            if(!empty($userCookieArr['uid'])){
                $userModelDb = new UserModelDB();
                //fetch db to validate register user
                $user = $userModelDb->getUserByUid($userCookieArr['uid']);
            }
            if(!empty($user)) {
                $this->uid = $user['id'];
                $this->email = $user['email'];
                $this->role = 'member';
            }
        }
        return array('uid'=>$this->uid, 'email'=>$this->email, 'ip'=>$this->ip, 'ua'=>$this->ua,'role'=>$this->role);

    }

    /**
     * login model
     * @param $email
     * @param $password
     */
    public function login($email, $password){
        if(empty($email) || empty($password)) {
            return false;
        }
        $userModelDb = new UserModelDB();
        //fetch db to validate register user
        $user = $userModelDb->getUser($email, sha1($password));
        if(!empty($user)) {
            $this->email = $user['email'];
            $this->role = 'member';
            $this->uid = $user['id'];
            $this->setUserCookie();
            return true;//login success
        } else {
            //no register user, login failed
            return false;
        }
    }
}
