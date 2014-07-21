<?php

class CommentController extends MyController {

    /**
     * construct function
     */
    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    /**
     * add comment
     */
    public function add() {
        if($this->isPost()) {
            $uid = $this->user['uid'];
            if(empty($uid)) {
                Message::showError('please login');
            }
            $content = trim($_POST['content']);
            if(empty($content)) {
                Message::showError('content is empty');
            }
            if(!is_numeric($_POST['order_id']) || !is_numeric($_POST['item_id']) || !is_numeric($_POST['star']) || !is_numeric($_POST['parent_id'])) {
                Message::showError('parameters is not number');
            }
            $order_id = trim($_POST['order_id']);
            $item_id = trim($_POST['item_id']);
            $star = trim($_POST['star']);
            $parent_id = trim($_POST['parent_id']);

            $commentModel = new CommentModel();
            $result = $commentModel->add($uid, $content, $star, $order_id, $item_id, $parent_id);
            if(!empty($result)) {
                Message::showSucc('add comment success');
            } else {
                Message::showError('add comment failed');
            }
        }
        $this->display('comment.html');

    }

    /**
     * modify comment
     */
    public function modify() {
        if($this->isPost()) {
            $uid = $this->user['uid'];
            if(empty($uid)) {
                Message::showError('please login');
            }
            $comment_id = trim($_POST['id']);
            if(!is_numeric($comment_id)) {
                Message::showError('comment id necessay');
            }
            $content = trim($_POST['content']);
            if(empty($content)) {
                Message::showError('content is empty');
            }
            if(!is_numeric($_POST['star'])) {
                Message::showError('star is not number');
            }
            $star = trim($_POST['star']);

            $commentModel = new CommentModel();
            $result = $commentModel->modify($uid, $comment_id, $star, $content);
            if(!empty($result)) {
                Message::showSucc('modify comment success');
            } else {
                Message::showError('modify comment failed');
            }
        }
        $this->display('comment.html');

    }

    /**
     * get list comment
     * http://www.missy-blue.com/comment/get_list/?format=json
     */
    public function getList() {
        $commentModelDb = new CommentModelDB();
        $result = $commentModelDb->getList(5);
        if(!empty($result)) {
            Message::showSucc('add comment success', $result);
        } else {
            Message::showError('add comment failed');
        }
    }
}