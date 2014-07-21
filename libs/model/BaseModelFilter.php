<?php
/*********************************************************************************
	Copyright (c) 2010, 新浪网-运营部-网络应用开发部 
	All rights reserved. 
	@程序名称：BaseModelFilter.php
	@程序功能：标签过滤、修复类，需要服务器安装tidy扩展支持
	@修　　改：刘松 liusong2@staff.sina.com.cn
	@版　　本：1.00
	@修改历史：
    @ example 
    $filter = new BaseModelFilter;
    $html = $filter->tidy($html);
*********************************************************************************/
class BaseModelFilter {
	private $forbiddenTags = array('meta', 'xml', 'title', 'head', 'link', 'script', 'style', 'iframe');
	private $allowIds = array();
	private $allowClasses = array();
    
    
	public function __constuct() {
		if(!extension_loaded('tidy')) {
			dl('tidy.so');
		}
	}
    
    public  function setForbiddenTags(array $forbiddenTags) {
        $this->forbiddenTags = $forbiddenTags;
    }
    
    public  function setAllowIds(array $allowIds) {
        $this->allowIds = $allowIds;
    }
    
    public  function setAllowClasses(array $allowClasses) {
        $this->allowClasses = $allowClasses;
    }

	public  function tidy($html, $encoding = 'utf-8') {
		if ($html == '') {
			return false;
		}

		$output = '';
		$html = trim($html);

		//对于非utf-8编辑处理
		if($encoding !== 'utf-8') {
			$html = BaseModelCommon::convertEncoding($html, 'utf-8', $encoding);
		}
		$html = preg_replace("|\/\*(.*)\*\/|sU", "", $html);//过滤掉全部注释内容
		$html = preg_replace("/<!\[CDATA\[(.*?)\]\]>/is", "\\1", $html);//过滤掉CDATA标签
		$html = $this->_escapeUnicode($html);//转义Unicode字符

		$tidy_conf = array(
			'output-xhtml' => true,
			'show-body-only' => true,
			'join-classes' => true
		);

		$html = str_replace("&", "&amp;", $html);
		$dom = tidy_parse_string($html, $tidy_conf, 'utf8');
		$body = $dom->body();
		if($body->child){
			foreach($body->child as $child) {
				$this->_filterNode($child, $output);
			}
		}

		$html = $this->_unEscapeUnicode($output);//反转义Unicode字符
		if($encoding !== 'utf-8') {
			$html = BaseModelCommon::convertEncoding($html, $encoding, 'utf-8');
		}
		
		$html = $this->_insertVideo($html);
		return $html;
	}

	private function _filterAttr($nodeName, $attrName, &$attrValue) {
		if ($attrName == "id" ) {
			foreach ($this->allowIds as $allow_id) {
				if(preg_match ("/{$allow_id}/isU", $attrValue, $arr)) {
					$isIdAllowed = true;
				}
			}

			if (!$isIdAllowed) {
				return false;
			}

		}
		
		if ($attrName == "class" ) {
			foreach ($this->allowClasses as $allow_class) {
				if (preg_match ("/{$allow_class}/isU", $attrValue, $arr)) {
					$isClassAllowed = true;
				}
			}

			if (!$isClassAllowed) {
				return false;
			}
		}
		
		if($nodeName == "param" || $nodeName == "embed") {
			if (strtolower($attrValue) == "captioningid" || $attrName == "captioningid"){
				return false;
			}

			if ($nodeName == "param" && $attrName == 'name' &&  strtolower($attrValue) == 'allowscriptaccess') {
				$attrValue = 'AllowScriptAccess_old';
			}
		}

		if (in_array('script', $this->forbiddenTags, true)) {
			if (substr($attrName, 0, 2) == 'on') {
				return false;
			} else if (in_array($attrName, array('src', 'href', 'codebase', 'dynsrc', 'content', 'datasrc', 'data'), true) && preg_match("/^(javascript|mocha|livescript|vbscript|about|view-source):/i", $attrValue) ) {
				// 判断属性中是否含有js
				return false;
			} else if (strpos(strtolower(trim($attrValue)),'javascript:') !== false 
					|| strpos(strtolower(trim($attrValue)),'vbscript:') !== false
					|| strpos(strtolower(trim($attrValue)),'expression') !== false) {
				return false ;//过滤js注入
			} else if (strtolower(trim($attrName))=="style") {
				$attrValue2 = $this->_unEscapeUnicode($attrValue);
				$attrValue2 = unhtmlentities2($attrValue2); 
				$attrValue2 = preg_replace("|\/\*(.*)\*\/|sU", "", $attrValue2);//过滤注释
				$attrValue2 = stripslashes($attrValue2);  
				$reg_data  = array( iconv("GBK", "UTF-8", "ｅ"),
									iconv("GBK", "UTF-8", "ｘ"),
									iconv("GBK", "UTF-8", "ｐ"),
									iconv("GBK", "UTF-8", "ｒ"),
//									iconv("GBK", "UTF-8", "ｅ"),
									iconv("GBK", "UTF-8", "ｓ"),
									iconv("GBK", "UTF-8", "ｉ"),
									iconv("GBK", "UTF-8", "ｏ"),
									iconv("GBK", "UTF-8", "ｎ")
							);
				$rep_data = array("e","x","p","r","s","i","o","n");
				$attrValue2 = str_replace($reg_data , $rep_data , $attrValue2);

				if ( strpos(strtolower(trim($attrValue2)), 'javascript:') !== false ) {
					 return false ;
				}
				if ( strpos(strtolower(trim($attrValue2)), 'expression') !==false ) {
					 return false ;
				}

				//$reg_data = array("ｅ","ｘ","ｐ","ｒ","ｅ","ｓ","ｉ","ｏ","ｎ"); 
				//$reg_data  = array(iconv("GBK", "UTF-8", "ｅ"),iconv("GBK", "UTF-8","ｘ"),iconv("GBK", "UTF-8","ｐ"),iconv("GBK", "UTF-8","ｒ"),iconv("GBK", "UTF-8","ｅ"),iconv("GBK", "UTF-8","ｓ"),iconv("GBK", "UTF-8","ｉ"),iconv("GBK", "UTF-8","ｏ"),iconv("GBK", "UTF-8","ｎ"));

				//$rep_data = array("e","x","p","r","e","s","i","o","n");
				$attrValue = str_replace($reg_data, $rep_data, $attrValue);

				$reg_data = array("E","X","P","R","S","I","O","N");
				$rep_data = array("e","x","p","r","s","i","o","n");
				$attrValue = str_replace($reg_data, $rep_data, $attrValue);

				$attrValue = str_replace('expression', 'expression_x', $attrValue);
				$attrValue = str_replace('eval', '', $attrValue);
			}
		}

		return true;
	}

