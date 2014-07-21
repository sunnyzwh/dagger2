<?php
/**
 * All rights reserved.
 * 文件操作基类
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/2 11:48
 * @version         Id: 0.9
*/

class BaseModelFile {
    
    protected $path;
    
    protected $location;
    
    /*
     * @param $path 文件路径，相对路径，不用传入第一个斜线，如：abc/a.jpg
     * @param $location cache|data|vfs 默认为cache
     */
    public function __construct($path, $location = 'cache') {
        if (DAGGER_PLATFORM == 'sae') {
            $path = ltrim($path, "./");
        }
        switch ($location) {
            case 'data':
                $this->path = DAGGER_PATH_DATA . $path;
            break;
            case 'cache':
                $this->path = DAGGER_PATH_CACHE . $path;
            break;
            case 'log':
                $this->path = DAGGER_PATH_APPLOG . $path;
            break;
            default:
                $this->path = $path;
            break;
        }
        $this->location = $location;
    }
    
    /*
     * 内容读取
     * 错误返回false，正确返回读取内容
     */
    public function read() {
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($this->path, 'file_read');
        return @file_get_contents($this->path);
    }
    
    /*
     * 内容写入
     * 错误返回false，正确返回写入字节数
     */
    public function write($str) {
        $dirName = dirname($this->path);
        BaseModelCommon::recursiveMkdir($dirName);
        defined('DAGGER_DEBUG') && BaseModelCommon::debug($this->path, 'file_write');
        return file_put_contents($this->path, $str);
    }
    
    /*
     * 内容追加
     * 错误返回false，正确返回写入字节数
     */
    public function writeTo($str) {
        if (DAGGER_PLATFORM == 'sae') {
            if ($this->location == 'log') {
                ini_set("display_errors","Off");
                sae_debug($str);
                ini_set("display_errors","On");
            } else {
                throw new BaseModelException("SAE不支持追加写入功能", 90900, 'file_trace');
            }
        } else {
            $dirName = dirname($this->path);
            BaseModelCommon::recursiveMkdir($dirName);
            $fp = fopen($this->path, "a");
            $num = fwrite($fp, $str);
            fclose($fp);
            return $num;
        }
    }
}
?>
