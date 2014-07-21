<?php
/**
 * BaseModelXML
 *
 * Convert between xml and array
 *
 * PHP versions 5
 *
 * @package   BaseModelXML
 * @author     <>
 * @copyright sina
 */

/**
 * option: Version of xml file
 *
 * Possible values:
 * - any version number
 * - 1.0 (default)
 */
define("XML_SERIALIZER_OPTION_VERSION", "version");

/**
 * option: Line break char
 *
 * Possible values:
 * '\r\n'
 * - '\n' (default)
 */
define("XML_SERIALIZER_OPTION_LINEBREAK", "lineBreak");

/**
 * option: Indent char
 *
 * Possible values:
 * - '\t'
 * - ' ' (default)
 */
define("XML_SERIALIZER_OPTION_INDENTCHAR", "indentChar");

/**
 * option: Number of indent chars
 *
 * Possible values:
 * - any
 * - 4 (default)
 */
define("XML_SERIALIZER_OPTION_INDENTLEN", "indentLen");

/**
 * option: Encoding of the XML document
 *
 * Possible values:
 * - UTF-8(default)
 */
define("XML_SERIALIZER_OPTION_ENCODING", "encoding");

/**
 * option: root of XML
 *
 * Possible values:
 * - any string
 */
define("XML_SERIALIZER_OPTION_ROOT", "root");

/**
 * Convert between xml and array
 *
 * This class can be used in two modes:
 *
 *  1. Create an XML string from an array
 *    <code>
 *    $data = array(
 *        'channel' => array(
 *            'title' => 'Example RDF channel',
 *            'link'  => 'http://www.php-tools.de',
 *            'image' => array(
 *                'title' => 'Example image',
 *                'url'   => 'http://www.php-tools.de/image.gif',
 *                'link'  => 'http://www.php-tools.de'
 *            ),
 *        )
 *    );
 *    </code>
 *
 *    To create an XML string from this array, do the following:
 *
 *    <code>
 *    require_once 'BaseModelXML.php';
 *    $options = array(
 *       XML_SERIALIZER_OPTION_VERSION     => "1.0",     // xml version
 *       XML_SERIALIZER_OPTION_INDENTCHAR  => " ",     // indent with tabs
 *       XML_SERIALIZER_OPTION_LINEBREAK   => "\n",     // use UNIX line breaks
 *       XML_SERIALIZER_OPTION_INDENTLEN   => 4,        // indent len
 *       XML_SERIALIZER_OPTION_ENCODING    => "UTF-8",   // xml encoding
 *       XML_SERIALIZER_OPTION_ROOT        => 'rdf:RDF',// root tag
 *    );
 *    $convert = new BaseModelXML($options);
 *    $xml = $convert->serialize($data);
 *    file_put_contents("temp.xml", $xml);
 *    </code>
 *
 *    You will get a complete XML document
 *
 * 2. Create an array from an XML string
 *    <code>
 *      $xmlArray = $convert->unserialize(file_get_contents("temp.xml"));
 *    </code>
 *
 * @package   BaseModelXML
 * @author    ()
 * @copyright sina
 */
class BaseModelXML {

    /**
     * list of all available serialization options
     *
     * @access private
     * @var    array
     */
    protected $_knownSerializerOptions = array(
        XML_SERIALIZER_OPTION_VERSION,
        XML_SERIALIZER_OPTION_LINEBREAK,
        XML_SERIALIZER_OPTION_INDENTCHAR,
        XML_SERIALIZER_OPTION_INDENTLEN,
        XML_SERIALIZER_OPTION_ENCODING,
        XML_SERIALIZER_OPTION_ROOT,
    );
    /**
     * default options for the serialization
     *
     * @access private
     * @var    array
     */
    protected $_defaultSerializerOptions = array(
        //XML Version
        XML_SERIALIZER_OPTION_VERSION => "1.0",
        //XML Line Break
        XML_SERIALIZER_OPTION_LINEBREAK => "\n",
        //XML default indent char
        XML_SERIALIZER_OPTION_INDENTCHAR => " ",
        //XML number of indents per level
        XML_SERIALIZER_OPTION_INDENTLEN => 4,
        //XML Encoding
        XML_SERIALIZER_OPTION_ENCODING => "UTF-8",
        //XML root
        XML_SERIALIZER_OPTION_ROOT => "root",
    );
    /**
     * current options for the serialization
     *
     * @access public
     * @var    array
     */
    protected $serializerOptions = array();

