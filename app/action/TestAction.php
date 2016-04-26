<?php
/**
 * Created by PhpStorm.
 * User: hange
 * Date: 16/4/21
 * Time: 下午5:33
 */
require_once 'action/ABaseAction.php';
require_once 'service/TestService.php';
require_once 'const/TestConst.php';
class TestAction extends ABaseAction{

    public function handle()
    {
        $this->service = new TestService();
        switch($this->cmd){
            case TestConst::CMD_DB_GET:
                $this->data = $this->service->getList();
                break;
            case TestConst::CMD_DB_INSERT:
                $this->data = $this->service->insert();
                break;
            case TestConst::CMD_DB_GET_ONE:
                $this->data = $this->service->getOne();
                break;
            case TestConst::CMD_DB_UPDATE:
                $this->data = $this->service->update();
                break;
            case TestConst::CMD_DB_DELETE:
                $this->data = $this->service->delete();
                break;
        }
    }
}