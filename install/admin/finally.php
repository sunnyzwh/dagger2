<?PHP
require("./common.php");
if ($_POST['field']) {
    $js_str = '';
    foreach($_POST['field'] as $k => $v) {
        $js_str .= "$('input[name=\"name[{$v}]\"]').val('{$_POST['name'][$v]}');\n";
        $js_str .= "$('input[name=\"empty[{$v}]\"]').val(['{$_POST['empty'][$v]}']);\n";
        $js_str .= "$('select[name=\"check[{$v}]\"]').val('{$_POST['check'][$v]}');\n";
        $js_str .= "$('input[name=\"show_field[{$v}]\"]').val(['{$_POST['show_field'][$v]}']);\n";
        $js_str .= "$('input[name=\"add_field[{$v}]\"]').val(['{$_POST['add_field'][$v]}']);\n";
        $js_str .= "add_field($('input[name=\"add_field[{$v}]\"]'));\n";
        $js_str .= "$('select[name=\"input_type[{$v}]\"]').val('{$_POST['input_type'][$v]}');\n";
        $js_str .= "input_type($('select[name=\"input_type[{$v}]\"]'));\n";
        $js_str .= "$('select[name=\"fk_type[{$v}]\"]').val('{$_POST['fk_type'][$v]}');\n";
        $js_str .= "fk_type($('select[name=\"fk_type[{$v}]\"]'));\n";
        $js_str .= "$('select[name=\"fk_type_db_table[{$v}]\"]').val('{$_POST['fk_type_db_table'][$v]}');\n";
        $js_str .= "fk_type_db_table($('select[name=\"fk_type_db_table[{$v}]\"]'));\n";
        $js_str .= "$('select[name=\"fk_type_file[{$v}]\"]').val('{$_POST['fk_type_file'][$v]}');\n";
        $js_str .= "fk_type_file($('select[name=\"fk_type_file[{$v}]\"]'));\n";
        $js_str .= "$('select[name=\"fk_type_db_table_key[{$v}]\"]').val('{$_POST['fk_type_db_table_key'][$v]}');\n";
        $js_str .= "$('select[name=\"fk_type_db_table_value[{$v}]\"]').val('{$_POST['fk_type_db_table_value'][$v]}');\n";
        $js_str .= "$('select[name=\"fk_type_file_arr[{$v}]\"]').val('{$_POST['fk_type_file_arr'][$v]}');\n";
        $js_str .= "$('input[name=\"search_field[{$v}]\"]').val(['{$_POST['search_field'][$v]}']);\n";
        $js_str .= "$('input[name=\"order_field[{$v}]\"]').val(['{$_POST['order_field'][$v]}']);\n";
        $js_str .= "$('input[name=\"primary[{$v}]\"]').val(['{$_POST['primary'][$v]}']);\n";
    }
    file_put_contents("./setting/{$_SESSION['ModuleFileName']}.js", $js_str);
    //echo htmlspecialchars($js_str);
}
if (empty($_POST['primary'])) {
    die("必须选择主键");
};
//*********************************************************************************
$dir = $_SESSION['dir'];
if (empty($_SESSION['ModuleName']) || empty($dir)){
    die('SESSION已过期，请重新创建<a href="./index.php">返回</a>');
}
//DB类*****************************************************************************
if (is_file($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php")) {
    $out .= $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类已存在，只能对字段信息进行更新...<br>";
    $rs = file_get_contents($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php");
} else {
    $rs = file_get_contents("./module/TemplateModelDB.php");
    $rs = str_replace('{%ModuleName%}',$_SESSION['ModuleName'], $rs);
    $rs = str_replace('{%ModuleVarName%}',$_SESSION['ModuleVarName'], $rs);
    $rs = str_replace('{%ModuleFileName%}',$_SESSION['ModuleFileName'], $rs);
    $rs = str_replace('{%TableName%}',$_SESSION['TableName'], $rs);
}
$arr = explode("//field_arr start", $rs);
list($field_str, $ext) = explode("//field_arr end", $arr[1]);
$field_str = str_replace(array(" ", "\t", "\n", "\r"), "", $field_str);

$check_md5 = substr($ext, 1, 32);
if (md5($field_str) != $check_md5 && !empty($field_str)){
    $out .=  $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类字段配置已手动修改，自动生成配置写入不成功！<br>";
} else {
    $out .=  $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类字段配置信息已按设置更新！<br>";
    $field_arr = array();
    $primary_arr = array();
    foreach ($_POST['field'] as $k => $v) {
        preg_match("/([^\(]*)\((.*)\)/", $_POST['type_field'][$v], $match);
        $_POST['empty'][$v] = $_POST['empty'][$v] ? 0 : 1;
        $validate = $_POST['check'][$v] ? $_POST['empty'][$v] . '_' . $_POST['check'][$v] : $_POST['empty'][$v];
        $_SESSION[$_SESSION['TableName'] . "_" . $v] = $_POST['name'][$v];
        $field_arr[$v] = array(
            'name' => $_POST['name'][$v],
            'type' => $match[1],
            'max_length' => $match[2],
            'validate' => $validate
        );
        if (!empty($_POST['primary'][$v])) {
            $primary_arr[] = $v;
        }
    }

    $str = var_export($field_arr, true);
    $str = "\nprotected \$field_arr = " . $str . ";\n    ";
    $str = str_replace("\n", "\n    ", $str);

    $rs = substr_replace($rs, $str, strpos($rs, "//field_arr start") + strlen("//field_arr start"), strpos($rs, "//field_arr end") - strpos($rs, "//field_arr start") - strlen("//field_arr start"));
    $md5 = md5(str_replace(array(" ", "\t", "\n", "\r"), "", $str));
    $rs = substr_replace($rs, " " . $md5, strpos($rs, "//field_arr end") + strlen("//field_arr end"), 32);
    $out .= $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类写入成功，内容如下：<br>";
    $out .= "<textarea name='null' rows='5' cols='100'>".htmlspecialchars($rs)."</textarea><br>";
    file_put_contents($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php", $rs);
}

$fkSetArr = array();
//model类*****************************************************************************
if (is_file($dir . "/model/{$_SESSION['ModuleFileName']}Model.php")) {
    $out .= $dir . "/model/{$_SESSION['ModuleFileName']}Model.php model类已存在，生成文件写入失败！<a href='./unlink.php?file=./model/{$_SESSION['ModuleFileName']}Model.php' onClick='return confirm(\"确定删除本控制器？\")' target='_blank'>删除现有控制器</a><br>";
    $rs = file_get_contents($dir . "/model/{$_SESSION['ModuleFileName']}Model.php");
} else {
    $rs = file_get_contents("./module/TemplateModel.php");
    $pkFunction = "";
    foreach($_POST['field'] as $k => $v) {
        if (in_array($_POST['input_type'][$v], array('select','radio','checkbox','multi_select'))) {
            if ($_POST['fk_type'][$v] == 'db' && !empty($_POST['fk_type_db_database'][$v]) && !empty($_POST['fk_type_db_table'][$v]) && !empty($_POST['fk_type_db_table_key'][$v]) && !empty($_POST['fk_type_db_table_value'][$v])) {
                $useDB = $_POST['fk_type_db_database'][$v];
                $useModelDB = $_POST['fk_type_db_table'][$v];
                if ($fkSetArr[$useModelDB]) {
                    continue;
                }
                $fkSetArr[$useModelDB] = true;
                $useModel = str_replace("DB", "", $useModelDB);
                $useFk = str_replace("Model", "", $useModel);
                $useVarName = strtolower($useModelDB{0}) . substr($useModelDB, 1);
                $useVarFk = strtolower($useFk{0}) . substr($useFk, 1);
                $useDB = $useDB == DAGGER_DB_DEFAULT ? '' : "'{$useDB}'";
                $useKey = $_POST['fk_type_db_table_key'][$v];
                $useValue = $_POST['fk_type_db_table_value'][$v];
                $pkFunction .= "/**\n     * 获取外键数据来源\n     * @param string \$key 指定key获取\n     * @return array|string 获取到的数据或指定key的值\n     */\n    public static function get{$useFk}(\$key = '') {\n        \${$useVarFk}DB = new {$useModelDB}({$useDB});\n        if (empty(\$key)) {\n            \$sql = \"SELECT `{$useKey}`,`{$useValue}` FROM `\".\${$useVarFk}DB->getTableName().\"`\";\n            \${$useVarName}Arr = \${$useVarFk}DB->getData(\$sql);\n            \$data = Common::createArr(\${$useVarName}Arr, \$arrKey = '{$useKey}', \$arrValue = '{$useValue}');\n        } else {\n            \$sql = \"SELECT `{$useValue}` FROM `\".\${$useVarFk}DB->getTableName().\"` WHERE `{$useKey}` = ?\";\n            \$data = \${$useVarFk}DB->getFirst(\$sql, array(\$key));\n        }\n        return \$data;\n    }\n    ";
            }
        }
    }
    $rs = str_replace('{%ModuleFileName%}',$_SESSION['ModuleFileName'], $rs);
    $rs = str_replace('{%fkFunction%}', $pkFunction, $rs);
    $out .= $dir . "/model/{$_SESSION['ModuleFileName']}Model.php 生成控制器写入文件成功，内容如下：<br>";
    $out .= "<textarea name='null' rows='5' cols='100'>".htmlspecialchars($rs)."</textarea><br>";
    file_put_contents($dir . "/model/{$_SESSION['ModuleFileName']}Model.php", $rs);
}



$fkSetArr = array();
//管理页面controller***********************************************************************************************
if (is_file($dir . "/app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php")) {
    $out .= $dir . "/app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php 控制器已存在，生成文件写入失败！<a href='./unlink.php?file=./app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php' onClick='return confirm(\"确定删除本控制器？\")' target='_blank'>删除现有控制器</a><br>";
    $rs = file_get_contents($dir . "/app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php");
} else {
    $rs = file_get_contents("./module/TemplateController.php");
    $rs = str_replace('{%ModuleName%}',$_SESSION['ModuleName'], $rs);
    $rs = str_replace('{%ModuleVarName%}',$_SESSION['ModuleVarName'], $rs);
    $rs = str_replace('{%ModuleFileName%}',$_SESSION['ModuleFileName'], $rs);
    $rs = str_replace('{%TableName%}',$_SESSION['TableName'], $rs);
    $rs = str_replace('{%PageNum%}',$_SESSION['PageNum'], $rs);
    $db_mark = $_SESSION['db_mark'] != DAGGER_DB_DEFAULT ? "'" . $_SESSION['db_mark'] . "'" : "";
    $rs = str_replace('{%Database%}',$db_mark, $rs);
    $rs = str_replace('{%logicDelete%}',$_POST['logic_delete'], $rs);
    //更新条件
    $primaryWhereStr = '';//用于update/delete中查询数据
    $primaryWhere = '';
    $firstPrimaryField = '';//用于order排序
    $primaryStr = '';//用于日志主键
    foreach($primary_arr as $v) {
        $primaryWhereStr .= " AND `{$v}` = ?";
        $primaryWhere .= "        \$whereArr['$v'] = \$_GET['$v'];\n";
        $firstPrimaryField = empty($firstPrimaryField) ? $v : $firstPrimaryField;
        $primaryStr .= " . '__' . \$_GET['{$v}']";
    }
    $primaryWhere = trim ($primaryWhere, " \n");
    $primaryWhereStr = trim ($primaryWhereStr, " AND ");
    $primaryStr = trim ($primaryStr, " . '_");
    $rs = str_replace('{%primaryWhereStr%}',$primaryWhereStr, $rs);
    $rs = str_replace('{%PrimaryWhere%}',$primaryWhere, $rs);
    $rs = str_replace('{%firstPrimaryField%}',$firstPrimaryField, $rs);
    $rs = str_replace('{%primaryStr%}',$primaryStr, $rs);
    //更新跳过主键
    $skipPrimay = "if (in_array(\$v, array('".implode("','", $primary_arr)."'))) {continue;}//跳过主键";
    $rs = str_replace('{%SkipPrimay%}',$skipPrimay, $rs);

    $createPk = $_SESSION['has_auto_increment'] ? "\${$_SESSION['ModuleVarName']}DB->insertId()" : $primaryStr;
    $rs = str_replace('{%createPk%}',$createPk, $rs);
    //外键读取
    $fkRead = "//读取外键数据\n        ";
    $showUpdate = '';
    $showGet = '';
    $insertSpecailDone = '';
    $updateSpecailDone = '';
    foreach($_POST['field'] as $k => $v) {
        if (in_array($_POST['input_type'][$v], array('select','radio','checkbox','multi_select'))) {
            if($_POST['fk_type'][$v] == 'db' && !empty($_POST['fk_type_db_database'][$v]) && !empty($_POST['fk_type_db_table'][$v]) && !empty($_POST['fk_type_db_table_key'][$v]) && !empty($_POST['fk_type_db_table_value'][$v])) {
                $useDB = $_POST['fk_type_db_database'][$v];
                $useModelDB = $_POST['fk_type_db_table'][$v];
                $useModel = str_replace("DB", "", $useModelDB);
                $useFk = str_replace("Model", "", $useModel);
                $useVarName = strtolower($useModelDB{0}) . substr($useModelDB, 1);
                $useVarFk = strtolower($useFk{0}) . substr($useFk, 1);
                $useKey = $_POST['fk_type_db_table_key'][$v];
                $useValue = $_POST['fk_type_db_table_value'][$v];
                if (empty($fkSetArr[$useModelDB])) {
                    $fkRead .= "\${$useVarFk}FkArr = {$_SESSION['ModuleFileName']}Model::get{$useFk}();\n        \$this->setView('{$useVarFk}FkArr',\${$useVarFk}FkArr);\n        ";
                    $fkSetArr[$useModelDB] = true;
                }
                if ($_POST['input_type'][$v] == 'checkbox' || $_POST['input_type'][$v] == 'multi_select') {
                    $insertSpecailDone .= "isset(\$insertArr['{$v}']) && \$insertArr['{$v}'] = Common::checkboxStrEncode(\$_POST['{$v}']);\n        ";
                    $updateSpecailDone .= "isset(\$updateArr['{$v}']) && \$updateArr['{$v}'] = Common::checkboxStrEncode(\$_POST['{$v}']);\n        ";
                } else {
                    $showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = {$_SESSION['ModuleFileName']}Model::get{$useFk}(\$changeNewRow['{$v}']);\n        }\n        ";
                }
            } elseif ($_POST['fk_type'][$v] == 'file' && !empty($_POST['fk_type_file'][$v]) && !empty($_POST['fk_type_file_arr'][$v])) {
                $fileClassName = $_POST['fk_type_file'][$v];
                $arrName = $_POST['fk_type_file_arr'][$v];
                $fkName = $arrName . str_replace("Config", "", $fileClassName);
                if (empty($fkSetArr[$fkName])) {
                    $fkRead .= "\${$fkName}FkArr = {$fileClassName}::\${$arrName};\n        \$this->setView('{$fkName}FkArr',\${$fkName}FkArr);\n        ";
                    $fkSetArr[$fkName] = true;
                }
                if ($_POST['input_type'][$v] == 'checkbox' || $_POST['input_type'][$v] == 'multi_select') {
                    $insertSpecailDone .= "isset(\$insertArr['{$v}']) && \$insertArr['{$v}'] = Common::checkboxStrEncode(\$_POST['{$v}']);\n        ";
                    $updateSpecailDone .= "isset(\$updateArr['{$v}']) && \$updateArr['{$v}'] = Common::checkboxStrEncode(\$_POST['{$v}']);\n        ";
                } else {
                    $showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = {$fileClassName}::\${$arrName}[\$changeNewRow['{$v}']];\n        }\n        ";
                }
            }
        } elseif ($_POST['input_type'][$v] == 'add_time') {
            $insertSpecailDone .= "\$insertArr['{$v}'] = time();\n        ";
            $showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = date('Y-m-d H:i:s', \$changeNewRow['{$v}']);\n        }\n        ";
            $showGet .= "\$row['{$v}'] = date('Y-m-d H:i:s', \$row['{$v}']);\n        ";
        } elseif ($_POST['input_type'][$v] == 'modify_time') {
            $insertSpecailDone .= "\$insertArr['{$v}'] = time();\n        ";
            $updateSpecailDone .= "\$updateArr['{$v}'] = time();\n        ";
            $showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = date('Y-m-d H:i:s', \$changeNewRow['{$v}']);\n        }\n        ";
            $showGet .= "\$row['{$v}'] = date('Y-m-d H:i:s', \$row['{$v}']);\n        ";
        } elseif ($_POST['input_type'][$v] == 'time') {
            $insertSpecailDone .= "\$insertArr['{$v}'] = strtotime(\$_POST['{$v}']);\n        ";
            $updateSpecailDone .= "\$updateArr['{$v}'] = strtotime(\$_POST['{$v}']);\n        ";
            $showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = date('Y-m-d', \$changeNewRow['{$v}']);\n        }\n        ";
            $showGet .= "\$row['{$v}'] = date('Y-m-d', \$row['{$v}']);\n        ";
        } elseif ($_POST['input_type'][$v] == 'img') {
            if ($newS3 !== true) {
                $insertSpecailDone .= "//处理图片上传\n        \$s3 = new S3();\n        ";
                $updateSpecailDone .= "//处理图片上传\n        \$s3 = new S3();\n        ";
                $showUpdate .= "\$s3 = new S3();\n        ";
                $showGet .= "\$s3 = new S3();\n        ";
                $newS3 = true;
            }
            $insertSpecailDone .= "if (isset(\$_FILES['{$v}'])) {\n            \$fileName = 'dagger/'.PROJECT_ID.'/'.uniqid();\n            \$result = \$s3->write(\$fileName, \$_FILES['{$v}']['tmp_name'], \$_FILES['{$v}']['type']);\n            if (!\$result) {\n                Message::showError('图片上传失败');\n            }\n            \$insertArr['{$v}'] = \$fileName;\n        }\n        ";
            $updateSpecailDone .= "if (isset(\$_FILES['{$v}'])) {\n            \$fileName = 'dagger/'.PROJECT_ID.'/'.uniqid();\n            \$result = \$s3->write(\$fileName, \$_FILES['{$v}']['tmp_name'], \$_FILES['{$v}']['type']);\n            if (!\$result) {\n                Message::showError('图片上传失败');\n            }\n            \$updateArr['{$v}'] = \$fileName;\n        } else {\n            unset(\$updateArr['{$v}']);\n        }\n        ";
            //$showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = \"IMG:['\".\$s3->getFileUrl(\$changeNewRow['{$v}']).\"']\";\n        }\n        ";
            $showGet .= "\$row['{$v}'] = \"IMG:['\".\$s3->getFileUrl(\$row['{$v}']).\"']\";//拼凑管理后台能够渲染的格式\n        ";
        } elseif ($_POST['input_type'][$v] == 'file') {
            if ($newS3 !== true) {
                $insertSpecailDone .= "//处理文件上传\n        \$s3 = new S3();\n        ";
                $updateSpecailDone .= "//处理文件上传\n        \$s3 = new S3();\n        ";
                $showUpdate .= "\$s3 = new S3();\n        ";
                $showGet .= "\$s3 = new S3();\n        ";
                $newS3 = true;
            }
            $insertSpecailDone .= "if (isset(\$_FILES['{$v}'])) {\n            \$fileName = 'dagger/'.PROJECT_ID.'/'.uniqid();\n            \$result = \$s3->write(\$fileName, \$_FILES['{$v}']['tmp_name'], \$_FILES['{$v}']['type']);\n            if (!\$result) {\n                Message::showError('文件上传失败');\n            }\n            \$insertArr['{$v}'] = \$fileName;\n        }\n        ";
            $updateSpecailDone .= "if (isset(\$_FILES['{$v}'])) {\n            \$fileName = 'dagger/'.PROJECT_ID.'/'.uniqid();\n            \$result = \$s3->write(\$fileName, \$_FILES['{$v}']['tmp_name'], \$_FILES['{$v}']['type']);\n            if (!\$result) {\n                Message::showError('文件上传失败');\n            }\n            \$updateArr['{$v}'] = \$fileName;\n        } else {\n            unset(\$updateArr['{$v}']);\n        }\n        ";
            //$showUpdate .= "if(isset(\$changeNewRow['{$v}'])) {\n            \$changeNewRow['{$v}'] = \"FILE:['\".\$s3->getFileUrl(\$changeNewRow['{$v}']).\"']\";\n        }\n        ";
            $showGet .= "\$row['{$v}'] = \"FILE:['\".\$s3->getFileUrl(\$row['{$v}']).\"']\";//拼凑管理后台能够渲染的格式\n        ";
        }
    }
    $rs = str_replace('{%fkRead%}',$fkRead, $rs);
    $rs = str_replace('{%showUpdate%}',$showUpdate, $rs);
    $rs = str_replace('{%showGet%}',$showGet, $rs);
    $rs = str_replace('{%insertSpecailDone%}',$insertSpecailDone, $rs);
    $rs = str_replace('{%updateSpecailDone%}',$updateSpecailDone, $rs);

    $out .= $dir . "/app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php 生成控制器写入文件成功，内容如下：<br>";
    $out .= "<textarea name='null' rows='5' cols='100'>".htmlspecialchars($rs)."</textarea><br>";
    file_put_contents($dir . "/app/{$_SESSION['app']}/controller/{$_SESSION['ModuleFileName']}Controller.php", $rs);
}

