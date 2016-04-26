<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/22
 * Time: 下午5:01
 */
require_once 'gateway/ABaseGateway.php';
require_once 'const/SettingConst.php';
class BaseGateway extends ABaseGateway{
    public function __construct($memcached=''){
        parent::__construct($memcached);
    }
    public function insert($data){
        return $this->dao->insert($data);
    }
    public function queryList($where = array(),$values=array(),$order = array(),$limit = 0,$offset = 0){
        return $this->dao->queryList($where,$values,$order,$limit,$offset);
    }
    public function queryOne($where,$values){
        return $this->dao->queryOne($where,$values);
    }
    public function delete($where,$values){
        return $this->dao->delete($where,$values);
    }
    public function update($data,$where,$values){
        return $this->dao->update($data,$where,$values);
    }
}