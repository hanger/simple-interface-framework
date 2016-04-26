<?php
// header ( "Content-Type: text/html; charset=utf-8" );
// putenv ( "TZ=Asia/Shanghai" );
error_reporting ( 0 );
// session_start ();
define ( 'S_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );

$app_path = realpath ( dirname ( __FILE__ ) );
$framework_path = dirname ( $app_path ) . DIRECTORY_SEPARATOR . "framework/";
$includePaths = $framework_path . PATH_SEPARATOR . $app_path . PATH_SEPARATOR . get_include_path ();
set_include_path ( $includePaths );

?>
