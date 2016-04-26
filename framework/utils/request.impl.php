<?php
/**
 * 基础http处理
 */
class Request {
	public static function addCookie($key, $val) {
		if (empty ( $key )) {
			return;
		}
		$_COOKIE [$key] = $val;
		setcookie ( $key, $val );
	}
	public static function getCookie($paramName) {
		$paramValue = "";
		if (isset ( $_COOKIE [$paramName] )) {
			$paramValue = $_COOKIE [$paramName];
		} else {
			$paramValue = self::getParam ( $paramName );
		}
		if (is_string ( $paramValue )) {
			$paramValue = trim ( $paramValue );
			if (! get_magic_quotes_gpc ()) {
				$paramValue = addslashes ( $paramValue );
			}
		}
		if (! empty ( $paramValue ) && is_array ( $paramValue )) {
			for($i = 0; $i < count ( $paramValue ); $i ++) {
				$paramValue [$i] = trim ( $paramValue [$i] );
				if (! get_magic_quotes_gpc ()) {
					$paramValue [$i] = addslashes ( $paramValue [$i] );
				}
			}
		}
		return $paramValue;
	}
	/* 参数获得 */
	public static function getParam($paramName) {
		$paramValue = "";
		if (isset ( $_POST [$paramName] )) {
			$paramValue = $_POST [$paramName];
		} else if (isset ( $_GET [$paramName] )) {
			$paramValue = $_GET [$paramName];
		} else if (isset ( $_REQUEST [$paramName] )) {
			$paramValue = $_REQUEST [$paramName];
		} else {
			if ($_REQUEST != null && array_key_exists ( $paramName, $_REQUEST )) {
				$paramValue = $_REQUEST [$paramName];
			}
		}
		
		if (is_string ( $paramValue )) {
			$paramValue = trim ( $paramValue );
			if (! get_magic_quotes_gpc ()) {
				$paramValue = addslashes ( $paramValue );
			}
		}
		
		if (! empty ( $paramValue ) && is_array ( $paramValue )) {
			for($i = 0; $i < count ( $paramValue ); $i ++) {
				$paramValue [$i] = trim ( $paramValue [$i] );
				if (! get_magic_quotes_gpc ()) {
					$paramValue [$i] = addslashes ( $paramValue [$i] );
				}
			}
		}
		
		return $paramValue;
	}
	
	/**
	 *
	 * @param type $paramName        	
	 * @param type $default
	 *        	如果没有传参 获取的默认值
	 * @return type获取字符串型参数
	 */
	public static function getString($paramName, $default = "") {
		$paramValue = "";
		if (isset ( $_POST [$paramName] )) {
			$paramValue = $_POST [$paramName];
		} else if (isset ( $_GET [$paramName] )) {
			$paramValue = $_GET [$paramName];
		}
		if (is_string ( $paramValue )) {
			$paramValue = trim ( $paramValue );
			$paramValue = stripslashes ( $paramValue );
		}
		if ("" === $paramValue) {
			$paramValue = $default;
		}
		return strval ( $paramValue );
	}
	/**
	 *
	 * @param type $paramName        	
	 * @param type $default
	 *        	默认值
	 * @return type获取整型参数
	 */
	public static function getInt($paramName, $default = 0) {
		$paramValue = 0;
		if (isset ( $_POST [$paramName] )) {
			$paramValue = $_POST [$paramName];
		} elseif (isset ( $_GET [$paramName] )) {
			$paramValue = $_GET [$paramName];
		}
		if (is_numeric ( $paramValue )) {
			$paramValue = intval ( $paramValue );
		}
		if (0 === $paramValue) {
			$paramValue = $default;
		}
		return intval ( $paramValue );
	}
	
	/**
	 *
	 * @param type $paramName        	
	 * @param type $default
	 *        	默认值
	 * @return type获取浮点型参数
	 */
	public static function getFloat($paramName, $default = 0) {
		$paramValue = 0;
		if (isset ( $_POST [$paramName] )) {
			$paramValue = $_POST [$paramName];
		} elseif (isset ( $_GET [$paramName] )) {
			$paramValue = $_GET [$paramName];
		}
		if (is_float ( $paramValue )) {
			$paramValue = floatval ( $paramValue );
		}
		if (0 == $paramValue) {
			$paramValue = $default;
		}
		return $paramValue;
	}
	
	/**
	 *
	 * @param type $paramName        	
	 * @param type $default
	 *        	默认值
	 * @return type获取双精度型参数
	 */
	public static function getDouble($paramName, $default = 0) {
		$paramValue = 0;
		if (isset ( $_POST [$paramName] )) {
			$paramValue = $_POST [$paramName];
		} elseif (isset ( $_GET [$paramName] )) {
			$paramValue = $_GET [$paramName];
		}
		if (is_double ( $paramValue )) {
			$paramValue = doubleval ( $paramValue );
		}
		if (0 == $paramValue) {
			$paramValue = $default;
		}
		return $paramValue;
	}
	/**
	 * 获取传入的json串参数并转换为数组
	 *
	 * @param [type] $paramName
	 *        	[description]
	 * @param array $default
	 *        	[description]
	 * @return [type] [description]
	 */
	public static function getJson2Array($paramName, $default = array()) {
		$paramValue = array ();
		if (isset ( $_POST [$paramName] )) {
			$paramValue = json_decode ( $_POST [$paramName], true );
		} elseif (isset ( $_GET [$paramName] )) {
			$paramValue = json_decode ( $_GET [$paramName], true );
		}
		if (null === $paramValue || ! is_array ( $paramValue )) {
			$paramValue = $default;
		}
		return $paramValue;
	}
	