//模版页面*********************************************************************************************************************
if (is_file($dir . "/app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html")) {
    $out .= $dir . "/app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html 模版已存在，生成文件写入失败！<a href='./unlink.php?file=./app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html' onClick='return confirm(\"确定删除本模板？\")' target='_blank'>删除现有模板</a><br>";
    $rs = file_get_contents($dir . "/app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html");
} else {
    $rs = file_get_contents("./module/Template.html");
    $rs = str_replace('{%ModuleName%}',$_SESSION['ModuleName'], $rs);
    $rs = str_replace('{%ModuleUrlName%}',$_SESSION['ModuleUrlName'], $rs);
    $rs = str_replace('{%ModuleVarName%}',$_SESSION['ModuleVarName'], $rs);
    $rs = str_replace('{%ModuleFileName%}',$_SESSION['ModuleFileName'], $rs);
    $rs = str_replace('{%TableName%}',$_SESSION['TableName'], $rs);
    $delete_a = empty($_POST['logic_delete']) ? 'delete' : "{=if \$item.{$_POST['logic_delete']} == 1=}logic_resume{=else=}logic_delete{=/if=}";
    $delete_word = empty($_POST['logic_delete']) ? '删除' : "{=if \$item.{$_POST['logic_delete']} == 1=}恢复{=else=}删除{=/if=}";
    $delete_status = empty($_POST['logic_delete']) ? "" : "{=if \$item.{$_POST['logic_delete']} == 1=} del{=/if=}";
    //主键操作url
    $primaryUrlArr = array();
    $primaryKey = '';
    foreach($primary_arr as $v) {
        $primaryUrlArr[] = "{$v}=>{=\$item.{$v}=}";
        $primaryKey .= "_{=\$item.$v=}";
    }
    $primaryUrl = '['.implode(',', $primaryUrlArr).']';
    //列表\FROM
    $theader = "<tr>\n";
    $tbody = '<tr class="{=cycle values=\',odd\'=}'.$delete_status.'" id="tr'.$primaryKey.'">';
    $tbody .= "\n";
    $form = '<div id="'.$_SESSION['ModuleUrlName'].'_update_dialog" style="display:none;" title="'.$_SESSION['ModuleName'].'编辑">';
    $form .= "\n";
    $form .= '<form class="ui-form" action="" method="post"><table align="center" width="100%"><tbody>';
    $form .= "\n";
    $search = '';
    foreach($_POST['field'] as $k => $v) {
        if (in_array($_POST['input_type'][$v], array('checkbox','select','radio','multi_select'))) {
            if ($_POST['fk_type'][$v] == 'db' && !empty($_POST['fk_type_db_database'][$v]) && !empty($_POST['fk_type_db_table'][$v]) && !empty($_POST['fk_type_db_table_key'][$v]) && !empty($_POST['fk_type_db_table_value'][$v])) {
                $useModelDB = $_POST['fk_type_db_table'][$v];
                $useFk = str_replace("ModelDB", "", $useModelDB);
                $useVarFk = strtolower($useFk{0}) . substr($useFk, 1);
            } elseif ($_POST['fk_type'][$v] == 'file' && !empty($_POST['fk_type_file'][$v]) && !empty($_POST['fk_type_file_arr'][$v])) {
                $fileClassName = $_POST['fk_type_file'][$v];
                $arrName = $_POST['fk_type_file_arr'][$v];
                $useVarFk = $arrName . str_replace("Config", "", $fileClassName);
            }
        }
        if ($_POST['show_field'][$v] == true) {
            if($_POST['order_field'][$v] == true) {
                $theader .= "                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='{$_POST['order_field'][$v]}' data-opt_id='sortable_tbody' data-url='http://{=\$smarty.server.HTTP_HOST=}{=\$smarty.server.REQUEST_URI=}'><div class='sortoptions'></div>{$_POST['name'][$v]}</th>\n";
            }else{
                $theader .= "                        <th>{$_POST['name'][$v]}</th>\n";
            }
            if (in_array($_POST['input_type'][$v], array('select','radio'))){
                $tbody .= "                        <td class=\"td_{$v}\">{=\${$useVarFk}FkArr[\$item.{$v}]=}</td>\n";
            } elseif (in_array($_POST['input_type'][$v], array('add_time','modify_time','time'))) {
                $tbody .= "                        <td class=\"td_{$v}\">{=\$item.{$v}|date_format:'%Y-%m-%d'=}</td>\n";
            } elseif (in_array($_POST['input_type'][$v], array('checkbox', 'multi-select'))) {
                $tbody .= "                        <td class=\"td_{$v}\">{=\$item.{$v}=}</td>\n";
            } elseif (in_array($_POST['input_type'][$v], array('img'))) {
                $tbody .= "                        <td class=\"td_{$v}\">{=\$item.{$v}=}</td>\n";
            } elseif (in_array($_POST['input_type'][$v], array('file'))) {
                $tbody .= "                        <td class=\"td_{$v}\">{=\$item.{$v}=}</td>\n";
            } else {
                $tbody .= "                        <td class=\"td_{$v}\">{=\$item.{$v}=}</td>\n";
            }
        }
        if ($_POST['add_field'][$v] == true) {
                $form .= '<tr class="field-group"><td class="field-header">'.$_POST['name'][$v].'：</td><td class="field-content">';
            if ($_POST['input_type'][$v] == 'textarea') {
                $form .= '<textarea name="'.$v.'" style="width:300px;height:100px;"></textarea>';
            } elseif ($_POST['input_type'][$v] == 'select' && in_array($_POST['fk_type'][$v], array('db','file'))) {
                $form .= '<select name="'.$v.'">{=html_options options=$'.$useVarFk.'FkArr=}</select>';
            } elseif ($_POST['input_type'][$v] == 'radio' && in_array($_POST['fk_type'][$v], array('db','file'))) {
                $form .= '<div class="ui-radioes ui-buttonset">{=html_radios name="'.$v.'" options=$'.$useVarFk.'FkArr label_ids=true=}</div>';
            } elseif ($_POST['input_type'][$v] == 'checkbox' && in_array($_POST['fk_type'][$v], array('db','file'))) {
                $form .= '<div class="ui-checkboxes ui-buttonset">{=html_checkboxes name="'.$v.'" options=$'.$useVarFk.'FkArr label_ids=true=}</div>';
            } elseif ($_POST['input_type'][$v] == 'time') {
                $form .= '<input type="input" class="ui-text-dialog" name="'.$v.'" id="input_calendar_'.$v.'"><img style="vertical-align: middle;margin-left: 2px;" src="images/calendar.gif" alt="..." title="..." id="show_calendar_'.$v.'" />';
                $form .= "\n<script>\n$(document).ready(function(){\n   \$('#input_calendar_{$v}').datepicker({\n        dateFormat: \"yy-mm-dd\"\n    });\n});\n</script>\n";
            } elseif ($_POST['input_type'][$v] == 'img') {
                $form .= '<input type="file" class="ui-text-dialog" name="'.$v.'"><div class="dagger_input_value_show" style="display:none;"><img src="" style="width:50px;" id="show_img_'.$v.'" /></div>';
            } elseif ($_POST['input_type'][$v] == 'file') {
                $form .= '<input type="file" class="ui-text-dialog" name="'.$v.'"><div class="dagger_input_value_show" style="display:none;"><a href="" id="show_file_'.$v.'" target="_blank">点击下载</a></div>';
            } else if($_POST['input_type'][$v] == 'richtext'){
                if(!isset($ueditor)){
                  $form .= '<script type="text/javascript"> window.UEDITOR_HOME_URL = "http://ueditor.baidu.com/ueditor/";</script>';
                  $form .= "\n";
                  $form .= '<script type="text/javascript" charset="utf-8" src="http://ueditor.baidu.com/ueditor/editor_config.js"></script>';
                  $form .= "\n";
                  $form .= '<script type="text/javascript" charset="utf-8" src="http://ueditor.baidu.com/ueditor/editor_all_min.js"></script>';
                  $form .= "\n";
                  $ueditor = true;
                }
                $form .= '<script type="text/plain" id="ueditor_' . $v . '" name="' . $v . '"></script>';
                $form .= "\n";
                $form .= '<script type="text/javascript"> var ue=UE.getEditor("ueditor_' . $v . '", {}); </script> ';
                $form .= "\n";
            } else if($_POST['input_type'][$v] == 'multi_select'){
                if(!isset($multiselect)){
                    $multiselect = array();
                }
                $multiselect[$v] = $v;
                $form .= '<div class="ajs-multi-select-placeholder textarea long-field"></div><select id="ms_'.$v.'" name="'.$v.'[]" class="hidden" multiple="multiple" size="5">{=html_options options=$'.$useVarFk.'FkArr=}</select>';
            } else {
                $form .= '<input type="text" class="ui-text-dialog" name="'.$v.'" value="" />';
            }
            $form .= "</td></tr>\n";
        }
        if ($_POST['search_field'][$v] == true) {
            $search .= '<option value="'.$v.'">'.$_POST['name'][$v].'</option>';
        }
    }
    $form .= '<tr class="field-group" style="border-bottom:0px;padding-bottom:0px;"><td class="field-header"></td><td class="field-content" style="text-align: center; display: block;"><input type="submit" class="ui-button-submit" id="submit" value="提交"><td></tr></tbody></table></form>';
    $form .= "\n</div>";
    if(isset($multiselect)){
        $form .= "\n";
        $form .= '<script type="text/javascript" src="/js/multiselect/ajs-gadgets.js"></script>';
        $form .= "\n";
        $form .= '<script type="text/javascript" src="/js/multiselect/AJS.js"></script>';
        $form .= "\n";
        $form .= '<script type="text/javascript">';
        $form .= "\n";
        $form .= 'var multiselectors = {};';
        $form .= "\n";
        $form .= '</script>';
        $form .= "\n";
        $form .= '<link rel="stylesheet" href="/css/admin/batch.css"/>';
        $form .= "\n";
        $form .= '<script type="text/javascript">'."\n";
        foreach($multiselect as $m=>$v){
            $form .= 'multiselectors.'.$v.' = new MultiSelect({element:$("#ms_'.$m.'"), itemAttrDisplayed:"label", minWidth:"308px"});'."\n";
        }
        $form .= '</script>';
    }
    $theader .= "                        <th>操作</th></tr>";
    $theader .= "\n";

	if (empty($_POST['logic_delete'])) {
		$tbody .= "                        " . '<td>[<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_update_dialog" data-opt_type="update" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'get\' params='.$primaryUrl.'=}" data-opt_action="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'update\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'编辑" title="'.$_SESSION['ModuleName'].'编辑" >编辑</a>] | [<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_delete" data-opt_type="delete" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'delete\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'删除" title="'.$_SESSION['ModuleName'].'删除" >删除</a>]</td></tr>';
	} else {
		$tbody .= "{=if \$item.{$_POST['logic_delete']} == 1=}";
		$tbody .= "                        " . '<td>[<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_update_dialog" data-opt_type="update" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'get\' params='.$primaryUrl.'=}" data-opt_action="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'update\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'编辑" title="'.$_SESSION['ModuleName'].'编辑" >编辑</a>] | [<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_logic_resume" data-opt_type="delete" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'logic_resume\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'恢复" title="'.$_SESSION['ModuleName'].'恢复" >恢复</a>]</td>';
		$tbody .= "{=else=}";
		$tbody .= "                        " . '<td>[<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_update_dialog" data-opt_type="update" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'get\' params='.$primaryUrl.'=}" data-opt_action="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'update\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'编辑" title="'.$_SESSION['ModuleName'].'编辑" >编辑</a>] | [<a href="javascript:void(0);" class="opt"  data-opt_id="'.$_SESSION['ModuleUrlName'].'_logic_delete" data-opt_type="delete" data-opt_url="{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'logic_delete\' params='.$primaryUrl.'=}" alt="'.$_SESSION['ModuleName'].'删除" title="'.$_SESSION['ModuleName'].'删除" >删除</a>]</td>';
		$tbody .= "{=/if=}";
		$tbody .= '</tr>';
	}
    $tbody .= "\n";

    $rs = str_replace('{%theader%}',$theader, $rs);
    $rs = str_replace('{%tbody%}',$tbody, $rs);
    $rs = str_replace('{%Form%}',$form, $rs);
    $rs = str_replace('{%Search%}',$search, $rs);
    $rs = str_replace('{%CreateAction%}', '{=createUrl controller=\''.$_SESSION['ModuleUrlName'].'\' action=\'create\'=}', $rs);

    $out .= $dir . "/app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html 生成模版写入成功，内容如下：<br>";
    $out .= "<textarea name='null' rows='5' cols='100'>".htmlspecialchars($rs)."</textarea><br>";
    file_put_contents($dir . "/app/{$_SESSION['app']}/templates/{$_SESSION['ModuleFileName']}.html", $rs);
}

