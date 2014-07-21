<?php
/*
 * 加解密基类
 * Author: Hongbo Yin<hongbo4@staff.sina.com.cn>
 */
class BaseModelEncrypt
{
	//对称加密盐
	private $salt = '';

	function __construct(){
		$this->padding	= EncryptConfig::ENCRYPT_CONF_PADDING;
		$this->pub_key	= EncryptConfig::ENCRYPT_CONF_PUBLIC_KEY;
		$this->pri_key	= EncryptConfig::ENCRYPT_CONF_PRIVATE_KEY;
		$this->pri_dkey	= EncryptConfig::ENCRYPT_CONF_PRIVATE_DES_KEY;
		$this->pri_pass	= EncryptConfig::ENCRYPT_CONF_PRI_PASS;

		$this->sym_cip	= EncryptConfig::ENCRYPT_CONF_MCRYPT_CIPHER;
		$this->sym_key	= EncryptConfig::ENCRYPT_CONF_SYMMETRY_KEY;
		$this->sym_mode	= EncryptConfig::ENCRYPT_CONF_MCRYPT_MODE;
		$this->sym_iv	= EncryptConfig::ENCRYPT_CONF_SYMMETRY_IV;

		$this->reg_key	= EncryptConfig::ENCRYPT_CONF_REGULATE_KEY;
		$this->reg_type	= EncryptConfig::ENCRYPT_CONF_REGULATE_TYPE;
	}

	/*
	 * RSA 私钥加密函数
	 */
	public function rsa_private_encode($source,$bin = false, $des = false){
		return $this->rsa_private($source,"encode",$bin,$des);
	}

	/*
	 * RSA 私钥解密函数
	 */
	public function rsa_private_decode($source,$bin = false, $des = false){
		return $this->rsa_private($source,"decode",$bin,$des);
	}

	/*
	 * RSA 公钥加密函数
	 */
	public function rsa_public_encode($source,$bin = false){
		return $this->rsa_public($source, 'encode', $bin);
	}

	/*
	 * RSA 公钥解密函数
	 */
	public function rsa_public_decode($source,$bin = false){
		return $this->rsa_public($source, 'decode', $bin);
	}

	/*
	 * RSA private function
	 * @param	$source string 加解密的文本或binary
	 * @param	$bin bool 是否返回/原数据是否是 binary数据
	 * @param	$type encode or decode
	 * @param	$des 是否启用DES加密的密钥，需要对应的密钥的密码
	 */
	private function rsa_private($source, $type = 'encode',$bin = false,$des = false) {
		# $private_key = openssl_pkey_get_private($private_key_file, $password);

		if($des){
			$private_key	= openssl_pkey_get_private(json_decode($this->pri_dkey),$this->pri_pass);
		}else{
			$private_key	= openssl_pkey_get_private(json_decode($this->pri_key));
		}
		if ($private_key == false) {
			throw new Exception("The private key {$private_key_content} is not invalid");
			return false;
		}
		# print_r(openssl_pkey_get_details($private_key));
		$result	= '';
		if($type == 'decode'){
			if($bin == false){
				$pack	= pack("H*", $source);
			}else{
				$pack	= $source;
			}

			if (!openssl_private_decrypt($pack, $result, $private_key, $this->padding)) {
				while ($msg = openssl_error_string()) {
					$errmsg .= $msg. "\n";
				}
				throw new Exception($errmsg);
				return false;
			}

			if ($this->padding == OPENSSL_NO_PADDING) {
				return rtrim(strrev($result), "/0");
			} else {
				return urldecode($result);
			}
		}else{
			if (!openssl_private_encrypt($source, $result, $private_key, $this->padding)) {
				while ($msg = openssl_error_string()) {
					$errmsg .= $msg. "\n";
				}
				throw new Exception($errmsg);
				return false;
			}
			if($bin == false){
				return bin2hex($result);
			}
			return $result;
		}
	}
	/*
	 * RSA public function
	 * @param	$source string or binary 要加解密的字符串或binary data
	 * @param	$bin bool 是否返回/原数据是否是 binary数据
	 * @param	$type string encode or decode
	 */
	private function rsa_public($source, $type = 'encode', $bin = false) {

		$public_key		= openssl_pkey_get_public(json_decode($this->pub_key));
		if ($public_key == false) {
			echo "public key is empty";
			return false;
		}
		#print_r(openssl_pkey_get_details($public_key));

		$result = '';
		if($type == 'encode'){
			if (!openssl_public_encrypt($source, $result, $public_key, $this->padding)) {
				while ($msg = openssl_error_string())
					echo $msg . "<br />\n";
				return false;
			}
			if($bin == false){
				$unpack	= unpack("H*",$result);
				return $unpack[1];
			}
			return $result;
		}else{
			if($bin == false){
				$source	= pack("H*",$source);
			}
			if (!openssl_public_decrypt($source, $result, $public_key, $this->padding)) {
				while ($msg = openssl_error_string())
					echo $msg . "<br />\n";
				return false;
			}
			return $result;
		}
	}

