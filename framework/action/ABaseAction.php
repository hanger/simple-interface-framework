<?php
require_once 'const/ABaseConst.php';
require_once 'action/IBaseAction.php';
abstract class ABaseAction implements IBaseAction{
	protected $result = ABaseConst::RESULT_SUCCESS;
	protected $code = 0;
	protected $message = '';
	protected $data = array();
	protected $extra = array();
	protected $cmd ;
	protected $service;

	public function __construct(){
		$this->cmd = Request::getInt(ABaseConst::CMD);
	}
	public abstract function handle();
	public function execute(){
		if(null == $this->service){
			$this->result = BaseConst::RESULT_FAIL;
			$this->code = ABaseConst::CODE_NO_ACTION;
		}
		if (empty($this->cmd)) {
			$this->result = BaseConst::RESULT_FAIL;
			$this->code = ABaseConst::CODE_NO_CMD;
		}else{
			try {
				$this->handle();
			} catch (ServiceException $e) {
				$this->result = BaseConst::RESULT_FAIL;
				$this->code = $e->getMessage ();
				$this->message = $e->getCode();
			}
		}
		$v = array_merge($this->extra,array(ABaseConst::RESULT=>$this->result,ABaseConst::RESULT_CODE=>$this->code,ABaseConst::MESSAGE=>$this->message,ABaseConst::SERVER_TIME=>time(),ABaseConst::DATA=>$this->data));
		echo preg_replace('/:(-?\d+\.?\d*)([,}])/', ':"$1"$2', json_encode($v));
	}

	public function putExtra($key,$value){
		$this->extra[$key] = $value;
	}
}
?>
