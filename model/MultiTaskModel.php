<?php
/*
 * Copyright (c) 2010,  新浪网运营部-网络应用开发部
 * All rights reserved.
 * @file: Thread.php
 * @description: 多线程
 * @author: junjie7@staff.sina.com
 * @date: 2013-2-5
 */
/**
 * 多任务并发类
 */
class MultiTask {

    /**
     * 执行后结果
     */
    public $result = array();

    /**
     * 检查线程间隔时间(微秒)，默认1秒
     */
    private $checkTime = 1000000;

    /**
     * 命令池
     */
    private $cmds = array();

    /**
     * 任务池
     */
    private $pool = array ();

    /**
     * 并发数
     */
    private $concurrency = 1;

    /**
     * 设置并发数
     */
    public function setConcurrency($concurrency = 1) {
        $this->concurrency = $concurrency;
    }

    public function setCheckTime($time){
        $this->checkTime = $time;
    }

    public function reset (){
        $this->result = array();
        $this->checkTime = 1000000;
        $this->cmds = array();
        $this->pool = array ();
        $this->concurrency = 1;
    }

    /**
     * 添加一个新任务.
     * 如果任务池满了,就先消化一个任务池内的任务.
     * @param String $cmd_php
     * @param array $cmd_param 支持直接将数组作为命令参数
     * @param array $param
     * @return bool
     *
     */
    public function addTask($cmd_php ,$cmd_param = array(),$param = array()) {
        if(empty($cmd_param)){
            $cmd = new Cmd($cmd_php,$param);
        }else{
            $cmd = new Cmd($cmd_php . ' "' . addslashes(json_encode($cmd_param)) . '"',$param);
        }
        $cmds = &$this->cmds;
        $cmds [] = $cmd;
        return true;
    }

    /**
     * 消化一个任务池内的任务,本方法是本程序的核心所在
     *
     * @return bool
     *
     */
    private function doWork() {
        $pool = &$this->pool;
        $cmds = &$this->cmds;
        foreach ( $pool as $tid => $task ) {
            $status = $task->status ();
            if ($task->isRunning ()) {
                if ($task->isTimeout ()) {
                    // 					echo "task:$tid:{$status['command']} timeout, force closed!\n";
                    $task->terminate ();
                    unset ( $pool [$tid] );
                }
            } else {
                $this->result[] = $task->getResult();
                // 				echo "task : " . $task->getCmd() . " is over, running " . $status ['excute_time'] . "\n";
                // 				ob_flush();
                // 				flush();
                $task->close ();
                unset ( $pool [$tid] );
                if(!empty($cmds)){
                    reset($cmds);
                    $cmd = &current($cmds);
                    $this->addToRun($cmd);
                    array_shift($cmds);
                }
            }
        }
        return true;
    }

    private function addToRun(Cmd $cmd){
        $this->pool [] = new Task($cmd->cmd,$cmd->param);
    }

    /**
     * 判断任务池满了
     *
     * @return bool
     *
     */
    private function isFull() {
        return count ( $this->pool ) >= $this->concurrency;
    }

    /**
     * 判断任务池非空
     *
     * @return bool
     *
     */
    private function notEmpty() {
        return ! empty ( $this->pool );
    }

    /**
     * 全部任务添加后的完成阶段
     */
    public function finish() {

        foreach ($this->cmds as $key => $cmd){
            if($key >= $this->concurrency)
                break;
            $this->addToRun($cmd);
            array_shift($this->cmds);
        }

        while ( $this->notEmpty () ) {
            $this->doWork ();
            usleep ( $this->checkTime );
        }
    }
}

/*
 * 命令类
 */
class Cmd {
    public $cmd;
    public $param;

    public function __construct($cmd, $param = array()) {
        $this->cmd = $cmd;
        $this->param = $param;
    }

}


/*
 * 任务类
 */
class Task {

    public $cmd;

    /**
     * 任务句柄
     */
    private $handle;

    /**
     * 任务开始时间
     */
    private $start_time;

