<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/26
 * Time: 上午9:37
 */

class CacheConst {
    const KEY_TEST = 'test_';

    public static function getKey($keyPre,$keyLast){
        return $keyPre . '_' . $keyLast;
    }
}