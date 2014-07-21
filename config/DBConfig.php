<?php
/**
    * All rights reserved.
    * 数据库，MC配置
    * @author          Xin Wang <wx012@126.com>
    * @package         /config
    * @version         Id:
  */

/*
//SAE环境变量
SAE_MYSQL_HOST_M);
SAE_MYSQL_PORT);
SAE_MYSQL_USER);
SAE_MYSQL_PASS);
SAE_MYSQL_DB);

SAE_MYSQL_HOST_S);
SAE_MYSQL_PORT);
SAE_MYSQL_USER);
SAE_MYSQL_PASS);
SAE_MYSQL_DB);
*/
$config = array(
    'mysql' => array(
        //default库
        'default' => array(
            //线上库
            'product' => array(
                'master' => array(
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'user' => 'root',
                    'pass' => '',
                    'database' => 'cinderella',
                    'charset' => 'utf8',
                ),
                'slave' => array(
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'user' => 'root',
                    'pass' => '',
                    'database' => 'cinderella',
                    'charset' => 'utf8',
                )
            ),
            //测试库
            'test' => array(
            ),
            //开发库
            'dev' => array(
            )
        ),

        //user库
        //'user' => array(
        //  'product'=> array(
        //      'master' => array(
        //          ******
        //      ),
        //      'slave' => array(
        //          ******
        //      )
        //  )
        //)
    ),

    'memcache' => array(
        'default' => array(
            'servers' => '127.0.0.1:11211',//可为多个MC服务器，用空格隔开
        ),

        //user MC
        //'user' => array(
        //    'server' => '127.0.0.1:11211 127.0.0.1:11212',
        //)
    )
);

class DBConfig {

    static public $config = array();

    private function __construct() {
        return;
    }

    public static function set($config) {
        self::$config = $config;
    }
}
DBConfig::set($config);
