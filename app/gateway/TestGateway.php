<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/25
 * Time: 上午10:30
 */
require_once 'gateway/BaseGateway.php';
require_once 'dao/TestDAO.php';
class TestGateway extends BaseGateway{
    public function __construct(){
        $this->dao = new TestDAO();
        parent::__construct();
    }

    public static $instance;
    public static function getInstance(){
        if(null == self::$instance)
            self::$instance = new TestGateway();
        return self::$instance;
    }
}