    /**
     * Class constructor
     *
     * @param mixed $serializerOptions array containing options for the serialization
     *
     * @access public
     * @return void
     */
    public function __construct($serializerOptions = null) {
        if (is_array($serializerOptions)) {
            $this->serializerOptions = array_merge($this->_defaultSerializerOptions, $serializerOptions);
        } else {
            $this->serializerOptions = $this->_defaultSerializerOptions;
        }
    }

    /**
     * Reset all options to default options
     *
     * @param void
     *
     * @return void
     * @access public
     */
    public function resetSerializerOptions() {
        $this->serializerOptions = $this->_defaultSerializerOptions;
    }

    /**
     * Set an option
     *
     * You can use this method if you do not want
     * to set all options in the constructor.
     *
     * @param string $name  option name
     * @param mixed  $value option value
     *
     * @return void
     * @access public
     */
    public function setSerializerOption($name, $value) {
        $this->serializerOptions[$name] = $value;
    }

    /**
     * Sets several options at once
     *
     * You can use this method if you do not want
     * to set all options in the constructor.
     *
     * @param array $serializerOptions options array
     *
     * @return void
     * @access public
     */
    public function setSerializerOptions($serializerOptions) {
        $this->serializerOptions = array_merge($this->serializerOptions, $serializerOptions);
    }

    /**
     * Get XML Head
     *
     * @param void
     *
     * @return void
     * @access public
     */
    protected function getXMLHead() {
        return $xmlHead = '<?xml version="' . $this->serializerOptions[XML_SERIALIZER_OPTION_VERSION] .
        '" encoding="' . $this->serializerOptions[XML_SERIALIZER_OPTION_ENCODING] .
        '"?>' . $this->serializerOptions[XML_SERIALIZER_OPTION_LINEBREAK];
    }

    /**
     * Create an indent string
     *
     * @param int $num - number of indents
     *
     * @return str
     * @access Private
     */
    protected function indent($num) {
        $str = '';
        $indentLen = $this->serializerOptions[XML_SERIALIZER_OPTION_INDENTLEN];
        $indentChar = $this->serializerOptions[XML_SERIALIZER_OPTION_INDENTCHAR];
        $indentTimes = $num * $indentLen;

        for ($i = 0; $i <= $indentTimes; $i++) {
            $str .= $indentChar;
        }

        return $str;
    }

    /**
     * String key to XML Safe
     *
     * @param string $str
     *
     * @return string
     * @access public
     */
    protected static function strKeyToXMLSafe($str) {
        $str = str_replace(array('<', '>', '&', "'", '"'), array('&lt;', '&gt;', '&amp;', '&apos;', '&quot;'), $str);
        $str = preg_match("/^\d+/", $str) ? "_{$str}" : $str;
        return $str;
    }

    /**
     * String value to XML Safe
     *
     * @param string $str
     *
     * @return string
     * @access public
     */
    protected static function strValueToXMLSafe($str) {
        if ((strpos($str, "<") === false) && (strpos($str, ">") === false) &&
            (strpos($str, "&") === false) && (strpos($str, "'") === false) &&
            (strpos($str, '"') === false)) {
            return $str;
        } else {
            // str contains a invalid char
            // A CDATA section starts with "<![CDATA[" and ends with "]]>"
            $str = "<![CDATA[" . $str . "]]>";
            return $str;
        }
    }

