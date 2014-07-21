<?php

class BaseModelHTTPException extends BaseModelException
{
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }
}
