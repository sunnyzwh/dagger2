<?php
/**
 * All rights reserved.
 * 数据验证基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
 */
class BaseModelValidate {
    private function __construct() {
        return false;
    }

    /**
     * 数据验证
     * @param string $value 被验证的数据
     * @param string $type 数据类型 基本参照mysql的数据类型
     * @param string $validate 指明如何验证，示例 1_email 1指明要验证该值是否为空，email指明要验证该值是否是邮箱地址
     * @param string $length 验证数据是否超过了指定长度 0表示不验证长度
     * @return string|boolean|mixed 验证成功返回true值，否则返回失败原因
     * @example BaseModelValidate::check('1111-11-21 1:1:1', 'datetime', '1_datetime');
     * @author 
     */
    public static function check($value, $type, $validate, $length = 0) {
        //验证数据值
        $tmpValidataArr = explode('_', $validate);
        $notEmpty = $tmpValidataArr[0];
        $validate = array_key_exists(1, $tmpValidataArr) ? $tmpValidataArr[1] : '';
        if ($value === '') {
            if ($notEmpty) {
                return '不能为空';
            } else {
                return true;//该值可以为空,不需要检测
            }
        }
        if ($validate == ''){
            return true;//不需要检测该值类型
        }
        //验证数据类型
        switch ($type) {
            case 'int':
            case 'tinyint':
            case 'smallint':
            case 'bigint':
                if (self::isNumber($value) !== true) {
                    return '不是一个数字';
                } elseif ($length && strlen($value) > $length) {
                    return '数字超出了范围';
                }
                break;
            case 'bool':
                if (!in_array($value, array(true, false), true)) {
                    return '必须是一个bool型';
                }
                break;
            case 'float':
            case 'double':
                if (!is_numeric($value)) {
                    return '不是一个数字';
                }
                break;
            case 'char':
            case 'varchar':
            case 'text':
            case 'varchar':
                if ($length && strlen($value) > $length) {
                    return '字符串超出了范围';
                }
                break;
            case 'datetime':
                if (self::isDatetime($value) !== true) {
                    return '时间格式应为：2011-03-02 19:01:00';
                }
                break;
            case 'date':
                if (self::isDate($value) !== true) {
                    return '日期格式应为：2011-03-02';
                }
                break;
            case 'time':
                if (self::isTime($value) !== true) {
                    return '时间格式应为：19:01:00';
                }
                break;
            case 'year':
                if (self::isYear($value) !== true) {
                    return '年份格式应为：2011';
                }
                break;
        }
        /**
         * 按指定类型判断
         * forward_static_call(array('Validate', "is".ucfirst($validate)), $value);5.3之后支持
         * 这里forward_static_call是不会重置class information，如果call_user_func使用parent、
         * self、static等关键字可以起到同样的效果
         */
        $class = 'Validate';
        $method_name = 'is' . ucfirst($validate);
        if(class_exists($class)){
            if (method_exists($class, $method_name)) {
                return call_user_func("{$class}::{$method_name}", $value);
            }
        }
        if (method_exists(__CLASS__, $method_name)) {
            return call_user_func("self::{$method_name}", $value);
        }
        return '没有找到检测方法';
    }

    //检测是否为数字
    protected static function isNumber($value) {
        return is_numeric($value) ? true : '必须为一个数字';
    }

    //检查是否为移动电话
    protected static function isMobile($value) {
        return preg_match("/1[3-8]\d{9}$/",$value) ? true : '必须为一个有效的手机号';
    }

    //检查是否为有效年龄
    protected static function isAge($value) {
        return (is_numeric($value) && $value >= 0 && $value < 250) ? true : '必须为0到250岁的有效年龄';
    }

    //检查是否为有效邮政编码
    protected static function isPostcode($value) {
        return preg_match('/^[1-9]\d{5}$/', $value) ? true : '必须为有效邮政编码';
    }

