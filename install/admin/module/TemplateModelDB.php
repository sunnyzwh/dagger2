<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @description: {%ModuleName%}表DB类
 * @author: **
 * @date: 2010/07/15
 * @version: 1.0
 */
class {%ModuleFileName%}ModelDB extends MyDB {
    
    //field_arr start
    //field_arr end 11111111111111111111111111111111
    
    public function __construct($dbname = null, array $db_config = array()) {
        parent::__construct($dbname, $db_config);
        parent::setTableName("{%TableName%}");
    }
}
?>