    /**
     * array2Xml
     * convert array to xml
     *
     * @param array $arr
     * @param int $lvl
     * @param int $prev_lvl - will be one less than lvl if this is the first row, else it'll be the same
     *
     * @return string
     * @access private
     */
    protected function array2Xml($arr, $lvl = 0, $prev_lvl = 0) {
        $lineBreakChar = $this->serializerOptions[XML_SERIALIZER_OPTION_LINEBREAK];
        $str = '';
        foreach ($arr as $key => $value) {
            $xmlStr = "";
            $key = $this->strKeyToXMLSafe($key);
            list($key) = explode(' ', $key);
            if ($prev_lvl != $lvl) {
                $xmlStr .= $lineBreakChar;//first node should start at a new line
                $prev_lvl = $lvl;
            }
            if (!is_array($value)) {
                $xmlTag = sprintf("<%s>%s</%s>", $key, $this->strValueToXMLSafe($value), $key);// the node is a leaf,  so xml tag should be <key><value></key>
                $xmlStr .= $this->indent($lvl) . $xmlTag . $lineBreakChar;
            } else {
                $associative = count(array_diff(array_keys($value), array_keys(array_keys($value))));
                if ($associative) {
                    // the node is a assoc_array, travel the value recursively
                    $xmlStr .= $this->indent($lvl) . '<' . $key . '>';
                    $xmlStr .= $this->array2Xml($value, $lvl + 1, $lvl);
                    $xmlStr .= $this->indent($lvl) . '</' . $key . '>' . $lineBreakChar;
                } else {
                    // the node is a index_array, travel the each node in value recursively
                    foreach ($value as $valueTag) {
                        if (is_array($valueTag)) {
                            $xmlStr .= $this->indent($lvl) . '<' . $key . '>';
                            $xmlStr .= $this->array2Xml($valueTag, $lvl + 1, $lvl);
                            $xmlStr .= $this->indent($lvl) . '</' . $key . '>' . $lineBreakChar;
                        } else {
                            $valueTag = sprintf("<%s>%s</%s>", $key, $this->strValueToXMLSafe($valueTag), $key);
                            $xmlStr .= $this->indent($lvl) . $valueTag . $lineBreakChar;
                        }
                    }
                }
            }
            $str .= $xmlStr;
        }
        return $str;
    }

    /**
     * xml2Array
     * convert SimpleXMLElement object to array
     *
     * @param SimpleXMLElement $xmlObject
     *
     * @return array
     * @access private
     */
    protected function xml2Array($xmlObject) {
        $arr = array();

        if ($xmlObject instanceof SimpleXMLElement) {
            //the parent object is diffrent with the child object
            if (!$xmlObject->children()) {
                $key = $xmlObject->getName();
                $value = (string) $xmlObject;
                $arr[$key] = $value;
                return $arr;
            }

            foreach ($xmlObject as $key => $value) {
                if ($value->children()) {
                    // the child is a SimpleXMLElement object, travel the value recursively
                    $arrayValue = $this->xml2Array($value);
                    if (empty($arrayValue)){
                        $arrayValue = (string) $value;// a tag with attributes alse have a children @attributes.
                    }
                } else {
                    // the node is a leaf. stop.
                    $arrayValue = (string) $value;
                }

                if (!isset($arr[$key])) {
                    $arr[$key] = $arrayValue;
                } else {
                    $bGenerateIndexArray = (!is_array($arr[$key])) ||
                    ($bAssociative = count(array_diff(array_keys($arr[$key]), array_keys(array_keys($arr[$key])))));
                    if ($bGenerateIndexArray) {
                        // if the same kay have been inserted into the array
                        // if the value is a array too! like
                        //[contact] => Array(
                        //              [name] => John Doe 姐八猪
                        //              [phone] => 123-456-5678
                        //)
                        // we have to generate a index array to place more pairs like (key,value)
                        $bak = $arr[$key];
                        unset($arr[$key]);
                        $arr[$key] = array();
                        $arr[$key][] = $bak;
                    }
                    $arr[$key][] = $arrayValue;
                }
            }
        }
        return $arr;
    }

    /**
     * unserialize
     * convert xml to array
     *
     * @param string $xmlString
     *
     * @return array
     * @access public
     */
    public function decode($xmlString) {
        $xmlObject = @simplexml_load_string($xmlString);
        if(!is_object($xmlObject)) {
            throw new BaseModelException('XML Parse Fail', 92000, 'xml_trace');
            return array();
        }
        $this->serializerOptions[XML_SERIALIZER_OPTION_ROOT] = $xmlObject->getName();
        return $xmlArray = $this->xml2Array($xmlObject);
    }

    /**
     * serialize
     * convert array to xml
     *
     * @param array $xmlArray
     *
     * @return string
     * @access public
     */
    public function encode($xmlArray) {
        if (!is_array($xmlArray)) {
            throw new BaseModelException('XML Fail: Not Array', 92000, 'xml_trace');
            return false;
        }
        $root = $this->serializerOptions[XML_SERIALIZER_OPTION_ROOT];
        $xmlString = "";
        $xmlString .= $this->getXmlHead();
        $xmlTag = $this->array2Xml($xmlArray, 1, 0);
        $xmlString .= "<{$root}>{$xmlTag}</{$root}>";
        $xmlObject = @simplexml_load_string($xmlString);
        if(!is_object($xmlObject)) {
            throw new BaseModelException('XML Encode Fail', 92000, 'xml_trace');
            return false;
        }
        return $xmlString;
    }
}
