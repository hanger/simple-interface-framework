<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 15/11/23
 * Time: 下午3:06
 */

class PDODBManager extends ADBManager{
    private $pdo = null;
    public function __construct($server){
        $dbConfigs = parse_ini_file('db_config.ini',true);

        if(!isset($dbConfigs[$server]) || empty($dbConfigs[$server]))
            die('db not exist!');
        $dbConfig = $dbConfigs[$server];

        $dsn = "mysql:host=".$dbConfig['host'].":".(isset($dbConfig['port'] ) ? $dbConfig['port'] : 3306 ). ";dbname=".$dbConfig['dbname'];
        $this->pdo = new PDO($dsn,$dbConfig['username'],$dbConfig['password'],isset($dbConfig['options']) ? $dbConfig['options'] : null);

        if(isset($dbConfig['attributes'])){
            foreach ($dbConfig['attributes'] as $key => $attribute) {
                if(is_numeric($attribute))
                    $this->pdo->setAttribute(constant($key),$attribute);
                else
                    $this->pdo->setAttribute(constant($key),constant($attribute));
            }
        }
    }

    function mysql_fetch_array_assoc($result)
    {
        return $result->fetch ( PDO::FETCH_ASSOC );
    }

    function begin()
    {
        return $this->pdo->beginTransaction();
    }

    function commit()
    {
        return $this->pdo->commit();
    }

    function rollback()
    {
        return $this->pdo->rollBack();
    }

    function lock($table)
    {
        // TODO: Implement lock() method.
    }

    function unlock()
    {
        // TODO: Implement unlock() method.
    }

    function exe_upd($sql)
    {
        return $this->pdo->exec ( $sql );
    }

    function transaction($sql_arr)
    {
        $now_sql = "";
        try {
            $this->begin ();
            foreach ( $sql_arr as $sql ) {
                $now_sql = $sql;
                $status = $this->exe_upd( $sql );
            }
            $ret = $this->commit ();
        } catch ( PDOException $ex ) {
            $this->rollBack ();
            $ret = false;
        }
        return $ret;
    }

    function is_no_data($result)
    {
        try {
            return $result == null;
        } catch ( PDOException $e ) {
            $debug_arr = debug_backtrace ();
            Tool::put_err_log ( $e->getMessage () . ", trace : " . json_encode ( $debug_arr ) );
            return true;
        }
    }

    function query_data_result($sql,$arr)
    {
        $stm = null;
        try {
            $stm = $this->pdo->prepare ( $sql );
            $stm->execute($arr);
        } catch ( PDOException $e ) {
            $debug_arr = debug_backtrace ();
            Tool::put_err_log ( "sql : " . $sql . ", " . $e->getMessage () . ", trace : " . json_encode ( $debug_arr ) );
        }
        if(!$stm)
            die ( "db err" . self::$db_err . ", sql : " . $sql );
        return $stm;
    }

    function query_data_arr($sql,$arr)
    {
        $stm = $this->query_data_result ( $sql ,$arr);
        try {
            return $stm->fetchAll ( PDO::FETCH_ASSOC );
        } catch ( PDOException $e ) {
            $debug_arr = debug_backtrace ();
            Tool::put_err_log ( "sql : " . $sql . ", " . $e->getMessage () . ", trace : " . json_encode ( $debug_arr ) );
            return null;
        }
    }

    function query_fetch_result($result)
    {
        try {
            return $result->fetch ( PDO::FETCH_ASSOC );
        } catch ( PDOException $e ) {
            return null;
        }
    }

    function query_upd($sql,$arr=array())
    {
        if (empty ( $sql )) {
            return false;
        }
        try {
            $stm = $this->pdo->prepare($sql);
            return $stm->execute($arr);
        } catch ( PDOException $e ) {
            $debug_arr = debug_backtrace ();
            Tool::put_err_log ( "sql : " . $sql . ", " . $e->getMessage () . ", trace : " . json_encode ( $debug_arr ) );
            return false;
        }
    }

    function last_insert_id($sql,$arr=array())
    {
        try {
            $this->query_upd($sql,$arr);
            $ret_val = $this->pdo->lastInsertId ();
            return $ret_val;
        } catch ( PDOException $e ) {
            $debug_arr = debug_backtrace ();
            Tool::put_err_log ( "sql : " . $sql . ", " . $e->getMessage () . ", trace : " . json_encode ( $debug_arr ) );
            return false;
        }
    }
}