	private function _insertVideo(&$message) {
		if(stripos($message, 'vurl') !== false) {
			if (preg_match_all('/<img.*vurl="(.*?)".*?>/is', $message, $matchs)) {
				if($this->_checkValidUrls($matchs[1]) === false) {
					$message = preg_replace('/<img.*vurl="(.*?)".*?>/is', '', $message);		
				} else {
					$message = preg_replace('/<img.*vurl="(.*?)".*?>/is', '<object width="482" height="388"><param name="AllowScriptAccess_old" value="always"><embed allowscriptaccess="samedomain" pluginspage="http://www.macromedia.com/go/getflashplayer" src="$1" type="application/x-shockwave-flash" name="articlevblog" allowfullscreen="true" width="482" height="388"><embed allowscriptaccess="never"></object>', $message);	
				}	
			}			
		}
		return $message;
	}

	private function _checkValidUrls($urls) {
		foreach($urls as $url) {
			$arr = parse_url($url);
			if(preg_match('/youku.com|sina.com.cn|ku6.com|56.com|tudou.com/i', $arr['host']) === 0) {
				return false;    
			}
		}
		return true;
	}

	private function _filterNode($node, &$output){

		//查看节点名，如果是<script> 和<style>就直接清除
		if (in_array($node->name, $this->forbiddenTags, true)) {
			return '';
		}

		if ($node->type == TIDY_NODETYPE_TEXT){
			/*
			 如果该节点内是文字
			*/
			$output .= $node->value;
			return;
		}

		//不是文字节点，那么处理标签和它的属性
		$output .= '<'.$node->name;

		//检查每个属性
		if ($node->attribute) {
			foreach ($node->attribute as $name=>$value) {
				/*
				 清理一些DOM事件，通常是on开头的，
				 比如onclick onmouseover等....
				 或者属性值有javascript:字样的，
				 比如href="javascript:"的也被清除.
				 */
				if ($this->_filterAttr($node->name, $name, $value)) {
					$output .= ' '.$name.'="'.htmlspecialchars($value).'"';
				}
			}
		}

		if ($node->type == TIDY_NODETYPE_START && !in_array($node->name, array('img', 'br', 'hr'), true)) {
			$output .= '>';
			//递归检查该节点下的子节点
			if ($node->hasChildren()){
				foreach ($node->child as $child) {
					$this->_filterNode($child, $output);
				}
			}

			if ('object' == $node->name) {
				$output .= '<embed allowscriptaccess="never"></embed>';
			}

			//闭合标签
			$output .= '</'.$node->name.'>';
		} else {
			//对单体标签，比如<hr/> <br/> <img/>等直接以 />闭合
			$output .= '/>';
		}
	}

	/**
	 * 临时对Unicode编码的字符进行自定义转义，去掉起始的&符号，方便对其他的&符号做全局转义
	 *
	 * @param string $str
	 * @return string
	 */
	private function _escapeUnicode($str) {
		$str = preg_replace("/&(#?[0-9a-zA-Z]{2,7});/", "__".md5('sina_word_left')."_\\1_".md5('sina_word_right')."__", $str);
		return $str;
	}
	/**
	 * 对临时自定义转义了的字符进行反转义
	 *
	 * @param string $str
	 * @return string
	 */
	private function _unEscapeUnicode($str) {
		$str = preg_replace("/__".md5('sina_word_left')."_(#?[0-9a-zA-Z]{2,7})_".md5('sina_word_right')."__/U", "&\\1;", $str);
		return $str;
	}
}

function unhtmlentities2_preg_callback($a) {
	return chr($a[1]);
}

function unhtmlentities2($string) {
	// replace numeric entities
	$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
	$string = preg_replace('~\\\\([0-9a-f]{2,})~ei', 'chr(hexdec("\\1"))', $string);
	$string = urldecode($string);
	$string = preg_replace_callback('~&#([0-9]+);?~', 'unhtmlentities2_preg_callback', $string);
	$string = str_replace(' ' , '' , $string);
	// replace literal entities
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}

?>