<?php
/**
 * All rights reserved.
 * 测试
 * @author            **
 * @package            /
 * @version            $Id: $2010-3-3
 */

class DefaultController extends BaseController {

    public function view() {
        $this->setView('hi', 'hello world!!!');
        $this->display('./hi.html');
    }

    public function page() {
        header("Content-type: text/html; charset=utf-8");
        $page = new Page(100, 10);//参数1：总条数，参数2：每页显示条数
        $page->setStyle(1);
        echo $pageStr = $page->getPageStr();
    }

    public function message() {
        if (empty($_REQUEST['format'])) {
            $_REQUEST['format'] = 'json';
        }
        $arr = array(array('name'=>'张三', 'age'=>18), array('name'=>'李四', 'age'=>22));
        Message::showSucc('获取数据成功', $arr);
    }

    public function image() {
        $image = new Image('http://sinastorage.com/sandbox/test.jpg');
        $image->rotate(50);
        $s3 = new S3();
        $result = $s3->plainWrite('test.jpg', $image->getContent(), $image->getSize(), $image->getMimeType());
        Common::debug($result);
    }

    public function http() {
        Http::get('www.baidu.com');
        Http::post('www.baidu.com', array('id'=>1));
        Http::head('www.baidu.com');
    }

    public function db() {
        $db = new BaseModelDB();
        $rs = $db->getData("SELECT * FROM `test`", 10);

        //$db->setTableName('test');
        //$db->update(array('name'=>'示例'), array('id'=>2));
        // $rs = $db->getRow("SELECT * FROM `test`");
        // $rs = $db->getFirst("SELECT * FROM `test`");
        header('Content-Type: text/html; charset=UTF-8');
        var_dump($rs);
    }

    public function mc() {
        $mc = new MyMemcache();
        $mc->get('abc');
        $mc->set('abc', '1');
        $mc->increment('abc', 3);
        $mc->decrement('abc');
    }

    public function pagelet() {
        $this->display('pagelet.html');
    }
}