	/*
	 * 实现对称加密。默认是FLOWFISH加密
	 * @param $data string 要加密的字符串 
	 */
	public function Symmetry_encrypt($source){
		if(!function_exists("mcrypt_encrypt")){
			die("AES need mcrypt library");
		}
		$encrypted_string	= bin2hex(mcrypt_encrypt($this->sym_cip, $this->sym_key, $source, $this->sym_mode,$this->sym_iv));
		return $encrypted_string;
	}

	/*
	 * 实现对称加密的解密。默认是FLOWFISH加密
	 * @param $source string 要解密的字符串 
	 */
	public function Symmetry_decrypt($source){
		if(!function_exists("mcrypt_decrypt")){
			die("AES need mcrypt library");
		}
		$decrypted_string	= mcrypt_decrypt($this->sym_cip, $this->sym_key, pack("H*",$source),$this->sym_mode, $this->sym_iv);
		return trim($decrypted_string);
	}

	/*
	 * 生成随机IV向量
	 */
	public function genRandIv(){
		$iv_size	= mcrypt_get_iv_size($this->sym_cip, $this->sym_mode);
		$iv		= mcrypt_create_iv($iv_size, MCRYPT_RAND);
		return bin2hex($iv);
	}

	/*
	 * 不可逆加密算法，默认采用sha256方式。
	 * @param string $uid 个性盐，目前采用UID
	 * @param string $source 要加密的信息
	 * @return 返回加密的后信息，返回长度视加密算法而定！
	 */
	public function sina_encryptV1( $source, $uid = 1234567890){
		if(!in_array($this->reg_type, hash_algos(), true)){
			return -1;
		}
		$this->genSalt($uid);
		$salt	= $this->hashWithSalt($this->reg_key,$this->reg_type); 
		$result	= $salt . sha1($source) . sha1($this->reg_key); 
		return hash($this->reg_type, $result,$bin);

	}

	/*
	 * HASH盐
         */
	private function hashWithSalt($original_string, $hash_algo = 'sha256'){
		return hash_hmac($hash_algo, $original_string, $this->salt);
	}

	/*
	 * 测试盐
         */
	private function checkHashDigest($hash_digest, $original_string, $hash_algo = 'sha256'){
		return $hash_digest === hash_hmac($hash_algo, $original_string, $this->salt);
	}

	/*
	 * 生成盐
         */
	private function genSalt($uid){
		$SALT_FACTOR = 'SINA_2f/NPp9}Z/C:tA~T`e.%l"I[0Ya}-Hp^$MziDL:gy=5*\vl8|X{e~NpE":6J`(dt';
		$this->salt = substr(sha1(substr($uid, 0, 3) . $SALT_FACTOR . substr($uid, 3)), 4, 24);
	}

	public function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

?>
