<?php
/**
 * All rights reserved.
 * 信息提示基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
 */

class BaseModelMessage {

    //private static $fieldsArr = array();

    private function __construct() {}



    /**
     * 正确信息提示，包括页面或api，错误号code为0
     * @param string $msg 信息提示, 输出接口中：$data['status']['msg'];
     * @param array $data 数据内容, 输出接口中：$data['data'];
     * @param array $otherData 扩展数据内容, 输出接口中：$data['xxx']; 与以上data节点同级
     * @param string $url 如果是页面提示形式，提示完成后跳转的url
     * @param int $t 提示完成后调整的等待时间，默认为3秒
     * @param string $ie 数据内容编码，默认支持为utf-8
     * @param string $oe 输出内容编码，默认为utf-8（json结果输出时设置无效,统一为unicode）
     */
    public static function showSucc($msg, $data=array(), $otherData=array(), $url='', $t=3, $ie='', $oe='UTF-8') {
        self::message(0, $msg, $data, $url, $t, $otherData, $ie, $oe);
    }


    /**
     * 错误信息提示，包括页面或api
     * @param string $msg 信息提示, 输出接口中：$data['status']['msg'];
     * @param array $data 数据内容, 输出接口中：$data['data'];
     * @param int $code 错误号，默认为11
     * @param string $url 如果是页面提示形式，提示完成后跳转的url
     * @param int $t 提示完成后调整的等待时间，默认为3秒
     * @param string $ie 数据内容编码，默认支持为utf-8
     * @param string $oe 输出内容编码，默认为utf-8（json结果输出时设置无效,统一为unicode）
     */
    public static function showError($msg, $data=array(), $code=11, $url='', $t=3, $ie='', $oe='UTF-8') {
        self::message($code, $msg, $data, $url, $t, array(), $ie, $oe);
    }

