<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 密码请求表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class PasswordRequestModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'is_del' => 
      array (
        'name' => 'is_del',
        'type' => 'int',
        'max_length' => '3',
        'validate' => 0,
      ),
      'user_id' => 
      array (
        'name' => 'USER_ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'code' => 
      array (
        'name' => 'CODE',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 1,
      ),
      'expiration' => 
      array (
        'name' => 'EXPIRATION',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 1,
      ),
      'status' => 
      array (
        'name' => 'STATUS',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'ctime' => 
      array (
        'name' => 'CTIME',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 1,
      ),
      'utime' => 
      array (
        'name' => 'UTIME',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 1,
      ),
    );
        //field_arr end 80623a7a7e9cf6d7c06aedec44e462121
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("password_request");
    }


    public function save($uid, $code, $expiration){
        $now =date('Y-m-d H:i:s');
        return $this->insert(array('user_id'=>$uid, 'code'=>$code, 'expiration'=>$expiration, 'is_del'=>0, 'status'=>0, 'ctime'=> $now, 'utime'=>$now));
    }

    public function getByCode($code){
        $sql = 'SELECT * from '.$this->getTableName().' where `code` = ? and `status` = 0';
        return  $this->getRow($sql, array($code));
    
    }

    public function setCodeInvalid($code){
        $now =date('Y-m-d H:i:s');
        return $this->update(array('status'=>1, 'utime'=>$now), array('code'=>$code) );
    }
}
?>