	/**
	 * 页面跳转
	 *
	 * @param String $url,跳转的地址        	
	 * @param String $alert,JS
	 *        	alert文字提示
	 */
	public static function go2($url, $alert = null) {
		echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
			<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n
			<meta http-equiv=\"refresh\" content=\"0;URL=" . $url . "\">";
		if ($alert) {
			echo "<script>alert('" . $alert . "')</script>";
		}
		exit ();
	}
	
	/**
	 * 表单提交错误,返回
	 *
	 * @param $reason 错误提示        	
	 * @return void
	 */
	public static function getHistoryBack($reason) {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n
                        <html xmlns=\"http://www.w3.org/1999/xhtml\">\n
                        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n<body>";
		echo "<SCRIPT LANGUAGE='JavaScript'>alert('" . $reason . "');window.history.go(-1);</SCRIPT>";
		echo "</body></html>";
		exit ();
	}
	
	/**
	 * UTF-8格式截取字符串长度
	 *
	 * @param String $string
	 *        	要截取的字符串
	 * @param int $length
	 *        	截取长度
	 * @param String $etc
	 *        	省略部分标表示
	 * @return String
	 */
	public static function truncateUtf8($string, $length = 80, $etc = "") {
		if ($length == 0)
			return null;
		$newlength = 0;
		if (strlen ( $string ) > $length) {
			for($i = 0; $i < $length; $i ++) {
				$newlength ++;
				$a = base_convert ( ord ( $string {$i} ), 10, 2 );
				$a = substr ( '00000000' . $a, - 8 );
				if (substr ( $a, 0, 1 ) == 0) {
					continue;
				} elseif (substr ( $a, 0, 3 ) == 110) {
					$newlength ++;
					$i ++;
				} elseif (substr ( $a, 0, 4 ) == 1110) {
					$newlength += 2;
					$length ++;
					$i ++;
				} else {
					$newlength --;
				}
			}
			$length = $newlength;
			return substr ( $string, 0, $length ) . $etc;
		} else {
			return $string;
		}
	}
	
	/**
	 * UTF8编码格式监测字符串长度
	 *
	 * @param
	 *        	$str,检测的字符串
	 * @param
	 *        	$min,最小长度
	 * @param
	 *        	$max,最大长度
	 * @return boolean
	 */
	public static function checkUtf8Length($string, $min = null, $max = null) {
		if ((null == $min) && (null == $max)) {
			return true;
		}
		if ($max <= $min) {
			return false;
		}
		
		$length = strlen ( $string );
		$newlength = 0;
		for($i = 0; $i < $length; $i ++) {
			$newlength ++;
			$a = base_convert ( ord ( $string {$i} ), 10, 2 );
			$a = substr ( '00000000' . $a, - 8 );
			if (substr ( $a, 0, 1 ) == 0) {
				continue;
			} elseif (substr ( $a, 0, 3 ) == 110) {
				$newlength ++;
				$i ++;
			} elseif (substr ( $a, 0, 4 ) == 1110) {
				$newlength ++;
				$i ++;
			} else {
				$newlength --;
			}
		}
		
		if ($min > $newlength) {
			return false;
		}
		if ($max && ($max < $newlength)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 获得随即字符串
	 *
	 * @param int $size
	 *        	字符串长度
	 * @return String random code in $size
	 */
	public static function randomCode($size) {
		$charList = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";
		$randomCode = "";
		for($i = 0; $i < $size; $i ++) {
			$randomCode .= $charList [mt_rand ( 0, strlen ( $charList ) - 1 )];
		}
		
		return $randomCode;
	}
	
	/**
	 * 检查邮箱是否合法
	 *
	 * @return boolean
	 */
	public static function checkEmail($email) {
		if (! $email) {
			return false;
		}
		if (! preg_match ( "/^\w+((-\w+)|(.\w+))+@[a-zA-Z0-9_-]+((\.[a-zA-Z0-9_-]{2,3}){1,2})+$/", $email )) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 错误提示
	 */
	public static function systemAlert($message, $link = "") {
		if (! $link) {
			if (! $link = self::getBackUrl ()) {
				$link = "index.php";
			}
		}
		include_once ("template/system_alert.html");
		exit ();
	}
	
	/**
	 * 获得返回地址
	 *
	 * @return string $backUrl
	 */
	public static function getBackUrl() {
		$backUrl = "";
		if (isset ( $_POST ["backurl"] )) {
			$backUrl = $_POST ["backurl"];
		} elseif (isset ( $_GET ["backurl"] )) {
			$backUrl = $_GET ["backurl"];
		} elseif (isset ( $_SERVER ["HTTP_REFERER"] )) {
			$backUrl = $_SERVER ["HTTP_REFERER"];
		}
		
		return $backUrl;
	}
}