    //检查是否为有效网址
    protected static function isUrl($value) {
        /* if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) !== FALSE ? true : '必须为有效网址';
        } */
        $strict = false;
        $validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=') . '\/0-9a-z]|(%[0-9a-f]{2}))';
        $ip_pattern = '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])';
        $hostname_pattern = '(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)';
        $url_pattern = '/^(?:(?:https?|ftps?|file|news|gopher):\/\/)' . (!empty($strict) ? '' : '?');
        $url_pattern .= '(?:' . $ip_pattern . '|' . $hostname_pattern . ')(?::[1-9][0-9]{0,3})?';
        $url_pattern .= '(?:\/?|\/' . $validChars . '*)?';
        $url_pattern .= '(?:\?' . $validChars . '*)?';
        $url_pattern .= '(?:#' . $validChars . '*)?$/i';
        $url_return = preg_match($url_pattern, $value);
        return $url_return ? true : '必须为有效网址';
    }

    //检查url是否为有效图片
    //http://i3.sinaimg.cn/ty/g/pl/2012-09-18/U370P6T12D6229820F44DT20120918104241.jpg
    protected static function isImage($value) {
        $isUrl = self::isUrl($value);
        if ($isUrl) {
            $imagedata = BaseModelHttp::get($value);
        } else {
            $imagedata = @file_get_contents($value);
        }
        if (empty($imagedata)) {
            return '文件无法访问';
        }
        $imageinfo = @getimagesize($value);
        if (empty($imageinfo)) {
            return '必须为有效图片';
        }
        return true;
    }

    //检查url是否为有效资源
    protected static function isResource($value) {
        $data = BaseModelHttp::header($value);
        if (empty($data)) {
            return '必须为有效资源';
        }
        return true;
    }

    //检查是否为有效日期
    protected static function isDatetime($value) {
        $matches = array();
        preg_match("/^(\d{1,4}-\d{1,2}-\d{1,2})\s(\d{1,2}:\d{1,2}:\d{1,2})$/", $value, $matches);
        if (empty($matches)) {
            return '时间格式应为：2011-03-02 19:01:00';
        }
        $date = $matches[1];
        $time = $matches[2];
        $isDate = self::isDate($date);
        $isTime = self::isTime($time);
        if ($isDate !== true) {
            return $isDate;
        }
        if ($isTime !== true) {
            return $isTime;
        }
        return true;
    }

    protected static function isDate($value) {
        $matches = array();
        preg_match("/^(\d{1,4})-(\d{1,2})-(\d{1,2})$/", $value, $matches);
        if (empty($matches)) {
            return '日期格式应为：2011-03-02';
        }
        $year = $matches[1];
        $month = $matches[2];
        $day = $matches[3];
        if (!checkdate($month, $day, $year)) {
            return '无效的日期';
        }
        return true;
    }

    protected static function isTime($value) {
        preg_match("/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/", $value, $matches);
        if (empty($matches)) {
            return '时间格式应为：19:01:00';
        }
        $hour = $matches[1];
        $minute = $matches[2];
        $second = $matches[3];
        $time = "2000-01-01 {$hour}:{$minute}:{$second}";
        $stamp = strtotime($time);
        if (empty($stamp)) {
            return '无效的时间';
        }
        return true;
    }

    //检查是否为有效年份
    protected static function isYear($value) {
        return (is_numeric($value) && $value >= 0 && $value < 9999) ? true : '必须为0至9999的有效年份';
    }

    //检查是否为有效月份
    protected static function isMonth($value) {
        return (is_numeric($value) && $value > 0 && $value < 13) ? true : '必须为有效月份';
    }

    //检查是否为有效日期
    protected static function isDay($value) {
        return (is_numeric($value) && $value > 0 && $value < 32) ? true : '必须为有效日期';
    }

    //检查是否为有效身份证,15位或者18位
    protected static function isIdcard($value) {
        /*$len = strlen($value);
        if ($len == 15){
            //"/\d{1,4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}/"
            // $Idcard_pattern = '/^([1-9]{0,1})?(\d){1,13}((\d)|x)?$/';
            $Idcard_pattern = '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/';
        } else if ($len == 18){
            // $Idcard_pattern = '/^([1-9]{0,1})?(\d){1,16}((\d)|x)?$/';
            $Idcard_pattern = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|(3[0-1]))\d{3}[\dx]$/';
            "[1-9]    \d{5}    [1-9]    \d{3}    ((0\d)|(1[0-2]))    (([0|1|2]\d)|(3[0-1]))    \d{3}    [\dx]";
            "4       31022    1      985      10                  07                      463      9";
        } else {
            return '必须为15位或者18位有效身份证号码';
        }
        if (preg_match($Idcard_pattern, $value)) {
            return true;
        }
        return '必须为有效身份证号码';*/
        $validator = new IdCardValidator($value);
        return $validator->isValid();
    }

    //检查是否为有效邮箱
    protected static function isEmail($value) {
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE ? true : '必须为有效邮箱';
        }
        $hostname_pattern = '(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)';
        $email_pattern = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . $hostname_pattern . '$/i';
        $email_return = preg_match($email_pattern, $value);
        $host_return = preg_match('/@(' . $hostname_pattern . ')$/i', $value, $regs);
        if ($email_return && $host_return) {
            if (function_exists('getmxrr') && getmxrr($regs[1], $mxhosts)) {
                return true;
            }
            if (function_exists('checkdnsrr') && checkdnsrr($regs[1], 'MX')) {
                return true;
            }
            return is_array(gethostbynamel($regs[1])) ? true : '必须为有效邮箱';
        }
        return '必须为有效邮箱';
    }

    //检查是否为IPv4
    protected static function isIPv4($value) {
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4)) !== FALSE ? true : '必须为有效IPv4地址';
        }
        $pattern = '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])';
        $ipv4_pattern = '/^' . $pattern . '$/';
        return preg_match($ipv4_pattern, $value) ? true : '必须为有效IPv4地址';
    }

    //检查是否为IPv6
    protected static function isIPv6($value) {
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV6)) !== FALSE ? true : '必须为有效IPv6地址';
        }
        $pattern  = '((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}';
        $pattern .= '(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})';
        $pattern .= '|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})';
        $pattern .= '(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)';
        $pattern .= '{4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
        $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}';
        $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|';
        $pattern .= '((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}';
        $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
        $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4})';
        $pattern .= '{0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)';
        $pattern .= '|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]';
        $pattern .= '\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4})';
        $pattern .= '{1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?';
        $ipv6_pattern = '/^' . $pattern . '$/';
        return preg_match($ipv6_pattern, $value) ? true : '必须为有效IPv6地址';
    }
}

