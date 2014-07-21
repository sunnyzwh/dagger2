<?php
/**
 * All rights reserved.
 * {%ModuleName%}
 * @author            **
 * @package            /
 * @version            $Id: $2011-2-3
 */

class {%ModuleFileName%}Controller extends DefaultController {

    /* 
     * 普通列表展示
     * 对应参数生成SQL，如url参数为：name=test，生成对应SQL为：`name` = 'test'（name必须为表字段）
     */
    public function view() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        //普通条件，检查GET参数
        $fieldArr = ${%ModuleVarName%}DB->getFields();
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
        $orderStr = '`{%firstPrimaryField%}` DESC';
        if (!empty($_POST['order']))
        {
            foreach($_POST['order'] as $k=>$v){
                $orderArr[] = "`{$k}` {$v}";
            }
            $orderStr = implode(',', $orderArr);
        }
        //查询操作
        $sql = "SELECT * FROM `".${%ModuleVarName%}DB->getTableName()."` WHERE {$whereStr} ORDER BY {$orderStr}";
        $data = ${%ModuleVarName%}DB->getData($sql, $whereArr, {%PageNum%});
        
        $pageStr = ${%ModuleVarName%}DB->getPageStr();
        {%fkRead%}
        //模版显示
        $this->setView('pageStr', $pageStr);
        $this->setView('data', $data);
        $this->display('{%ModuleFileName%}.html');
    }
    
    /* 
     * 指定条件，获取一条数据
     * 主要用于数据编辑时form表单中的数据获取
     */
    public function get() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $fieldArr = ${%ModuleVarName%}DB->getFields();
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
        $sql = "SELECT * FROM `".${%ModuleVarName%}DB->getTableName()."` WHERE {$whereStr}";
        $row = ${%ModuleVarName%}DB->getRow($sql, $whereArr);
        {%showGet%}
        $row ? Message::showSucc('获取单条{%ModuleName%}数据', $row) : Message::showError('没有获取到指定数据，可能已被删除', $row);
    }
    
    /* 
     * 数据插入
     * 根据form表单的name自动匹配数据表字段进行插入
     */
    public function create() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $fieldArr = ${%ModuleVarName%}DB->getFields();
        $insertArr = array();
        foreach ($fieldArr as $v) {
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $insertArr[$v] = $_POST[$v];
            }
        }
        {%insertSpecailDone%}
        $rs = ${%ModuleVarName%}DB->insert($insertArr);
        Log::write($this->adminUserName, $this->ip, {%createPk%}, self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('添加成功') : Message::showError('添加失败：' . implode(" ", ${%ModuleVarName%}DB->getErrorInfo()));
    }

    /* 
     * 数据更新
     * 根据form表单的name自动匹配数据表字段进行更新，以主键为条件
     */
    public function update() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $updateArr = $whereArr = array();
        {%PrimaryWhere%}
        
        $sql = "SELECT * FROM `".${%ModuleVarName%}DB->getTableName()."` WHERE {%primaryWhereStr%}";
        $row = ${%ModuleVarName%}DB->getRow($sql, array_values($whereArr));
        
        $fieldArr = ${%ModuleVarName%}DB->getFields();
        foreach ($fieldArr as $v) {
            {%SkipPrimay%}
            if (array_key_exists($v, $_POST) && $_POST[$v] !== null) {
                $updateArr[$v] = $_POST[$v];
            }
        }
        {%updateSpecailDone%}
        $changeNewRow = array_diff_assoc($updateArr, $row);//获取被修改字段数据
        if (empty($changeNewRow)) {
            Message::showSucc('提交成功，数据无修改');
        }
        $rs = ${%ModuleVarName%}DB->update($changeNewRow, $whereArr);
        //获取被修改数据写入日志
        $changeOldRow = array_diff_assoc(array_diff_assoc($row, $updateArr),array_diff_key($row, $updateArr));
        Log::write($this->adminUserName, $this->ip, {%primaryStr%}, self::$controller . "__" . self::$action, $rs, Log::arrayToLog($changeOldRow));
        //列表无刷新显示外键数据获取
        {%showUpdate%}
        //更新提示
        $rs ? Message::showSucc('更新成功', $changeNewRow) : Message::showError('更新失败：' . implode(" ", ${%ModuleVarName%}DB->getErrorInfo()));
    }

    /* 
     * 数据删除
     * 以主键为条件
     */
    public function delete() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $whereArr = array();
        {%PrimaryWhere%}
        
        $sql = "SELECT * FROM `".${%ModuleVarName%}DB->getTableName()."` WHERE {%primaryWhereStr%}";
        $row = ${%ModuleVarName%}DB->getRow($sql, array_values($whereArr));
        
        $rs = ${%ModuleVarName%}DB->delete($whereArr);
        Log::write($this->adminUserName, $this->ip, {%primaryStr%}, self::$controller . "__" . self::$action, $rs, Log::arrayToLog($row));
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", ${%ModuleVarName%}DB->getErrorInfo()));
    }

    /* 
     * 数据逻辑删除
     * 以主键为条件，字段内必须有逻辑删除标识字段“delete”，0为正常，1为删除。
     */
    public function logicDelete() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $updateArr = array('{%logicDelete%}' => 1);
        $whereArr = array();
        {%PrimaryWhere%}
        
        $rs = ${%ModuleVarName%}DB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, {%primaryStr%}, self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('删除成功') : Message::showError('删除失败：' . implode(" ", ${%ModuleVarName%}DB->getErrorInfo()));
    }

    public function logicResume() {
        ${%ModuleVarName%}DB = new {%ModuleFileName%}ModelDB({%Database%});
        $updateArr = array('{%logicDelete%}' => 0);
        $whereArr = array();
        {%PrimaryWhere%}
        
        $rs = ${%ModuleVarName%}DB->update($updateArr, $whereArr);
        Log::write($this->adminUserName, $this->ip, {%primaryStr%}, self::$controller . "__" . self::$action, $rs);
        $rs ? Message::showSucc('恢复成功') : Message::showError('恢复失败：' . implode(" ", ${%ModuleVarName%}DB->getErrorInfo()));
    }
}
?>
