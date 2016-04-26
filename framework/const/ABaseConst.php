<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/22
 * Time: 下午2:17
 */

abstract class ABaseConst {
    const ACTION = 'a';
    const CMD = 'cmd';
    const RESULT = 'result';
    const RESULT_CODE = 'code';
    const MESSAGE = 'message';
    const DATA = 'data';
    const SERVER_TIME = 'server_time';


    /**
     * 接口通信成功或失败
     */
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 0;
    /**
     * 错误码
     */
    const CODE_NO_ACTION = 10000;   //服务不存在
    const CODE_NO_CMD = 10001;  //接口错误
}