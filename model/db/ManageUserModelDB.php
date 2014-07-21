<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 管理用户表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class ManageUserModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'email' => 
      array (
        'name' => 'EMAIL',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 1,
      ),
      'password' => 
      array (
        'name' => 'PASSWORD',
        'type' => 'varchar',
        'max_length' => '100',
        'validate' => 1,
      ),
      'is_del' => 
      array (
        'name' => 'IS_DEL',
        'type' => 'int',
        'max_length' => '3',
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
        //field_arr end 80ddb5784902e611ecef606e6e3f5e061
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("user");
    }
}
?>