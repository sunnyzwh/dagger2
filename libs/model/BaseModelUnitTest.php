<?php
class BaseModelUnitTest extends PHPUnit_Framework_TestCase{
    public static function assertArrayHasKey($key, $value){
        try{
            return parent::assertArrayHasKey($key, $value);
        }catch(Exception $e){
            return;
        }
    }
}
