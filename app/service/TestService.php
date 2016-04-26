<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/21
 * Time: 上午9:34
 */
require_once 'service/BaseService.php';
require_once 'gateway/TestGateway.php';
class TestService extends BaseService{
    public function getList(){
        $where = array(
            array(
                TestConst::WHERE_LOGIC => '',
                TestConst::WHERE_PROPERTY => TestConst::NAME,
                TestConst::WHERE_OPERATOR => '='
            ),
            array(
                TestConst::WHERE_LOGIC => 'OR',
                TestConst::WHERE_PROPERTY => TestConst::EMAIL,
                TestConst::WHERE_OPERATOR => '=',
            )
        );
        $whereValues = array('hange','123@gmail.com');
        return TestGateway::getInstance()->queryList($where,$whereValues);
    }

    public function getOne(){
        $where = array(
            array(
                TestConst::WHERE_LOGIC => '',
                TestConst::WHERE_PROPERTY => TestConst::EMAIL,
                TestConst::WHERE_OPERATOR => '=',
            )
        );
        $whereValues = array('123@gmail.com');
        return TestGateway::getInstance()->queryOne($where,$whereValues);
    }

    public function insert(){
        $user = array(
            TestConst::NAME => 'hange',
            TestConst::EMAIL => 'zhaihange@gmail.com',
            TestConst::DATA_JSON => array(
                'age' => '永远20岁',
                'des' => '此描述信息真实有效'
            )
        );
        return TestGateway::getInstance()->insert($user);
    }

    public function update(){
        $update = array(
            TestConst::NAME => 'google',
            TestConst::DATA_JSON => array(
                'age' => '18',
                'des' => '说你十八你不信咋地'
            )
        );
        $where = array(
            array(
                TestConst::WHERE_LOGIC => '',
                TestConst::WHERE_PROPERTY => TestConst::EMAIL,
                TestConst::WHERE_OPERATOR => '='
            )
        );
        $whereValues = array('123@gmail.com');
        return TestGateway::getInstance()->update($update,$where,$whereValues);
    }

    public function delete(){
        $where = array(
            array(
                TestConst::WHERE_LOGIC => '',
                TestConst::WHERE_PROPERTY => TestConst::NAME,
                TestConst::WHERE_OPERATOR => '='
            )
        );
        $whereValues = array('hange');
        return TestGateway::getInstance()->delete($where,$whereValues);
    }
}