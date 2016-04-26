<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/22
 * Time: 下午5:02
 */
require_once 'dao/ABaseDAO.php';
class BaseDAO extends ABaseDAO{
    public function __construct(){
        parent::__construct();
    }
}