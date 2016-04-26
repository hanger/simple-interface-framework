<?php
header ( "content-type:Application/json;charset=utf-8" );
$root = getcwd () . "/";
require_once $root . 'common.php';
require_once 'utils/Tool.php';
require_once 'utils/function.class.php';
require_once 'utils/request.impl.php';
require_once 'const/BaseConst.php';
require_once 'const/ActionConst.php';
// 注册E_ERROR 类型错误捕捉方法
register_shutdown_function ( 'check_for_fatal' );
set_error_handler ( 'errorHandler');
set_exception_handler('exception_handler');
// 设置默认时间
date_default_timezone_set ( BaseConst::TIME_ZONE );

function check_for_fatal() {
	$error = error_get_last ();
	if ($error ["type"] == E_ERROR)
		errorHandler ( $error ["type"], $error ["message"], $error ["file"], $error ["line"] );
}

$action = Request::getString ( BaseConst::ACTION );
$actions = array (
	ActionConst::ACTION_TEST => 'action/TestAction.php',
);

$action = strtolower ( Request::getString ( BaseConst::ACTION ) );
if (empty ( $action ) || ! array_key_exists ( $action, $actions )) {
	$res = array(
		BaseConst::RESULT => BaseConst::RESULT_FAIL,
		BaseConst::RESULT_CODE => BaseConst::CODE_NO_ACTION,
		BaseConst::SERVER_TIME => time(),
	);
	echo preg_replace('/:(-?\d+\.?\d*)([,}])/', ':"$1"$2', json_encode($res));
	exit ();
}
include_once ($actions [$action]);
$actionFile = explode ( "/", $actions [$action] );
$actionName = explode ( ".", array_pop ( $actionFile ) );

$actionInstance = new $actionName [0] ();
$actionInstance->execute ();