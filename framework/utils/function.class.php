<?php
/**
 * 基础工具方法
 * Created by PhpStorm.
 * User: hange
 * Date: 15/11/26
 * Time: 下午2:31
 */

/**
 * 根据数量生成占位符
 * @param  [type] $num [description]
 * @return [type]      [description]
 */
function createPlaceHolder($num)
{
	if ($num <= 0) {
		return '';
	}
	$str = str_repeat('?,',$num);
	return substr($str,0,-1);
}
/**
 * 根据数组返回带有占位符的sql字串，可用于insert语句构建
 * 
 * @param
 *        	$array
 * @return string
 * @throws Exception
 */
function createValuePlaceHolderByArray($array) {
	if (! is_array ( $array ) || empty ( $array ))
		throw new Exception ( 'place holder create source data exception' );
	$keys = array_keys ( $array );
	return '(' . implode ( ',', $keys ) . ') VALUES(' . substr ( str_repeat ( "?,", count ( $keys ) ), 0, - 1 ) . ')';
}
/**
 * 根据数组返回带有占位符的sql字串，可用于update语句构建
 * 
 * @param
 *        	$array
 * @return string
 * @throws Exception
 */
function createUpdatePlaceHolderByArray($array) {
	if (! is_array ( $array ) || empty ( $array ))
		throw new Exception ( 'place holder create source data exception' );
	$keys = array_keys ( $array );
	return implode ( ' = ?,', $keys ) . ' = ? ';
}

/**
 * 根据数组创建where条件语句，可用于update、select、delete等操作
 * 
 * @param
 *        	$array
 * @return string
 * @throws Exception
 */
function createWherePlaceHolderByArray($array) {
	if (! is_array ( $array ) || empty ( $array ))
		throw new Exception ( 'place holder create source data exception' );
	$where = '';
	for($i = 0; $i < count ( $array ); ++ $i) {
		$term = $array [$i];
		$where .= ' ' . $term ['logic'] . ' ' . $term ['property'] . ' ' . $term ['operator'] . ' ?';
	}
	return $where;
}
/**
 * 异常处理
 * @param Exception $e
 */
function exception_handler(Exception $e){
	$exception = 'exception no:'. $e->getCode() . "<br />" .
		'exception str :' .$e->getMessage() . "<br />".
		'exception file:' .$e->getFile() . "<br />".
		'exception line:'. $e->getLine() . "<br />";
	Tool::put_err_log($exception . ", REQUEST : " . json_encode ( $_REQUEST ) . ", DEBUG : " . json_encode ( debug_backtrace () ));
	//todo 此处异常处理可以接入微信
//	EmailAPI::getInstance ()->sendMail ( "1480637355@qq.com", "服务器错误", $exception );
	$ret_val = array ();
	$ret_val [BaseConst::RET_VAL] = BaseConst::RET_VAL_STOP_SERVER;
	$ret_val [BaseConst::STOP_SERVER_MSG] = base64_encode ( $exception );
	$ret_val [BaseConst::START_SERVER_TIME] = BaseConst::START_SERVER_TIME_VAL;
	$ret_val [BaseConst::NOW_SERVER_TIME] = now_date ();
	$ret_json = json_encode ( $ret_val );
	echo gzencode ( $ret_json, 9 );
	return true;
}

function errorHandler($errno, $errstr, $errfile, $errline) {
	if (E_ERROR === $errno){
		$err = "PHP Fatal Error: " . $errstr . "<br>" . $errfile . "<br>" . $errline;
		Tool::put_err_log($err);
		$ret_val = array ();
		$ret_val ['ret_val'] = 10000;
		$ret_val ['stop_server_msg'] = base64_encode ( $err );
		$ret_val ['now_server_time'] = date ( "Y-m-d H:i:s" );
		$ret_json = json_encode ( $ret_val );
		echo gzencode ( $ret_json, 9 );
		exit();
	} else if (E_WARNING === $errno) {
		$err = "PHP Warning: " . $errstr . "<br>" . $errfile . "<br>" . $errline;
		Tool::put_err_log ( $err . ", REQUEST : " . json_encode ( $_REQUEST ) . ", DEBUG : " . json_encode ( debug_backtrace () ) );
		// echo $err;
	} else if (E_NOTICE === $errno) {
		$err = "PHP Notic: " . $errstr . "<br>" . $errfile . "<br>" . $errline;
		Tool::put_err_log ( $err . ", REQUEST : " . json_encode ( $_REQUEST ) . ", DEBUG : " . json_encode ( debug_backtrace () ) );
		// echo $err;
	} else {
		$err = "PHP Notic: " . $errstr . "<br>" . $errfile . "<br>" . $errline;
		Tool::put_err_log ( $err . ", REQUEST : " . json_encode ( $_REQUEST ) . ", DEBUG : " . json_encode ( debug_backtrace () ) );
	}
	//以下可以在调试时输出，生产环境需要屏蔽
	$ret_val = array ();
	$ret_val ['ret_val'] = 10000;
	$ret_val ['stop_server_msg'] = base64_encode ( $err );
	$ret_val ['now_server_time'] = date ( "Y-m-d H:i:s" );
	$ret_json = json_encode ( $ret_val );
	echo gzencode ( $ret_json, 9 );
	return true;
}