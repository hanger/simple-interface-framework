<?php

require_once ('plugin/cache/Cache.php');

class MemCachedImpl implements Cache {
	
	protected $servers = array ();
	private $mc = null;
	private $connected = false;
	function __construct($servers, $persistentid = null) {
		if (! class_exists ( 'Memcached' )) { //强制使用
			return null;
			die ( 'This Lib Requires The Memcached Extention!' );
		}
		$this->servers = $servers;
		if (empty ( $this->mc )) {
			$this->mc = $persistentid ? new Memcached ( $persistentid ) : new Memcached (); //是否持久连接
			$this->mc->setOption ( Memcached::OPT_TCP_NODELAY, true ); //启用tcp_nodelay
			$this->mc->setOption ( Memcached::OPT_NO_BLOCK, true ); //启用异步IO
			$this->mc->setOption ( Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT ); //分布式策略
			$this->mc->setOption ( Memcached::OPT_LIBKETAMA_COMPATIBLE, true ); //分布式服务组分散.推荐开启 
			$this->mc->setOption ( Memcached::OPT_HASH, Memcached::HASH_CRC ); //Key分布
		}
	}
	
	private function connect() {
		if (! $this->connected && ! empty ( $this->mc )) {
			$this->mc->addServers ( $this->servers );
			$this->connected = true;
		}
	}
	/**
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $expire
	 * @param unknown_type $zip
	 */
	public function add($key, $value, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->add ( $key, $value, $expire );
		$resultCode = $this->mc->getResultCode ();
		if ($resultCode != Memcached::RES_SUCCESS) { //不成功
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	}
	
	/**
	 * @param unknown_type $key
	 * @param unknown_type $expire
	 */
	public function delete($key, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->delete ( $key, ( int ) $expire );
		$resultCode = $this->mc->getResultCode ();
		if (! in_array ( $resultCode, array (Memcached::RES_SUCCESS, Memcached::RES_NOTFOUND ) )) { //删除不成功
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	}
	
	/**
	 * @param unknown_type $delay
	 */
	public function flush($delay = 0) {
	
	}
	
	/**
	 * @param unknown_type $key
	 * @param unknown_type $default
	 */
	public function get($key, $isCas = false) {
		$this->connect ();
		$result = null;
		$result = $isCas ? @$this->mc->get ( $key, null, $token ) : @$this->mc->get ( $key );
		$resultCode = $this->mc->getResultCode ();
		if ($resultCode == Memcached::RES_SUCCESS || $resultCode == Memcached::RES_NOTFOUND) {
			$result = $isCas ? array ($result, $token ) : $result;
		} else {
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		
		return $result;
	
	}
	
	/**
	 * @param unknown_type $keys
	 * @param unknown_type $zip
	 * @param unknown_type $inCas
	 */
	public function getMulti($keys, $isCas = false) {
		$this->connect();
		$result = null;
		$result = $isCas ? @$this->mc->getMulti($keys, $token, Memcached::GET_PRESERVE_ORDER) : @$this->mc->getMulti( $keys);
		$resultCode = $this->mc->getResultCode ();
		if( $resultCode == Memcached::RES_SUCCESS || $resultCode == Memcached::RES_NOTFOUND){
 			return $isCas ? array($result, $token) : $result;
        }else {
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
        return $result;
	}
	
	/**
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $expire
	 * @param unknown_type $zip
	 */
	public function replace($key, $value, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->replace ( $key, $value, $expire );
		$resultCode = $this->mc->getResultCode ();
		if (! in_array ( $resultCode, array (Memcached::RES_SUCCESS, Memcached::RES_NOTSTORED ) )) {
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	}
	
	/**
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $expire
	 */
	public function set($key, $value, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->set ( $key, $value, $expire );
		$resultCode = $this->mc->getResultCode ();
		if ($resultCode != Memcached::RES_SUCCESS) {
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	}
	
	/**
	 * @param unknown_type $items
	 * @param unknown_type $expire
	 * @param unknown_type $zip
	 */
	public function setMulti($items, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->setMulti ( $items, $expire );
		$resultCode = $this->mc->getResultCode ();
		if ($resultCode != Memcached::RES_SUCCESS) { 
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	
	}
	
	public function cas($token, $key, $value, $expire = 0) {
		$this->connect ();
		$rr = $this->mc->cas ( $token, $key, $value, $expire );
		$resultCode = $this->mc->getResultCode ();
		if ($resultCode != Memcached::RES_SUCCESS) { //保存成功则退出此层循环
			throw new Exception ( "memcached errorcode " . $resultCode . " : " . $this->mc->getResultMessage () );
		}
		return $rr;
	}

}

?>