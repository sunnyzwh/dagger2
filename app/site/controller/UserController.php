<?php

/**
 * Created by PhpStorm.
 * User: zhangwenhan
 * Date: 14-5-18
 * Time: 下午4:54
 */
class UserController extends MyController
{
    /**
     * construct function
     */
    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    /**
     * login action
     */
    public function login()
    {
        $userModel = new UserModel();
        //read cookie to auto login
        if($this->user['role'] == 'member') {
            Message::showSucc('已经登陆过了...Login Success');
        }

        if($this->isPost()){
            //login by email and password
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                //tip: email or password cannot empty
                Message::showError('邮箱或密码不得为空');
            }
            if (Validate::check($email, 'varchar', '1_email') == true) {
                //email validate success
                $isLogin = $userModel->login($email, $password);
                if($isLogin) {
                    Message::showSucc('Login Success');
                } else {
                    Message::showError('Login Failed');
                }
            } else {
                //tip: please input valid email
                Message::showError('Please input valid email');
            }

        }


        $this->display('login.html');
    }

    public function register(){
   
  
        if($this->isPost()){
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $password2 = trim($_POST['password_repeat']);
            if($password != $password2){
                $this->putErrorMsg('两次密码不一致');
            }
            if(!preg_match('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/', $password)){
                $this->putErrorMsg('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/');
            }
                
            if(empty($email)){
                $this->putErrorMsg('email不能为空');
            }else if(true != Validate::check($email,'varchar', '1_email') ){
                $this->putMsg('不是email');
           }
            if($this->isErrorMsgEmpty()){
                $userModelDB = new UserModelDB();
                $r =  $userModelDB->save($email, sha1($password));
                $uid = $userModelDB->insertId();
                if($r){
                    $this->putmsg('注册成功');
                    $um = new UserModel();
                    $succ = $um->setUserCookie(array('email'=>$email, 'uid'=>$uid));
                    if(!$succ){
                        $this->putErrorMsg('您居然把cookie关了...');
                    }
                }else{
                    $this->putErrorMsg('注册失败'.$r);
                }
            }
        }

        var_dump($this->getMsg());
        var_dump($this->getErrorMsg());
        $this->setView('msg', $this->getMsg());
        $this->setView('errorMsg', $this->getErrorMsg());
        $this->display('register.html');
    }

    public function findPassword(){
        if($this->isPost()){
            $code = sha1(uniqid());
            $email = trim($_POST['email']);
            if(empty($email)){
                Message::showError('email is empty');
            }
            $userModelDB = new UserModelDB();
            $user = $userModelDB->getUserByEmail($email);
            if(empty($user)){
                Message::showError('email 不存在');
            }
            $passwordRequestModelDB =new PasswordRequestModelDB();
            $expiration = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $succ = $passwordRequestModelDB->save($user['id'], $code, $expiration);
            if($succ){
                $mailer = new Mailer();
                $mailer->send($user['email'], 'missy-blue 找回密码', "http://missy-blue.com/user/reset_password?code=".$code);
                Message::showSucc('去看你的邮箱');

            }else{
                Message::showSucc('failed......... ');
            }
        
        }
    
        $this->display('find_password.html');
    }

    public function resetPassword(){
        $allowResetPassword = false;
        $passwordRequestModelDB =new PasswordRequestModelDB();
        if(isset($_GET['code'])){
            //find user_id
            //todo 判断过期
            $row  = $passwordRequestModelDB->getByCode(trim($_GET['code']));
            if(!empty($row)){
                
                 $userModelDB = new UserModelDB();
                 $user = $userModelDB->getUserByUid($row['user_id']);
                 if(!empty($user)){
                     $allowResetPassword = true;
                     $this->setView('user',$user);
                 }
            }

            $this->setView('code', $_GET['code']);
        }

        if($this->isPost()){
            $code = trim($_POST['code']);
            $password = trim($_POST['password']);
            $password2 = trim($_POST['password_repeat']);
            if($password2 !=$password){
                //todo  用正则判断密码
                Message::showError('两次密码不一致');
            }

            // 设置code 为不可用
            $userModelDB = new UserModelDB();
            $row  = $passwordRequestModelDB->getByCode($code);
            $passwordRequestModelDB->setCodeInvalid($code);
            if(empty($row)){
                Message::showError('code已使用过 ');
            }
            $succ = $userModelDB->updatePassword($row['user_id'], sha1($password));
            if($succ){
                Message::showSucc('密码修改成功, 跳转到登陆, 以便加强对密码的记忆...');
            }else{
            
                Message::showError('失败');
            }
        
        }
        $this->setView('allowResetPassword',  $allowResetPassword);
        $this->display('reset_password.html');
    }

    public function resetPasswordManually(){
        if($this->user['role']=='member'){
            if($this->isPost()){
                $old_password = trim($_POST['old_password']);
                $uid = $this->user['uid'];
                $userModelDB = new UserModelDB();
                $userInfo = $userModelDB->getUserByUid($uid); 
                if(empty($old_password)) {
                    Message::showError('您输入的旧密码不能为空');
                }
                if(!preg_match('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/', $old_password)){
                    $this->putErrorMsg('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/');
                    Message::showError('你输入的密码不符合正则');
                }
                if(sha1($old_password) != $userInfo['password']) {
                    Message::showError('您的旧密码输入有误');
                }
                $password = trim($_POST['password']);
                $password_repeat = trim($_POST['password_repeat']);
                if(empty($password) || empty($password_repeat)) {
                    Message::showError('新输入的密码不能为空');
                }
                if(!preg_match('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/', $password)){
                    $this->putErrorMsg('/[0-9a-zA-Z_\.\@\#\$\%]{6,18}/');
                    Message::showError('你输入的密码不符合正则');
                }
                if($password_repeat != $password) {
                    Message::showError('两次输入的密码不一致');
                }
                //save db
                $success = $userModelDB->updatePassword($uid, sha1($password_repeat));
                if(!$success) {
                    Message::showError('failed');
                }
                Message::showSucc('Modify password success!');
            }
        }else{
            Message::showError('请登陆');
        }

        $this->display('reset_password_manually.html');
    }

    public function image() {
        header('Content-type: image/jpeg');
        $image = new Image("http://www.imagemagick.org/image/wizard.jpg");
        //$image->rotate(45);
        $image->addWaterMark($waterText='missy-blue', $size=50, $opacity=0.4, $rotateDegree=-45);
        //$image->write('rotate.jpg');
        echo $image->getContent();
    }
}
