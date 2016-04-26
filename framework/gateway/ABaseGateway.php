<?php
require_once 'db/DBManager.php';
require_once 'gateway/IBaseGateway.php';
abstract class ABaseGateway implements IBaseGateway{
	protected $mc = null;
	protected $dao = null;
	protected $key;
	public function __construct($memcached){
//		$this->mc =
	}
}

?>