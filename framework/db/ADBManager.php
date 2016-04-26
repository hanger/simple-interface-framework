<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 15/11/23
 * Time: 下午2:57
 */
require_once 'IDBManager.php';
abstract class ADBManager implements IDBManager{
    abstract function mysql_fetch_array_assoc($result);
    abstract function is_no_data($result);
    abstract function query_data_result($sql,$arr);
    abstract function query_data_arr($sql,$arr);
    abstract function query_fetch_result($result);
    abstract function query_upd($sql,$arr);
    abstract function last_insert_id($sql);

    abstract function begin();
    abstract function commit();
    abstract function rollback();
    abstract function lock($table);
    abstract function unlock();
    abstract function exe_upd($sql);
    abstract function transaction($sql_arr);
}