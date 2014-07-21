<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 用户地址表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class AddressModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 0,
      ),
      'user_id' => 
      array (
        'name' => 'USER_ID',
        'type' => 'int',
        'max_length' => '10',
        'validate' => 1,
      ),
      'country' => 
      array (
        'name' => 'COUNTRY',
        'type' => 'varchar',
        'max_length' => '40',
        'validate' => 1,
      ),
      'province' => 
      array (
        'name' => 'PROVINCE',
        'type' => 'varchar',
        'max_length' => '40',
        'validate' => 1,
      ),
      'city' => 
      array (
        'name' => 'CITY',
        'type' => 'varchar',
        'max_length' => '40',
        'validate' => 1,
      ),
      'district' => 
      array (
        'name' => 'DISTRICT',
        'type' => 'varchar',
        'max_length' => '40',
        'validate' => 1,
      ),
      'street' => 
      array (
        'name' => 'STREET',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 1,
      ),
      'phone' => 
      array (
        'name' => 'PHONE',
        'type' => 'varchar',
        'max_length' => '40',
        'validate' => '1_mobile',
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
        //field_arr end b9c6ef2e6dd32050a0388627b0eaca081
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("address");
    }
}
?>