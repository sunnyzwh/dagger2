<?php

class PAPagelet extends Pagelet {

    public function run($params){
        $this->setView($params);
        $this->setView('hello', 'hello');
        $this->display('pagelets/test.tpl');
    }

}
