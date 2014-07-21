<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: Commentmodel类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class CommentModel {

    /**
     * add comment
     */
    public function add($uid, $content, $star, $order_id = 0, $item_id = 0, $parent_id = 0) {
        $commentModelDb = new CommentModelDB();
        $res = $commentModelDb->add($uid, $content, $star, $order_id, $item_id, $parent_id);
        if($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * modify comment
     */
    public function modify($uid, $comment_id, $star, $content) {
        $commentModelDb = new CommentModelDB();
        $res = $commentModelDb->modify($uid, $comment_id, $content, $star);
        if($res) {
            return true;
        } else {
            return false;
        }
    }
    
}   
?>
