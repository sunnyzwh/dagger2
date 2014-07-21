<?php
class BaseModelSwitch {
    /**
     * POST提交需要检查referer
     */
    const SWITCH_POST_REFERER_CHECK = 'postRefererCheck';
    /**
     * 只有POST提交才能访问主库
     */
    const SWITCH_MASTERDB_POST_ONLY = 'masterDbPostOnly';

    protected static $Switches = array(
        self::SWITCH_POST_REFERER_CHECK => true, //post提交referer限制开关
        self::SWITCH_MASTERDB_POST_ONLY => true,//主库操作post限制开关
    );

    /**
     * 设置开关,默认打开
     * @param string $switch 开关名称
     * @param bool $value 开关值，默认为打开：true
     * @return void
     */
    public static function set($switch, $value = true) {
        if (key_exists($switch, self::$Switches)) {
            self::$Switches[$switch] = $value;
        } else {
            throw new BaseModelException("{$switch}开关不存在", 91000, 'switch_trace');
        }
    }

    /**
     * 打开开关
     * @param string $switch 开关名称
     * @return void
     */
    public static function open($switch) {
        if (key_exists($switch, self::$Switches)) {
            self::$Switches[$switch] = true;
        } else {
            throw new BaseModelException("{$switch}开关不存在", 91000, 'switch_trace');
        }
    }

    /**
     * 关闭开关
     * @param string $switch 开关名称
     * @return void
     */
    public static function close($switch) {
        if (key_exists($switch, self::$Switches)) {
            self::$Switches[$switch] = false;
        } else {
            throw new BaseModelException("{$switch}开关不存在", 91000, 'switch_trace');
        }
    }

    /**
     * 检查开关值
     * @return bool
     * @author 
     */
    public static function check($switch, $cmpare = true) {
        if (key_exists($switch, self::$Switches)) {
            return self::$Switches[$switch] === $cmpare;
        } else {
            throw new BaseModelException("{$switch}开关不存在", 91000, 'switch_trace');
        }
    }
}
