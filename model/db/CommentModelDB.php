<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: 评论表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class CommentModelDB extends MyDB {
    
    //field_arr start
    protected $field_arr = array (
      'id' => 
      array (
        'name' => 'ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 0,
      ),
      'parent_id' => 
      array (
        'name' => 'PARENT_ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'order_id' => 
      array (
        'name' => 'ORDER_ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'item_id' => 
      array (
        'name' => 'ITEM_ID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'uid' => 
      array (
        'name' => 'UID',
        'type' => 'int',
        'max_length' => '11',
        'validate' => 1,
      ),
      'content' => 
      array (
        'name' => 'CONTENT',
        'type' => 'varchar',
        'max_length' => '500',
        'validate' => 1,
      ),
      'star' => 
      array (
        'name' => 'STAR',
        'type' => 'tinyint',
        'max_length' => '3',
        'validate' => 1,
      ),
      'ctime' => 
      array (
        'name' => 'CTIME',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 0,
      ),
      'utime' => 
      array (
        'name' => 'UTIME',
        'type' => NULL,
        'max_length' => NULL,
        'validate' => 0,
      ),
      'is_del' => 
      array (
        'name' => 'IS_DEL',
        'type' => 'tinyint',
        'max_length' => '3',
        'validate' => 0,
      ),
    );
        //field_arr end 396717971afbc8d3a19783aeac54027a1
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("comment");
    }

    /**
     * add comment
     *
     * @param $uid
     * @param $content
     * @param $star
     * @param int $order_id
     * @param int $item_id
     * @param int $parent_id
     * @return bool
     */
    public function add($uid, $content, $star, $order_id = 0, $item_id = 0, $parent_id = 0) {
        $now =date('Y-m-d H:i:s');
        $insertArr = array(
            'uid'=>$uid,
            'content'=>$content,
            'star'=>$star,
            'order_id'=>$order_id,
            'item_id'=>$item_id,
            'parent_id'=>$parent_id,
            'ctime'=>$now,
            'utime'=>$now,
            'is_del'=>0
        );
        return $this->insert($insertArr);
    }

    /**
     * modify comment
     * @param $comment_id
     * @param $uid
     * @param $content
     * @param $star
     * @return bool
     */
    public function modify($uid, $comment_id, $content, $star) {
        $updateArr = array(
            'content'=>$content,
            'star'=>$star,
            'utime'=>date('Y-m-d H:i:s'),
        );
        return $this->update($updateArr, array('id'=>$comment_id, 'uid'=>$uid));
    }

    public function getList($item_id) {
        $sql = "SELECT * FROM `" . $this->getTableName() . "` WHERE `item_id` = ? AND `parent_id` = 0";
        $data = $this->getData($sql, array($item_id), 10);
        foreach($data as &$row) {
            if(empty($row['parent_id'])) {
                $sql = "SELECT * FROM `" . $this->getTableName() . "` WHERE `parent_id` = ?";
                $row['leaf'] = $this->getData($sql, array($row['id']));
            }

        }
        //print_r($data);
        return $data;
    }
}
?>