//生成菜单*********************************************************************************************************************

if (is_file($dir . "/app/{$_SESSION['app']}/templates/left.html")) {
    $rs = file_get_contents($dir . "/app/{$_SESSION['app']}/templates/left.html");
    if (!strpos($rs, "{=createUrl controller='{$_SESSION['ModuleUrlName']}' action='view'=}")) {
        $rs = str_replace("{=*后台菜单*=}", "<h1 class=\"menu-itemHeader menu-extension\">{$_SESSION['ModuleName']}管理</h1>\n<div class=\"menu-itemCont\">\n    <ul>\n        <li><a href=\"{=createUrl controller='{$_SESSION['ModuleUrlName']}' action='view'=}\">{$_SESSION['ModuleName']}列表</a></li>\n    </ul>\n</div>\n{=*后台菜单*=}", $rs);
        file_put_contents($dir . "/app/{$_SESSION['app']}/templates/left.html", $rs);
        $out .= $dir . "/app/{$_SESSION['app']}/templates/left.html 生成左侧菜单成功。<br>";
    } else {
        $out .= $dir . "/app/{$_SESSION['app']}/templates/left.html 左侧菜单已存在，无改动。<br>";
    }
} else {
    $out .= $dir . "/app/{$_SESSION['app']}/templates/left.html 不存在，无法生成菜单。<br>";
}
$out .= '<div style="font-size:20px;padding-top:20px;">点击<a href="./../../index.php?c='.$_SESSION['ModuleUrlName'].'&a=view" target="_blank" style="color:#f00;">这里</a>访问该后台管理模块<span style="font-size:12px;color:#f00;">(请确保/config/Configure.php中的app选择器选择为后台DAGGER_APP_ADMIN)</span></div>';
?>
<html>
<head>
<title>模板构架</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit; border:1px dashed #FF9900;}
textarea {font-size:12px;}
</style>
</head>
<body>
<table align=center border=1>
<tr><th>生成结果</th></tr>
<tr><td><?php echo $out;?></td></tr>
</table>
</body>
</html>
