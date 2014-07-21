<?php

class BaseModelDBException extends BaseModelException
{
    public function __construct($message=null, $code=0, $data = array()) {
        if($code === 90311) {
            $message .= " client[{$_SERVER['REMOTE_ADDR']}],server[{$_SERVER['SERVER_ADDR']}],dbconnect[".implode('|', BaseModelDBConnect::getLinkInfo())."]";
        }
	parent::__construct($message, $code, $data);
    }
}
