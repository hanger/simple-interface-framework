<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 15/11/23
 * Time: 下午3:06
 */

class MysqliDBManager extends ADBManager{
    public $con;
    public function __construct($server){
        $dbConfigs = parse_ini_file('db_config.ini',true);

        if(!isset($dbConfigs[$server]) || empty($dbConfigs[$server]))
            die('db not exist!');
        $dbConfig = $dbConfigs[$server];

        $this->con = mysqli_connect( $dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname'], isset($dbConfig['port']) ? $dbConfig['port'] : 3306);
        if (! $this->con) {
            usleep ( 1000 );
            $this->con = mysqli_connect( $dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname'], isset($dbConfig['port']) ? $dbConfig['port'] : 3306);
        }
        mysqli_query ( $this->con,"set names utf8" );
    }

    function mysql_fetch_array_assoc($result)
    {
        return mysqli_fetch_array ( $result, MYSQL_ASSOC );
    }

    function begin()
    {
        mysqli_query ($this->con, "SET AUTOCOMMIT=0" );
        mysqli_query ( $this->con, "START TRANSACTION");
        return mysqli_query ( $this->con, "BEGIN");
    }

    function commit()
    {
        return mysqli_query( $this->con, "COMMIT");
    }

    function rollback()
    {
        return mysqli_query( $this->con, "ROLLBACK");
    }

    function lock($table)
    {
        mysqli_query ($this->con, "LOCK TABLES " . $table . " WRITE," . $table . " as " . $table . "1 WRITE;");
    }

    function unlock()
    {
        mysqli_query($this->con,"UNLOCK TABLES;");
    }

    function exe_upd($sql)
    {
        $result = mysqli_query ( $this->con,$sql );
        $ret_val = 0;
        if ($result) {
            $ret_val = mysqli_affected_rows($this->con);
        }
        return $ret_val;
    }

    function transaction($sql_arr)
    {
        $retval = 1;
        $this->begin();

        $log_str = "";
        foreach ( $sql_arr as $sql ) {
            if (empty ( $sql )) {
                continue;
            }
            $result = mysqli_query ( $this->con,$sql );
            $log_str .= $sql . ",  result = " . $result . "\n";
            if (! $result) {
                Tool::put_log ( "[conn] transaction db error : " . $log_str . ", err : " . mysqli_error ($this->con) );
                $retval = 0;
                break;
            }
        }
        if ($retval == 0) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }

    function is_no_data($result)
    {
        return null == $result || !mysqli_num_rows($result);
    }

    function query_data_result($sql,$arr)
    {
        if (empty ( $sql )) {
            return null;
        }
        Tool::put_log ( $sql );
        $stm = $this->prepareStatement($sql,$arr);
        return $stm->get_result();
    }

    /**
     * 构造prepare语句结构
     * @param $sql
     * @param array $arr
     * @return mysqli_stmt
     * @throws Exception
     */
    private function prepareStatement($sql,$arr=array()){
        $stm = $this->con->prepare($sql);
        //占位符个数
        $num = substr_count($sql,'?');
        if($num){
            if($num != count($arr))
                throw new Exception('placeholder not match : ' . $sql);
            $types = '';
            $params = array($stm,&$types);
            for($i = 0;$i < $num;++$i){
                if(is_int($arr[$i]))
                    $types .= 'i';
                elseif(is_double($arr[$i]))
                    $types .= 'd';
                else {
                    $types .= 's';
                    if(is_array($arr[$i]))
                        $arr[$i] = json_encode($arr[$i]);
                }
                $params[] = & $arr[$i];
            }
            call_user_func_array('mysqli_stmt_bind_param', $params);
        }
        $stm->execute();
        return $stm;
    }

    function query_data_arr($sql,$arr)
    {
        Tool::put_log ( "[db_mng.query_data_arr] sql : " . $sql );
        $stm = $this->query_data_result($sql,$arr);
        $ret = array ();
        if (! $this->is_no_data ( $stm )) {
            $result_data = $this->mysql_fetch_array_assoc ( $stm );
            while ( $result_data ) {
                $ret [] = $result_data;
                $result_data = $this->mysql_fetch_array_assoc ( $stm );
            }
        }
        return $ret;
    }

    function query_fetch_result($result)
    {
        return $this->mysql_fetch_array_assoc ( $result );
    }

    function query_upd($sql,$arr)
    {
        Tool::put_log ( $sql );
        if (empty ( $sql )) {
            return false;
        }
        $stm = $this->prepareStatement($sql, $arr);
        return $stm->affected_rows > 0;
    }

    function last_insert_id($sql,$arr=array())
    {
        Tool::put_log ( $sql );
        $this->query_upd ( $sql ,$arr);
        $ret_val = mysqli_insert_id ( $this->con );
        return $ret_val;
    }
}