    /**
     * 命令管道,包括 0=>输入 1=>输出 2=>错误
     */
    private $pipes = array ();

    /**
     * 任务状态
     */
    private $status = array ();

    /**
     * 指定任务运行所需的环境变量
     */
    private $env = array ();

    /**
     * 指定任务运行所需的当前路径
     *
     * @var string $cwd
     *
     */
    private $cwd = '';

    /**
     * 超时时间,单位为秒,默认为0,值为0时,永不超时.
     *
     * @var int $timeout
     *
     */
    private $timeout = 0;

    /**
     * 错误日志的路径
     */
    private $error_log = './error.log';

    /**
     * 结果文件路径
     */
    private $result_file_path = '';

    /**
     * 返回结果显示形式：1为所以队列执行完后显示，2为立即显示
     */
    private $result_show_type = 1;

    /**
     * 结果类型：1为json，2为文本；
     */
    private $result_type = 1;

    /**
     * 用构造函数来调用命令
     */

    public function getCmd(){
        return $this->cmd->cmd;
    }

    public function __construct($cmd, $param = array()) {

        $this->cmd = new Cmd($cmd,$param);

        // 		$this->cwd = getcwd ();
        // 只接受合法的参数
        foreach ( array ('env','cwd','timeout','error_log','result_file_path','result_type','result_show_type') as $validparam ) {
            if (isset ( $param [$validparam] )) {
                $this->$validparam = $param [$validparam];
            }
        }
        $desc = array (0 => array ('pipe','r'), 1 => array ('pipe','w'), 2 => array ('file',$this->error_log,'a'));
        // 执行命令
        $this->handle = proc_open ( $cmd, $desc, $this->pipes);
        // 把输出设成非阻塞
        stream_set_blocking ( $this->pipes [1], 0 );
        $this->start_time = microtime ( true );

        // 		echo "Task " . $this->getCmd() . " addToRun.". chr(10);

    }
    /**
     * 任务超时的判断,调用本方法前应先调用 $task->status()方法
     */
    public function isTimeout() {
        return $this->timeout ? $this->status ['excute_time'] >= $this->timeout : false;
    }

    /**
     * 判断任务是否在执行,调用本方法前应先调用 $task->status()方法
     */
    public function isRunning() {
        return $this->status ['running'];
    }

    /**
     * 正常结束任务
     */
    public function close() {
        if (is_resource ( $this->handle )) {
            proc_close ( $this->handle );
        }
    }

    /**
     * 获取运行结果
     */
    public function getResult(){
        if (is_resource($this->handle)) {

            if($this->result_file_path != ''){
                $result = file_get_contents($this->result_file_path);
                if(!empty($result)){
                    return unserialize($result);
                }
            }else{
                $result = stream_get_contents($this->pipes[1]);

                if($this->result_type == 1){
                    if(!empty($result)){
                        return json_decode($result,true);
                    }
                }else{
                    if($this->result_show_type == 2){
                        echo $result;
                    }
                    return $result;
                }
            }
            return array('error'=>1);
        }
    }

    /**
     * 强行终止超时的任务
     */
    public function terminate() {
        if (is_resource ( $this->handle )) {
            proc_terminate ( $this->handle );
            proc_close ( $this->handle );
        }
    }

    /**
     * 获取任务状态
     */
    public function status() {
        $status = & $this->status;
        // 获取进程句柄的状态
        $status = proc_get_status ( $this->handle );
        $status ['start_time'] = $this->start_time;
        $status ['excute_time'] = microtime ( true ) - $this->start_time;
        return $status;
    }
}

/**
 * demo
 */

// set_time_limit(0);
// $start_time = microtime(true);
// $m = new MultiTask ();
// $m->setConcurrency ( 10 );
// for($i = 0; $i < 100; $i ++) {
// 	// 正常执行的例子
// 	$m->addTask ('php tasktest.php' ,array('gid'=>$i));
// }
// // 任务结束,别忘了
// $m->finish ();
// var_dump($m->result);
// echo microtime(true) - $start_time;
?>
