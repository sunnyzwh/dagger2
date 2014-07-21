<?php
function smarty_function_pagelet($param, &$smarty)
{
    BasePagelet::factory($param['id'], $param);
}
