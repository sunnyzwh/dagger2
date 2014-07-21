<?php

class BaseModelException extends Exception {

    public static $data;

    public function __construct($message=null, $code=0, $data = array()) {
        self::$data = self::daggerHtmlspecialchars($data);
        parent::__construct($message, $code);
    }

    public static function daggerClear($message) {
        return str_replace(array("\t", "\r", "\n"), " ", $message);
    }

    public static function daggerHtmlspecialchars($string) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::daggerHtmlspecialchars($val);
            }
        } else {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if(strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        }
        return $string;
    }
}

function daggerExceptionHandler($exception) {
    $errormsg = $exception->getMessage();
    $errno = $exception->getCode();
    $sendmsg = (empty($_SERVER['REQUEST_URI']) ? (empty($_SERVER['SCRIPT_FILENAME']) ? '' : '(' . $_SERVER['SCRIPT_FILENAME'] . ')') : '(' . $_SERVER['REQUEST_URI'] . ')') . $errormsg;
    //记录到监控中心
    switch (get_class($exception)) {
        case 'BaseModelDBException':
            BaseModelLog::sendLog($errno, $sendmsg, '', BaseModelLog::ERROR_MODEL_ID_DB);
            break;
        case 'BaseModelMCException':
            BaseModelLog::sendLog($errno, $sendmsg, '', BaseModelLog::ERROR_MODEL_ID_MC);
            break;
        case 'BaseModelHTTPException':
            BaseModelLog::sendLog($errno, $sendmsg, '', BaseModelLog::ERROR_MODEL_ID_HTTP);
            break;
        case 'BaseModelS3Exception':
            BaseModelLog::sendLog($errno, $sendmsg, '', BaseModelLog::ERROR_MODEL_ID_S3);
            break;
        default:
            BaseModelLog::sendLog($errno, $sendmsg, '', BaseModelLog::ERROR_MODEL_ID_DEFAULT);
            break;
    }
    defined('DAGGER_DEBUG') && BaseModelCommon::debug('[errro code] ' . $errno. ' [errro msg] ' . $errormsg . ' [详细说明]:https://github.com/wxkingstar/dagger/wiki/'.$errno, 'dagger_error');
    if (!defined('DAGGER_DEBUG') || defined('DAGGER_ENV') && DAGGER_ENV === 'product') {
        BaseModelMessage::showError('抱歉让您看到这个页面');
    } else {
        if (defined('QUEUE') || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            BaseModelMessage::showError($errormsg, BaseModelException::$data, $errno);
        } else {
            $trace = $exception->getTrace();
            $tracemsg = array();
            $pos = 0;
            foreach ($trace as $k => $error) {
                if (strpos($error['function'], "smarty_function_") === 0) {
                    $errormsg = 'smarty插件：'.substr($error['function'], 16).$errormsg;
                    $pos = false;
                    defined('DAGGER_DEBUG') && BaseModelCommon::debug($errormsg, 'dagger_error');
                }
                if(!isset($error['line']) && $pos === 0) {
                    $pos = $k;
                }
                if(!empty($error['function'])) {
                    $fun = '';
                    if(!empty($error['class'])) {
                        $fun .= $error['class'].$error['type'];
                    }
                    $fun .= $error['function'].'(';
                    if(!empty($error['args'])) {
                        $mark = '';
                        foreach($error['args'] as $arg) {
                            //由于smarty和call_user方法会出现超大对象或数组传入，导致报错页面崩溃，所以直接跳过参数解析输出
                            if (stripos($error['function'], 'smarty') === 0 || stripos($error['function'], 'call_user') === 0) {
                                continue;
                            }
                            $fun .= $mark;
                            if(is_array($arg)) {
                                $fun .= var_export($arg, true);
                            } else if(is_bool($arg)) {
                                $fun .= $arg ? 'true' : 'false';
                            } else if(is_int($arg) || is_float($arg)) {
                                $fun .= $arg;
                            } else if(is_null($arg)) {
                                $fun .= 'NULL';
                            } else {
                                $fun .= '\''.BaseModelException::daggerHtmlspecialchars(BaseModelException::daggerClear($arg)).'\'';
                            }
                            $mark = ', ';
                        }
                    }

                    $fun .= ')';
                    $error['function'] = $fun;
                }
                $tracemsg[] = array(
                        'file' => (isset($error['file']) ? str_replace(array(DAGGER_PATH_ROOT, '\\'), array('', '/'), $error['file']) : ''),
                        'line' => (isset($error['line']) ? $error['line'] : ''),
                        'function' => $error['function'],
                        );
            }
            $throwPhpCode = file($exception->getFile());
            $tracemsg = array_merge(array(array('file'=>str_replace(array(DAGGER_PATH_ROOT, '\\'), array('', '/'), $exception->getFile()), 'line'=>$exception->getLine(), 'function'=>$throwPhpCode[$exception->getLine() - 1])), $tracemsg);
            defined('DAGGER_DEBUG') && BaseModelCommon::debug(array_merge(array(array('File', 'Line', 'Function')), $tracemsg), 'dagger_error_trace');
            if($pos > 0) {
                $errorFileCode = array_slice(file($tracemsg[$pos]['file']), ($tracemsg[$pos]['line'] > 6 ? $tracemsg[$pos]['line'] - 7 : 0), 13);
                $tracemsg[$pos]['function'] = '<pre class="brush: php; toolbar : false; highlight: ' . $tracemsg[$pos]['line'] . '; first-line: ' . ($tracemsg[$pos]['line'] > 6 ? $tracemsg[$pos]['line'] - 6 : 1) . '">' . implode('', $errorFileCode) . '</pre>';
            }
            $traceTable = '';
            if(is_array($tracemsg) && !empty($tracemsg)) {
                $traceTable .= '<table cellpadding="5" cellspacing="1" width="100%" class="table">';
                $traceTable .= '<tr class="bg2"><th width="249px">File</th><th width="44px">Line</th><th width="629px">Function</th></tr>';
                foreach($tracemsg as $k => $msg) {
                    $traceTable .= '<tr class="' . ($pos > 0 && $k == $pos ? 'bg3' : 'bg1') . '">';
                    $traceTable .= '<td>'.$msg['file'].'</td>';
                    $traceTable .= '<td>'.$msg['line'].'</td>';
                    $traceTable .= '<td>'.$msg['function'].'</td>';
                    $traceTable .= '</tr>';
                }
                $traceTable .= '</table>';
            }
            BaseModelDebug::showTrace($errno, $errormsg, $traceTable);
        }
    }
}

set_exception_handler('daggerExceptionHandler');
