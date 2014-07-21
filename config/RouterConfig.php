<?php
/**
* 路由配置信息
* 具体配置请参考http://wiki.intra.sina.com.cn/pages/viewpage.action?pageId=5509598
* example:
*   static public $baseUrl = array(
        DAGGER_APP_EXAMPLE=>'local.dagger.com/mv', //不带http://
        DAGGER_APP_ADMIN=>''
    );

    static public $config = array(
        DAGGER_APP_EXAMPLE=>array(
            'blog'=>array(
                'view'=>'<id?\d+>'
            ),
            'default'=>array(
                'view'=>'<author?a_\d+:a_>/<status?s_\d+:s_>'
            )
        ),
        DAGGER_APP_ADMIN => array(
        ),
    );
*/
class RouterConfig {
    static public $baseUrl = array(
        DAGGER_APP_SITE=>'',
        DAGGER_APP_EXAMPLE=>'',
        DAGGER_APP_ADMIN=>''
    );

    //注：如果没有设置以上的默认路由，配置数组的最后一个为默认路由，请勿轻易修改
    static public $config = array(
        DAGGER_APP_EXAMPLE=>array(
            'default'=>array(
                'view'=>'<year?20\d{2}>/<mid?m_\d+:m_>/<cid?c_\d+:c_>',//格式：<参数名?正则:参数值忽略前缀> , 其中参数值忽略前缀可以省略
                'db'=>'',
                'mc'=>'',
                's3'=>'',
                'http'=>'',
                'image'=>'',
                'page'=>'<year?20\d{2}>',
            )
        ),
        DAGGER_APP_SITE=>array(
            'default'=>array(
                'view'=>'<year?20\d{2}>/<mid?m_\d+:m_>/<cid?c_\d+:c_>',//格式：<参数名?正则:参数值忽略前缀> , 其中参数值忽略前缀可以省略
                'db'=>'',
                'mc'=>'',
                's3'=>'',
                'http'=>'',
                'image'=>'',
                'page'=>'<year?20\d{2}>',
                'login'=>'',
            ),
            'user'=>array(
                'login'=>'',
                'register'=>'',
                'find_password'=>'',
                'reset_password'=>'',
                'reset_password_manually'=>'',
                'image'=>'',
            ),
            'comment'=>array(
                'add'=>'',
                'modify'=>'',
                'delete'=>'',
                'get_list'=>'',
            ),
        ),
        DAGGER_APP_ADMIN => array(
            'admin'=>array(
                'login'=>''
            )
        ),
    );

    //默认controller或默认action
    static public $defaultRouter = array(
        DAGGER_APP_SITE=> array(
            'default_controller' => 'default',
            'default_action' => array(
                'default' => 'view',        //DefaultController的默认action为view
                'weibo_monitor' => 'show',  //WeiboMonitorcontroller的默认action为show
            )
        ),
        DAGGER_APP_EXAMPLE => array(
            'default_controller' => 'default',
            'default_action' => array(
                'default' => 'view',        //DefaultController的默认action为view
                'weibo_monitor' => 'show',  //WeiboMonitorcontroller的默认action为show
            )
        )
    );


    private function __construct() {
        return;
    }
}
