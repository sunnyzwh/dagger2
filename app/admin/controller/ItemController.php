<?php
/**
 * All rights reserved.
 * 商品
 * @author            **
 * @package            /
 * @version            $Id: $2011-2-3
 */

class ItemController extends DefaultController {

    /* 
     * 普通列表展示
     * 对应参数生成SQL，如url参数为：name=test，生成对应SQL为：`name` = 'test'（name必须为表字段）
     */
    public function view() {
        $itemDB = new ItemModelDB();
        //普通条件，检查GET参数
        $fieldArr = $itemDB->getFields();
        $where = $whereArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_GET) && $_GET[$v] !== null) {
                $where[] = "`{$v}` = ?";
                $whereArr[] = $_GET[$v];
            }
        }
        $whereStr = count($where) > 0 ? implode(" AND ", $where) : '1=1';
        if (array_key_exists('_search_field', $_GET) && $_GET['_search_field'] != '' && in_array($_GET['_search_field'], $fieldArr, true) && array_key_exists('_search_keyword', $_GET) && $_GET['_search_keyword'] != '') {
            $whereStr = $whereStr == '1=1' ? $_GET['_search_field'] . " LIKE ?" : $whereStr . " AND " . $_GET['_search_field'] . " LIKE ?";
            $whereArr[] = "%" . $_GET['_search_keyword'] . "%";
        }
        //排序条件
        $orderStr = '`id` DESC';
        if (!empty($_POST['order']))
        {
            foreach($_POST['order'] as $k=>$v){
                $orderArr[] = "`{$k}` {$v}";
            }
            $orderStr = implode(',', $orderArr);
        }
        //查询操作
        $sql = "SELECT * FROM `".$itemDB->getTableName()."` WHERE {$whereStr} ORDER BY {$orderStr}";
        $data = $itemDB->getData($sql, $whereArr, 20);
        
        $pageStr = $itemDB->getPageStr();
        //读取外键数据
        
        //模版显示
        $this->setView('pageStr', $pageStr);
        $this->setView('data', $data);
        $this->display('Item.html');
    }
    
    /* 
     * 指定条件，获取一条数据
     * 主要用于数据编辑时form表单中的数据获取
     */
    public function get() {
        $itemDB = new ItemModelDB();
        $fieldArr = $itemDB->getFields();
        $where = $whereArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_GET) && $_GET[$v] !== null) {
                $where[] = "`{$v}` = ?";
                $whereArr[] = $_GET[$v];
            }
        }
        if (count($where) == 0) {
            Message::showError('没有条件');
        }
        $whereStr = implode(" AND ", $where);
        $sql = "SELECT * FROM `".$itemDB->getTableName()."` WHERE {$whereStr}";
        $row = $itemDB->getRow($sql, $whereArr);
        
        $row ? Message::showSucc('获取单条商品数据', $row) : Message::showError('没有获取到指定数据，可能已被删除', $row);
    }
    
    /* 
     * 数据插入
     * 根据form表单的name自动匹配数据表字段进行插入
     */
    public function create() {
        $itemDB = new ItemModelDB();
        $fieldArr = $itemDB->getFields();
        $insertArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $insertArr[$v] = $_POST[$v];
            }
        }
        
        $rs = $itemDB->insert($insertArr);
        Log::write($this->adminUserName, $this->ip, $itemDB->insertId(), self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('添加成功') : Message::showError('添加失败：' . implode(" ", $itemDB->getErrorInfo()));
    }

    /* 
     * 数据更新
     * 根据form表单的name自动匹配数据表字段进行更新，以主键为条件
     */
    public function update() {
        Common::debug($_FILES);
        $itemDB = new ItemModelDB();
        $updateArr = $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$itemDB->getTableName()."` WHERE `id` = ?";
        $row = $itemDB->getRow($sql, array_values($whereArr));
        
        $fieldArr = $itemDB->getFields();
        foreach ($fieldArr as $v) {
            if (in_array($v, array('id'))) {continue;}//跳过主键
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $updateArr[$v] = $_POST[$v];
            }
        }
        
        $changeNewRow = array_diff_assoc($updateArr, $row);//获取被修改字段数据


        //upload image
        if(!empty($_POST['size'])){
            Common::debug('enter saving img');
            foreach($_POST['size'] as $key => $size){
                $fileName = $_POST['sn'].'_'.$_POST['size_id'][$key].'_'.$_POST['color_id']; //todo  根据文件后缀修改。。。。
                Common::debug('filename:'.$fileName);
                $fileName = $this->uploadImageFile($key, $fileName);
                //save   $fileName
            }
            $msg = '图片上传成功&';
        }


        if (empty($changeNewRow)) {
            Message::showSucc('提交成功，数据无修改');
        }
        $rs = $itemDB->update($changeNewRow, $whereArr);
        //获取被修改数据写入日志
        $changeOldRow = array_diff_assoc(array_diff_assoc($row, $updateArr),array_diff_key($row, $updateArr));
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($changeOldRow));
        //列表无刷新显示外键数据获取

        //更新提示
        $rs ? Message::showSucc($msg.'更新成功', $changeNewRow) : Message::showError('更新失败：' . implode(" ", $itemDB->getErrorInfo()));
    }

    /* 
     * 数据删除
     * 以主键为条件
     */
    public function delete() {
        $itemDB = new ItemModelDB();
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $sql = "SELECT * FROM `".$itemDB->getTableName()."` WHERE `id` = ?";
        $row = $itemDB->getRow($sql, array_values($whereArr));
        
        $rs = $itemDB->delete($whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs, Log::arrayToLog($row));
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $itemDB->getErrorInfo()));
    }

    /* 
     * 数据逻辑删除
     * 以主键为条件，字段内必须有逻辑删除标识字段“delete”，0为正常，1为删除。
     */
    public function logicDelete() {
        $itemDB = new ItemModelDB();
        $updateArr = array('is_del' => 1);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $itemDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", $itemDB->getErrorInfo()));
    }

    public function logicResume() {
        $itemDB = new ItemModelDB();
        $updateArr = array('is_del' => 0);
        $whereArr = array();
        $whereArr['id'] = $_GET['id'];
        
        $rs = $itemDB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, $_GET['id'], self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('恢复成功') : Message::showError('恢复失败：' . implode(" ", $itemDB->getErrorInfo()));
    }


    /**
     * updload image files
     */

    private function uploadImageFile($key, $fileName) {
        if ((($_FILES['image']["type"][$key] == "image/jpeg")
                || ($_FILES["image"]["type"][$key] == "image/pjpeg"))
            && ($_FILES["image"]["size"][$key] < 2*1024*1024*1204))
        {
            if ( is_array($_FILES["image"]["error"]) && $_FILES["image"]["error"][$key] > 0)
            {
                Message::showError("Return Code: " . $_FILES["image"]["error"][$key]);
            }else if(!is_array($_FILES["image"]["error"]) && $_FILES["image"]["error"] > 0 ){
                Message::showError("Return Code: " . $_FILES["image"]["error"]);
            }
            else
            {
                $i = 0;
                while(1){
                    $i++;
                    Common::debug(DAGGER_PATH_ROOT."images/" . $fileName.'.jpg');
                    if (file_exists(DAGGER_PATH_ROOT."images/" . $fileName.'.jpg')) {
                        Common::debug(DAGGER_PATH_ROOT."images/" . $fileName.'.jpg' . " already exists. ");
                        $fileName =  $fileName.'_'.$i;
                    }
                    else {
                        $fileName .= '.jpg';
                        move_uploaded_file($_FILES["image"]["tmp_name"][$key], DAGGER_PATH_ROOT."images/" . $fileName);
                        break;
                    }

                }
            }
            return $fileName; 
        }
        else
        {
                Message::showError("Invalid file");
        }
    }

}
?>