    /**
     * 输出json/xml/html格式的消息。该函数参数很多，还读取$_REQUSET的format、fileds参数，很凶残呐
     * @param int $code 错误号， 0表示没有错误发生
     * @param string $msg 结果描述
     * @param array $data 数据，可以是一维数组，也可以是二维数组， 仅在输出json/xml数据时有用
     * @param string $url 将要跳转的页面，仅在输出html页面时使用
     * @param int $t 跳转等待时间，仅在输出html页面时使用
     * @param array $otherData 消息的补充字段， 仅在输出json/xml数据时有用
     * @param string $ie 输入数据的编码，默认为gbk
     * @param string $oe 输出数据的编码，默认为utf8
     */
    protected static function message($code, $msg, $data, $url, $t, $otherData=array(), $ie='', $oe='UTF-8') {

        $format = empty($_REQUEST['format']) ? '' : strtolower($_REQUEST['format']);
        if (isset($_GET['oe']) && in_array(strtoupper($_GET['oe']), array('GBK', 'UTF-8'), true)) {
            $oe = $_GET['oe'];
        }
        $oe = $format === 'json' ? 'UTF-8' : $oe;// 标准的json只支持utf8中文
        $code = intval($code);
        // 转码
        if(!empty($ie) && strcasecmp($ie, $oe) !== 0) {
            $msg = BaseModelCommon::convertEncoding($msg, $oe, $ie);
            $data = BaseModelCommon::convertEncoding($data, $oe, $ie);
            $otherData = BaseModelCommon::convertEncoding($otherData, $oe, $ie);
        }

        /*
        支持get参数fields字段筛选结果，用于节约带宽
        //如果传入了fields字段，返回结果只显示指定字段，fields内容使用半角逗号隔开
        // 传fileds参数 && 返回的data是数组 && data不为空 && (data的第一维存在字符串key,对该key进行筛选 || (存在数字0的键，并且0的值为数组，则对下一维数组进行逐个筛选a))
        if (!empty($_GET['fields']) && is_array($data) && !empty($data) && (!isset($data['0']) || (!empty($data['0']) && is_array($data['0'])))) {
            $data = self::_checkFields($data);
        }
        */

        // 依据不同格式选择性输出
        switch($format) {
            case 'xml':
                header("Content-Type: text/xml; charset=" . strtoupper($oe));
                $outArr = array();
                if (!is_array($msg)) {
                    $outArr['status']['code'] = $code;
                    $outArr['status']['msg'] = $msg;
                    if (is_array($otherData)) {
                        foreach ($otherData as $k=>$v) {
                            if (!in_array($k, array('status', 'data'), true)) {
                                $outArr[$k] = $v;
                            }
                        }
                    }
                    $outArr['data'] = $data;
                } else {
                    $outArr = $msg;
                }
                $xml = new BaseModelXML();
                $xml->setSerializerOption(XML_SERIALIZER_OPTION_ENCODING, $oe);
                echo $xml->encode($outArr);
            break;
            case 'json':
                $outArr = array();
                if (!is_array($msg)) {
                    $outArr['status']['code'] = $code;
                    $outArr['status']['msg'] = $msg;
                    if (is_array($otherData)) {
                        foreach ($otherData as $k=>$v) {
                            if (!in_array($k, array('status', 'data'), true)) {
                                $outArr[$k] = $v;
                            }
                        }
                    }
                    $outArr['data'] = $data;
                } else {
                    $outArr = $msg;
                }
                $json = json_encode($outArr);
                $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
                if (preg_match("/^[a-zA-Z][a-zA-Z0-9_\.]+$/", $callback)) {
                    if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') { //POST
                        header("Content-Type: text/html");
                        $refer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : array();
                        if(!empty($refer) && (substr($refer['host'],-9,9)=='weibo.com')){
                            $result = '<script>document.domain="weibo.com";';
                        }else{
                            $result = '<script>document.domain="sina.com.cn";';
                        }
                        $result .= "parent.{$callback}({$json});</script>";
                        echo $result;
                    } else {
                        if(isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                            header('Content-Type: text/javascript; charset=UTF-8');
                        } else {
                            header('Content-Type: application/javascript; charset=UTF-8');
                        }
                        echo "{$callback}({$json});";
                    }
                } elseif ($callback) {
                    header('Content-Type: text/html; charset=UTF-8');
                    echo 'callback参数包含非法字符！';
                } else {
                    if(isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                        header('Content-Type: text/plain; charset=UTF-8');
                    } else {
                        header('Content-Type: application/json; charset=UTF-8');
                    }
                    echo $json;
                }
            break;
            default:
                if (defined('QUEUE') || defined('EXTERN')) {
                    BaseModelDebug::queueOut($code, $msg, $oe);
                    return;
                }
                try {
                    $tpl = new BaseView();
                    $tpl->assign('msg', $msg);
                    $tpl->assign('url', $url);
                    $tpl->assign('t', $t);
                    if ($code == '0') {
                        $tpl->display('message/message.html');
                    } else {
                        $tpl->display('message/error.html');
                    }
                } catch(Exception $e) {
                    // 默认模板
                    $html = <<<OUT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>提示信息</title>
<meta name="keywords" content="提示信息" />
<style type="text/css">
<!--
/* 初始化CSS */
html, body, ul, li, ol, dl, dd, dt, p, h1, h2, h3, h4, h5, h6, form, fieldset, legend,img{margin:0;padding:0;}
fieldset,img{border:none;}
address,caption,cite,code,dfn,th,var{font-style:normal;font-weight:normal;}
ul,ol{list-style:none;}
select,input{vertical-align:middle;}
select,input,textarea{font-size:12px;margin:0;}
table{border-collapse:collapse;}
body{background:#fff;color:#333;padding:5px 0;font:12px/20px "SimSun","宋体","Arial Narrow";}

.clearfix:after{content:".";display:block;height:0;visibility:hidden;clear:both;}
.clearfix{zoom:1;}
.clearit{clear:both;height:0;font-size:0;overflow:hidden;}

a{color:#009;text-decoration:none;}
a:visited{color:#800080;}
a:hover, a:active, a:focus{color:#f00;text-decoration:underline;}
a.linkRed:link,a.linkRed:visited{color:#f00!important;}/* 红色 */
a.linkRed:hover{color:#c00!important;}
a.linkRed01:link,a.linkRed01:visited{color:red!important}
a.linkRed01:hover{color:red!important}

.alert{margin:150px auto;width:390px;height:201px;padding:75px 30px 0;background:url(http://i1.sinaimg.cn/dy/deco/2012/0426/pic_m_01.png) no-repeat 0 0;color:#482400;font-size:14px;line-height:38px;text-align:center;}
.error{margin:150px auto;width:390px;height:201px;padding:75px 30px 0;background:url(http://i1.sinaimg.cn/dy/deco/2012/0426/pic_m_02.png) no-repeat 0 0;color:#482400;font-size:14px;line-height:38px;text-align:center;}
-->
</style>
</head>
<body>
OUT;
                    $html .= "<div class=\"" . ($code == '0' ? 'alert' : 'error') . "\">";
                    $html .= $msg;
                    $html .= empty($url) ? '' : "<div>{$t}秒钟后跳转下一页面</div><div><a href=\"{$url}\">点击直接跳转</a></div>";
                    $html .= "</div></body></html>";
                    print($html);
                }
            break;
        }
        exit;
    }

    private static function _checkFields($data) {
        empty(self::$fieldsArr) && self::$fieldsArr = array_flip(explode(',', $_GET['fields']));
        $newData = array();
        if(!isset($data['0'])) {
            //一维数组
            $newData = array_intersect_key($data, self::$fieldsArr);
        } else {
            // 多维数组
            if(is_array($data['0']['data']) && count($data['0']['data'] > 0)) {
                foreach($data as $kk => $vv) {
                    $data[$kk]['data'] = array_map(array('self', '_checkFields'), $vv['data']);
                }
                $newData = $data;
            } else {
                //二维数组,要求数组里每一个一维数组的键名都是一样的
                foreach ($data as $kk => $vv) {
                    $newData[$kk] = array_intersect_key($vv, self::$fieldsArr);
                }
            }
        }
        return $newData;
    }
}
