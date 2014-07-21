<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 商品表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class ItemModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'sn' => 
      array (
        'name' => '商品编号',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 1,
      ),
      'name' => 
      array (
        'name' => '商品名称',
        'type' => 'varchar',
        'max_length' => '200',
        'validate' => 1,
      ),
      'category_id' => 
      array (
        'name' => '栏目分类',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'size_id' => 
      array (
        'name' => '尺寸',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'color_id' => 
      array (
        'name' => '颜色',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'price' => 
      array (
        'name' => '价格',
        'type' => 'float',
        'max_length' => '11,6',
        'validate' => 1,
      ),
      'desc' => 
      array (
        'name' => '描述',
        'type' => 'varchar',
        'max_length' => '2000',
        'validate' => 1,
      ),
      'num' => 
      array (
        'name' => '数量',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'status' => 
      array (
        'name' => '状态',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'is_del' => 
      array (
        'name' => '删除',
        'type' => 'tinyint',
        'max_length' => '3',
        'validate' => 0,
      ),
      'ctime' => 
      array (
        'name' => '创建时间',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 0,
      ),
      'utime' => 
      array (
        'name' => '更新时间',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 0,
      ),
    );
        //field_arr end b07cae8d578e6ce2e2c90367ad23110a1
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("item");
    }
}
?>