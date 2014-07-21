<?php
require("./common.php");
if($_GET['action'] == 'select') {
    if ($_GET['type'] == 'db') {
        $rs = file_get_contents(DAGGER_PATH_ROOT . 'config/DBConfig.php');
        //preg_match_all("/DB_(.*?)_PRODUCT_MASTER_HOST/", $rs, $match);
        $match = array_keys(DBConfig::$config['mysql']);
        $fileList = @scandir(DAGGER_PATH_ROOT . 'model/db');
        if (empty($match)) {
            die("DBConfig中还为配置数据库");
        }
        if (empty($fileList)) {
            die("需要首先生成外键DB类");
        }
        //$match = array_map('strtolower', array_unique($match[1]));
        $html = " 数据库：<select name='fk_type_db_database[{$_GET['k']}]'>";
        foreach ($match as $v) {
            $html .= "<option value='{$v}'>{$v}</option>";
        }
        $html .= "</select>";
        $html .= " 表：<select class='fk_type_db_table' key='{$_GET['k']}' name='fk_type_db_table[{$_GET['k']}]'><option value=''>请选择</option>";
        foreach ($fileList as $v) {
            if (substr($v, 0, 1) == '.') {continue;}
            $v = str_replace(".php", "", $v);
            $html .= "<option value='{$v}'>{$v}</option>";
        }
        $html .= "</select>";
    } elseif ($_GET['type'] == 'file') {
        $fileList = scandir(DAGGER_PATH_ROOT . 'config');
        $html = " 配置文件：<select class='fk_type_file' key='{$_GET['k']}' name='fk_type_file[{$_GET['k']}]'><option value=''>请选择</option>";
        foreach ($fileList as $v) {
            if (substr($v, 0, 1) == '.' || in_array($v, array('Configure.php','DBConfig.php','RouterConfig.php','SSOConfig.php','EncryptConfig.php','SysInitConfig.php'))) {continue;}
            $value = str_replace(".php", "", $v);
            $html .= "<option value='{$value}'>{$v}</option>";
        }
        $html .= "</select>";
    } else {
    }
} elseif ($_GET['action'] == 'table') {
    $db = new $_GET['table'];
    $arr = $db->getFields();
    $html = " key：<select name='fk_type_db_table_key[{$_GET['k']}]'>";
    foreach ($arr as $v) {
        $html .= "<option value='{$v}'>{$v}</option>";
    }
    $html .= "</select>";
    $html .= " value：<select name='fk_type_db_table_value[{$_GET['k']}]'>";
    foreach ($arr as $v) {
        $html .= "<option value='{$v}'>{$v}</option>";
    }
    $html .= "</select>";
} elseif ($_GET['action'] == 'file') {
    $file = $_GET['file'];
    $arr = array();
    $tmpArr = get_class_vars($file);
    if (empty($tmpArr)) {
        $html = '暂无可用数组';
    } else {
        foreach ($tmpArr as $k => $v)
        {
            $arr[] = $k;
        }
        $html = " 使用数组：<select name='fk_type_file_arr[{$_GET['k']}]'>";
        foreach ($arr as $v) {
            $html .= "<option value='{$v}'>{$v}</option>";
        }
        $html .= "</select>";
    }
}
echo $html;
?>
