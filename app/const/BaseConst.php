<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/20
 * Time: 下午2:33
 */

require_once 'const/ABaseConst.php';
class BaseConst extends ABaseConst{
    const TIME_ZONE = 'PRC';

    /**
     * 生成where条件的参数构造
     */
    const WHERE_LOGIC = 'logic';
    const WHERE_PROPERTY = 'property';
    const WHERE_OPERATOR = 'operator';
}