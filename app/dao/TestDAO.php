<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/25
 * Time: 下午5:52
 */
require_once 'dao/BaseDAO.php';
class TestDAO extends BaseDAO{
    public function __construct(){
        $this->table = 'test_frame';
        $this->jsonKeys = 'data_json';
        $this->fields = array(
            TestConst::UID,
            TestConst::NAME,
            TestConst::EMAIL,
            TestConst::DATA_JSON
        );
        parent::__construct();
    }
}