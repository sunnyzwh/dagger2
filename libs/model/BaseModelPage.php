<?php
/**
 * All rights reserved.
 * 翻页逻辑基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
 */

class BaseModelPage
{
    protected $prePage;//上一页
    protected $nextPage;//下一页
    protected $firstPage = 1; //第一页
    protected $lastPage;//最后一页
    protected $pageStr;//翻页导航
    protected $totalNum = 0;//总个数
    protected $pageSize = 10;//每页显示几个
    protected $paramStr = '';//页面参数
    protected $totalPage =  0;//总页数
    protected $page = 0;//当前页数
    protected $pName = 'page';// 翻页参数名
    protected $limit = '';//SQL——limit
    protected $style = 0;//翻页样式
    protected $params = array();//参数数组

    /**
     * 翻页页码初始化
     * @param int $totalNum 总条数
     * @param int $pageSize 每页显示条数 
     * @param int $params 链接参数，默认为空时使用$_GET
     * @param int $pName 翻页页码数参数名称，默认为page
     * @return void
     */
    public function __construct($totalNum, $pageSize, $params = array(), $pName = 'page')
    {
    	$pageSize = intval($pageSize);
        if (empty($pageSize)) {
            return false;
        }
        empty($params) && $params = BaseModelRouter::$get;
        //基本数据计算
        $this->totalNum = max(intval($totalNum), 0); //自然数
        $this->pageSize = max($pageSize, (-1*$pageSize)); //自然数，当$pageSize=0时不分页
        $this->params = $params;
        empty($pName) || $this->pName = $pName;
        $this->page = isset($this->params[$this->pName]) ? max($this->params[$this->pName], 1) : 1;
        $this->totalPage = ceil($this->totalNum/$this->pageSize);
        // $this->page = min($this->page, $this->totalPage);
        $this->prePage = max($this->page - 1, 1);//上一页
        $this->nextPage = min($this->page + 1, $this->totalPage);//下一页
        $this->lastPage = $this->totalPage;//最后一页
        //limit计算
        $this->page || $this->page = 1;
        $this->limit  = " LIMIT " . ($this->page -1) * $this->pageSize . ', ' . $this->pageSize;//用于 MySQL 分页生成语句
        //url参数计算
        unset($this->params[$this->pName]);
        if (defined('QUEUE') || defined('EXTERN')) {
            $this->paramStr = "?" . http_build_query($this->params); 
        } else {
            $this->paramStr = Router::createUrl('', '', $this->params);
        }
        if(strpos($this->paramStr, '?') !== false){
            $this->paramStr .= '&';
        }else{
            $this->paramStr .= '?';
        }
        return;
    }

    /**
     * 获取limit语句
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * 设置翻页页码样式，样式文件在templates目录下的page目录中
     * @param int $style 选择样式编号
     * @return void
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * 构造翻页字符串
     * @return string 返回翻页页码html代码
     */
    public function getPageStr()
    {
        if (DAGGER_TEMPLATE_ENGINE === 'smarty') {
            $tpl = new BaseViewSmarty();
        } else {
            $tpl = new BaseView();
        }
        $assign = array(
            'totalPage'=>$this->totalPage,
            'pageSize'=>$this->pageSize,
            'prePage'=>$this->prePage,
            'nextPage'=>$this->nextPage,
            'firstPage'=>$this->firstPage,
            'lastPage'=>$this->lastPage,
            'totalNum'=>$this->totalNum,
            'pName'=>$this->pName,
            'page'=>$this->page,
            'paramStr'=>$this->paramStr
        );
        $format = isset($_REQUEST['format']) ? $_REQUEST['format'] : '';
        switch($format){
        case 'xml':
        case 'json':
            return (!$this->totalPage) ? array() : $assign;
            break;
        default:
            foreach($assign as $key=>$val){
                $tpl->assign($key, $val);
            }
            $style = intval($this->style);
            return (!$this->totalPage) ? '' : $tpl->fetch('page/style_' . $style . '.html');
        }
    }
}
