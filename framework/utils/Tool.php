<?php
class Tool {
	public static function now_date() {
		return date ( "Y-m-d H:i:s" );
	}
	public static function now_date_ymd() {
		return date ( "Ymd" );
	}
	public static function now_date_y_m_d() {
		return date ( "Y-m-d" );
	}
	public static function add_hours_ymd_his($start, $hours) {
		$ret_date = date ( "Y-m-d H:i:s", strtotime ( "+" . $hours . " hour", strtotime ( $start ) ) );
		return $ret_date;
	}
	public static function get_session_val($userId) {
		$key = "1@%&2";
		return base64_encode ( $userId . time () . $key );
	}
	
	public static function yx_mkdir($dir) {
		mkdir ( $dir, 0777, true );
	}
	public static function log_get_file_pre_path() {
		$dir = "";
		global $root;
		$datetime = date ( "Ymd" );
		$dir = $root . "log/" . $datetime;
		if (! is_dir ( $dir )) {
			Tool::yx_mkdir ( $dir, 0777, true );
		}
		return $dir;
	}
	public static function log($txt) {
		$dir = Tool::log_get_file_pre_path ();
		$userid = Request::getCookie ( "cookie_user_id" ) ? Request::getCookie ( "cookie_user_id" ) : Request::getInt ( "u" ) ;
		$file = $dir . "/_" . $userid . ".txt";
		$handle = fopen ( $file, "aw" );
		fwrite ( $handle, "[" . date ( "Y-m-d H:i:s" ) . "] " . "[" . Tool::get_real_ip () . "] " . "[" . Request::getCookie ( "cookie_user_id" ) . "] " . $txt . "\n" );
		fclose ( $handle );
	}
	public static function get_real_ip() {
		$ip = false;
		if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
			$ip = $_SERVER ["HTTP_CLIENT_IP"];
		}
		if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
			$ips = explode ( ", ", $_SERVER ['HTTP_X_FORWARDED_FOR'] );
			if ($ip) {
				array_unshift ( $ips, $ip );
				$ip = FALSE;
			}
			for($i = 0; $i < count ( $ips ); $i ++) {
				if (! preg_match ( "/^(10|172\.16|192\.168)\./", $ips [$i] )) {
					$ip = $ips [$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER ['REMOTE_ADDR']);
	}
	private static $logArr = array ();
	public static function log4j($name, $msg) {
		if (array_key_exists ( $name, self::$logArr )) {
			$logArr [$name]->info ( $msg );
			return;
		}
		self::$logArr [$name] = new Logger ( $name );
		self::$logArr [$name]->info ( $msg );
	}

	public static function img_save_path($filePath) {
		$root = getcwd () . "/";
		$retPath = $root . $filePath;
		return $retPath;
	}
	public static function put_err_log($log){
		echo $log;
	}
	public static function put_log($log){
		echo $log;
	}
}