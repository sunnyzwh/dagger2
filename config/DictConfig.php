<?php
/**
* 对应表配置信息
*/
class DictConfig {
    //管理员级别配置
    static public $adminLevelArr = array(1=>'一级管理员',1000=>'二级管理员',2000=>'三级管理员',3000=>'四级管理员');

    static public $sexArr = array(1=>'男',2=>'女');

    private function __construct() {
        return;
    }
}