class IdCardValidator {
    private $idcard;
    private static $ProvinceList = array(
                11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",
                23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",
                37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",
                50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",
                63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外",
    );
    
    /**
     * 身份证号码验证器,根据国家标准GB 11643-1999
     * @param string $idcard
     * @author 
     * @see http://www.sac.gov.cn/SACSearch/outlinetemplet/gjcxjg_qwyd.jsp?bzNum=GB%2011643-1999
     */
    public function __construct($idcard) {
        if ((strlen($idcard) == 15)) {
            //将15位身份证升级到18位
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
                $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            }else{
                $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
            }
            $masterNumber = $idcard;
            $checkNumber = $this->getCheckNumber($masterNumber);
            $idcard = $masterNumber . $checkNumber;
        }
        $this->idcard = $idcard;
    }
    
    /**
     * 判断身份证号码是否合法
     * @return string|boolean 合法则返回true,否则返回失败原因
     * @author 
     */
    public function isValid() {
        $idcard = $this->idcard;
        //检查长度
        if(18 !== strlen($idcard)) {
            return '长度不正确';
        }
        //检查地区
        $provinceCode = intval(substr($idcard, 0, 2));
        if (!key_exists($provinceCode, self::$ProvinceList)) {
            return '省份不正确';
        }
        //检查生日
        $year = substr($idcard, 6, 4);
        $month = substr($idcard, 10, 2);
        $day = substr($idcard, 12, 2);
        if (!checkdate($month, $day, $year)) {
            return '生日不正确';
        }
        //18位身份证校验码有效性检查
        $masterNumber = substr($idcard, 0, 17);
        $checkNumber = strtoupper(substr($idcard, 17, 1));
        if ($this->getCheckNumber($masterNumber) != $checkNumber) {
            return '校验码不正确';
        }
        return true;
    }
    
    /**
     * 获取身份证信息
     * @return boolean|array  
     * @author 
     */
    public function getInfo() {
        if ($this->isValid() !== true) {
            return false;
        }
        $idcard = $this->idcard;
        $info = array();
        $provinceCode = intval(substr($idcard, 0, 2));
        $info['province'] = self::$ProvinceList[$provinceCode];
        $year = substr($idcard, 6, 4);
        $month = substr($idcard, 10, 2);
        $day = substr($idcard, 12, 2);
        $info['birthday'] = "{$year}-{$month}-{$day}";
        $sexData = intval(substr($idcard, 14, 3));
        $info['gender'] = ($sexData % 2 === 0) ? '女' : '男';
        return $info;
    }
    
    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * @param string $masterNumber 本体码，身份证号码前17位
     * @return boolean|string
     * @author 
     */
    public function getCheckNumber($masterNumber) {
        if(strlen($masterNumber) != 17) {
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);//加权因子
        $checkSum = 0;
        for ($i = 0; $i < strlen($masterNumber); $i++) {
            $checkSum += substr($masterNumber, $i, 1) * $factor[$i];
        }
        $mod = $checkSum % 11;
        $checkNumberList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');//校验码对应值
        $checkNumber = $checkNumberList[$mod];
        return $checkNumber;
    }
}
