<?php

/**
 *
 */
interface Cache {
	 /**
     * Gets data.
     *
     * @param mixed $key     The key that will be associated with the item .
     * @param mixed $default Default value.
     *
     * @return mixed Stored data.
     */
	public function get($key, $default = null);
	
	/**
     * Stores data.
     *
     * @param string  $key    The key that will be associated with the item.
     * @param mixed   $value  The variable to store.
     * @param integer $expire Expiration time of the item. Unix timestamp or
     *                        number of seconds.
     */
	public function set($key, $value, $expire = 0);
	
	public function replace($key, $value, $expire=0);
	
	public function add($key, $value, $expire=0);
	
	public function setMulti($items, $expire=0);
	
	public function getMulti( $keys, $isCas=false);
	
	public function delete($key, $expire=0);
	
	public function flush( $delay=0);
}

?>