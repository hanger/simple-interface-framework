<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 15/11/23
 * Time: 下午2:54
 */
require_once 'ADBManager.php';
require_once 'PDODBManager.php';
require_once 'MysqliDBManager.php';
require_once 'const/SettingConst.php';
class DBManager extends ADBManager{
    private $conn = null;
    private static $instance = null;
    //初始化数据库连接
    private function __construct($server){
        //生成数据库连接对象实例
        if(SettingConst::USE_PDO)
            $this->conn = new PDODBManager($server);
        else
            $this->conn = new MysqliDBManager($server);
    }

    /**
     * singleton 获取数据库连接对象实例
     * @param $server
     * @return DBManager|null
     */
    public static function getConnection($server = SettingConst::DEF_DB_SERVER){
        if(null == self::$instance)
            self::$instance = new DBManager($server);
        return self::$instance;
    }

    function query_data($sql,$arr) {
        $ret = null;
        $result = $this->conn->query_data_result ( $sql ,$arr);
        if (! $this->conn->is_no_data ( $result )) {
            $ret = $this->conn->mysql_fetch_array_assoc ( $result );
        }
        return $ret;
    }

    /**
     * 返回带有KEY值的数据集
     *
     * @param string $sql
     * @param string $key
     *        	如果user_id作为KEY，那么就传入"user_id"
     */
     function query_data_arr_key($sql,$arr, $key) {
        $ret = array ();
        $result = $this->conn->query_data_result ( $sql,$arr );
        if (! $this->conn->is_no_data ( $result )) {
            $data = $this->conn->mysql_fetch_array_assoc ( $result );
            while ( $data ) {
                $ret [strval ( $data [$key] )] = $data;
                $data = $this->conn->mysql_fetch_array_assoc ( $result );
            }
        }
        return $ret;
    }

    /**
     * 返回一条数据
     *
     * @param string $sql
     * @param string $data_json_key
     *        	需要进行json_decode的数据key,可以是array
     * @param string|array $data_json_fields
     *        	如果赋值，就获取指定的字段
     * @return Ambigous <NULL, mixed, multitype:>
     */
    function query_data_data_json($sql,$arr, $data_json_key, $data_json_fields = false) {
        $ret = null;
        $result = $this->conn->query_data_result ( $sql,$arr );
        if (! $this->conn->is_no_data ( $result )) {
            $ret = $this->conn->mysql_fetch_array_assoc ( $result );
            if (is_array ( $data_json_key )) {
                foreach ( $data_json_key as $tmp_json_key ) {
                    if ($data_json_fields) {
                        $data_json_arr = json_decode ( $ret [$tmp_json_key], true );
                        if ($data_json_arr != null) {
                            $tmp_arr = array ();
                            foreach ( $data_json_fields as $field ) {
                                if (array_key_exists ( $field, $data_json_arr )) {
                                    $tmp_arr [$field] = $data_json_arr [$field];
                                }
                            }
                            $ret [$tmp_json_key] = $tmp_arr;
                        }
                    } else {
                        $ret [$tmp_json_key] = json_decode ( $ret [$tmp_json_key], true );
                    }
                }
            } else {
                if ($data_json_fields) {
                    $data_json_arr = json_decode ( $ret [$data_json_key], true );
                    if ($data_json_arr != null) {
                        $tmp_arr = array ();
                        foreach ( $data_json_fields as $field ) {
                            if (array_key_exists ( $field, $data_json_arr )) {
                                $tmp_arr [$field] = $data_json_arr [$field];
                            }
                        }
                        $ret [$data_json_key] = $tmp_arr;
                    }
                } else {
                    $ret [$data_json_key] = json_decode ( $ret [$data_json_key], true );
                }
            }
        }
        return $ret;
    }

