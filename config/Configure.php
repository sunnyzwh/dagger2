<?php
class Configure {

    static public $app;//当前选择DAGGER_APP

    private function __construct() {}
    private function __clone() {}
    private function __destruct() {
        self::$app = NULL;
    }

    //配置项目选择规则
    final public static function getDefaultApp() {
        if( strpos($_SERVER['HTTP_HOST'], 'admin') !== FALSE ) {
            self::$app = DAGGER_APP_ADMIN;
        } else {
            self::$app = DAGGER_APP_SITE;
        }
    }

}
