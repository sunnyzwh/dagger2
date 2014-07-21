<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 用户表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class UserModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'is_del' => 
      array (
        'name' => 'is_del',
        'type' => 'int',
        'max_length' => '3',
        'validate' => 0,
      ),
      'email' => 
      array (
        'name' => 'EMAIL',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => '1_email',
      ),
      'password' => 
      array (
        'name' => 'PASSWORD',
        'type' => 'varchar',
        'max_length' => '100',
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
        //field_arr end 940d7b92e66bb21686be30f20725579f1
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("user");
    }

    /**
     * fetch db to get register user
     * @param $email
     * @param $password
     */
    public function getUser($email, $password) {
        //find user table
        $sql = "SELECT * FROM `" . $this->tableName . "` WHERE `email` = ? AND `password` = ?";
        $data = $this->getRow($sql, array($email, $password));
        if(!empty($data)) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * fetch db to get user info by uid
     * @param $uid
     */
    public function getUserByUid($uid) {
        $sql = "SELECT * FROM `" . $this->tableName . "` WHERE `id` = ?";
        $data = $this->getRow($sql, array($uid));
        if(!empty($data)) {
            return $data;
        } else {
            return false;
        }
    }
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM `" . $this->tableName . "` WHERE `email` = ?";
        $data = $this->getRow($sql, array($email));
        if(!empty($data)) {
            return $data;
        } else {
            return false;
        }
    }

    public function save($email, $password){
        $now =date('Y-m-d H:i:s');
        return $this->insert(array('email'=>$email, 'password'=>$password, 'is_del'=>0, 'ctime'=>$now, 'utime'=>$now)); 
    }
    public function updatePassword($uid, $password){
w        return $this->update(array( 'password'=>$password, 'utime'=>$now), array('id'=>$uid));
    
    }
}
?>
