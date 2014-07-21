<?php
class BaseModelXPath 
{
    /**
     * @var DOM资源描述符
     */
    private $xpath;

    /**
     * 构造函数
     * @var string $html 传入的xml/html字符串
     */
    public function __construct ($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);
    }

    /**
     * @var string $query 合法的xpath查询
     * @return DOMList
     */
    public function query ($query) {
        return $this->xpath->query($query);
    }
}