    /**
     * 返回查询结果
     *
     * @param string $sql
     * @param string|array $data_json_key
     *        	需要进行json_decode的数据key,可以是array
     * @param string|array $data_json_fields
     *        	如果赋值，就获取指定的字段
     */
    function query_data_arr_data_json($sql,$arr, $data_json_key, $data_json_fields = false) {
        $ret = array ();
        $result = $this->conn->query_data_result ( $sql,$arr );
        if (! $this->conn->is_no_data ( $result )) {
            while ( $data = $this->conn->mysql_fetch_array_assoc ( $result ) ) {
                if (is_array ( $data_json_key )) {
                    foreach ( $data_json_key as $tmp_json_key ) {
                        if ($data_json_fields) {
                            $data_json_arr = json_decode ( $data [$tmp_json_key], true );
                            if ($data_json_arr != null) {
                                $tmp_arr = array ();
                                foreach ( $data_json_fields as $field ) {
                                    if (array_key_exists ( $field, $data_json_arr )) {
                                        $tmp_arr [$field] = $data_json_arr [$field];
                                    }
                                }
                                $data [$tmp_json_key] = $tmp_arr;
                            }
                        } else {
                            $data [$tmp_json_key] = json_decode ( $data [$tmp_json_key], true );
                        }
                    }
                } else {
                    if ($data_json_fields) {
                        $data_json_arr = json_decode ( $data [$data_json_key], true );
                        if ($data_json_arr != null) {
                            $tmp_arr = array ();
                            foreach ( $data_json_fields as $field ) {
                                if (array_key_exists ( $field, $data_json_arr )) {
                                    $tmp_arr [$field] = $data_json_arr [$field];
                                }
                            }
                            $data [$data_json_key] = $tmp_arr;
                        }
                    } else {
                        $data [$data_json_key] = json_decode ( $data [$data_json_key], true );
                    }
                }
                $ret [] = $data;
            }
        }
        return $ret;
    }

    /**
     * 返回查询结果
     *
     * @param
     *        	$sql
     * @param string $key
     *        	返回结果的key值
     * @param string|array $data_json_key
     *        	需要进行json_decode的数据key,可以是array
     * @param string|array $data_json_fields
     *        	如果赋值，就获取指定的字段
     */
    function query_data_arr_key_data_json($sql,$arr, $key, $data_json_key, $data_json_fields = false) {
        $result = $this->conn->query_data_result ( $sql,$arr );
        $ret = array ();
        if (! $this->conn->is_no_data ( $result )) {
            while ( $data = $this->conn->mysql_fetch_array_assoc( $result ) ) {
                if (is_array ( $data_json_key )) {
                    foreach ( $data_json_key as $tmp_json_key ) {
                        if ($data_json_fields) {
                            $data_json_arr = json_decode ( $data [$tmp_json_key], true );
                            if ($data_json_arr != null) {
                                $tmp_arr = array ();
                                foreach ( $data_json_fields as $field ) {
                                    if (array_key_exists ( $field, $data_json_arr )) {
                                        $tmp_arr [$field] = $data_json_arr [$field];
                                    }
                                }
                                $data [$tmp_json_key] = $tmp_arr;
                            }
                        } else {
                            $data [$tmp_json_key] = json_decode ( $data [$tmp_json_key], true );
                        }
                    }
                } else {
                    if ($data_json_fields) {
                        $data_json_arr = json_decode ( $data [$data_json_key], true );
                        if ($data_json_arr != null) {
                            $tmp_arr = array ();
                            foreach ( $data_json_fields as $field ) {
                                if (array_key_exists ( $field, $data_json_arr )) {
                                    $tmp_arr [$field] = $data_json_arr [$field];
                                }
                            }
                            $data [$data_json_key] = $tmp_arr;
                        }
                    } else {
                        $data [$data_json_key] = json_decode ( $data [$data_json_key], true );
                    }
                }
                $ret [strval ( $data [$key] )] = $data;
            }
        }
        return $ret;
    }

    function mysql_fetch_array_assoc($result)
    {
        return $this->conn->mysql_fetch_array_assoc($result);
    }

    function is_no_data($result)
    {
        $this->conn->is_no_data($result);
    }

    function query_data_result($sql,$arr)
    {
        return $this->conn->query_data_result($sql,$arr);
    }

    function query_data_arr($sql,$arr)
    {
        $this->conn->query_data_arr($sql,$arr);
    }

    function query_fetch_result($result)
    {
        return $this->conn->query_fetch_result($result);
    }

    function query_upd($sql,$arr)
    {
        return $this->conn->query_upd($sql,$arr);
    }

    function last_insert_id($sql,$arr=array())
    {
        return $this->conn->last_insert_id($sql,$arr);
    }

    function begin()
    {
        return $this->conn->begin();
    }

    function commit()
    {
        return $this->conn->commit();
    }

    function rollback()
    {
        return $this->conn->rollback();
    }

    function lock($table)
    {
        return $this->conn->lock($table);
    }

    function unlock()
    {
        return $this->conn->unlock();
    }

    function exe_upd($sql)
    {
        return $this->conn->exe_upd($sql);
    }

    function transaction($sql_arr)
    {
        return $this->conn->transaction($sql_arr);
    }
}