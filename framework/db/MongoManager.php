<?php
require_once ('plugin/phpmailer/class.phpmailer.php');
/**
 * mongodb manager
 * @author hange <zhaihange@gmail.com> 2013-12-4
 * 
 */
class MongoManager {
	private $config ;
	private $options ;
	private $db ;
	public function __construct($config)
	{
		$this->config = $config ;
		$this->options = array();
		$this->connect() ;
	}
	
	/**
	 * connect to mongodb
	 */
	private function connect()
	{
		$connection_string = $this->connection_string() ;
		if (!class_exists("Mongo"))
		{
			exit("mongoDB required!");
		}
		try{
			$conn = new Mongo($connection_string,$this->options);
			$this->db = $conn->{$this->config['dbname']} ;
		} catch(MongoConnectionException $e){
			//todo 发送管理员警告邮件
			//结束请求
			exit("mongo cont't connect");
		}
	}
	/**
	 * insert data to specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $data
	 */
	public function insert($coll,$data)
	{
		if (!$coll)
		{
			exit("db not exsits!");
		}
		$this->db->{$coll}->insert($data, array('fsync' => true));
		$this->mongo2Array($data);
		return $data ;
	}
	/**
	 * find data from specify collection 
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $fields whate colums to be show 
	 * @return multitype: array
	 */
	public function findAll($coll,$query=array(),$fields=array())
	{
		return $this->find($coll,$query,$fields);
		
	}
	/**
	 * find from specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $fields  what colums to be show 
	 * @param unknown_type $skip   the starting point of the results set
	 * @param unknown_type $limit  how many rows to show 
	 * @param unknown_type $sort  array('key'=>1/-1,...) sort by 1:asc;-1:desc
	 * @return multitype:
	 */
	public function find($coll,$query=array(),$fields=array(),$skip=0,$limit=0,$sort=array())
	{
		$cursor = $this->db->{$coll}->find($query,$fields);
		if (!empty($sort)) {
			$cursor->sort($sort);
		}
		if ($limit>0) {
			$cursor->limit($limit);
		}
		if ($skip > 0 ) {
			$cursor->skip($skip);
		}
		$data = array() ;
		foreach ($cursor as $doc)
		{
			$this->mongo2Array($doc);
			array_push($data, $doc);
		}
		return $data ;
	}
	/**
	 * get one row from specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $fields  whate colums to be show 
	 * @return unknown obj
	 */
	public function findOne($coll,$query=array(),$fields=array())
	{
		$data = $this->db->{$coll}->findOne($query,$fields);
		$this->mongo2Array( $data ) ;
		return $data ;
	}
	
	/**
	 * update data to specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $newData data new to update
	 * @param unknown_type $query  conditions equals to mysql where
	 */
	public function update($coll,$newData,$query)
	{
		$this->array2Mongo($newData);
		return $this->db->{$coll}->update($query,$newData);
	}
	/**
	 * search from specify collection for some rows by coordinate('langtitude','latitude')
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $near coordinate as the center array('langtitude','latitude')
	 * @param unknown_type $maxDistance  the maxDistance(unit of radian) from the center to search
	 * @param unknown_type $limit how much rows to show as max
         * @param unknown_type $skip how much rows to skip
	 * @return multitype: array
	 */
	public function searchGeo($coll,$query,$near,$maxDistance,$limit,$skip=0)
	{
		$command = array(
				'geoNear' => $coll,
				'near' => $near ,	
				'query' => $query,
				'spherical' => true,
				'distanceMultiplier' => 6371
			);
		if ($maxDistance > 0 )
		{
			$command['maxDistance'] = $maxDistance / 6371 ;
		}
		if ($limit>0)
		{
			$command['num'] = $limit ;
		}
		$result = $this->db->command($command);
		$data = array();
		if ($result['ok']==1) {
                        $res = array();
                        $count = count($result['results']);
                        if($count > $skip )
                        {
                            $res = array_slice($result['results'], $skip,$count-$skip);
                            foreach ( $res as $value)
                            {
                                $tmp = $value['obj'];
                                $tmp['dis'] = $value['dis'] * 1000 ;
                                $this->mongo2Array($tmp);
                                array_push($data, $tmp);
                            }
                        }
		}
		return $data ;
	}
	/**
	 * append some fields new to specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $pushKey  append to which field
	 * @param unknown_type $newData  append data
	 * @return boolean
	 */
	public function push($coll,$query,$pushKey,$newData)
	{
		$newData = array(
			'$push' => array(
				$pushKey => $newData ,		
			)
		);
		return $this->update($coll, $newData,$query);
	}
	/**
	 * update some fields new to specify collection
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @param unknown_type $pushKey  update to which field
	 * @param unknown_type $newData  new data
	 * @return boolean
	 */
	public function set($coll,$query,$setKey,$newData)
	{
		$newData = array(
				'$set' => array(
					$setKey.'.$' => $newData		
				)	
			);
		return $this->update($coll, $newData,$query);
	}
	/**
	 * remove matched documents
	 * @param unknown_type $coll
	 * @param unknown_type $query  conditions equals to mysql where
	 * @return boolean
	 */
	public function delete($coll,$query)
	{
		return $this->db->{$coll}->remove($query);
	}
	/**
	 * organize the mongodb config to string format
	 * @return string
	 */
	private function connection_string() {
		$host = trim($this->config['host']);
		$port = trim($this->config['port']);
		$user = trim($this->config['user']);
		$pass = trim($this->config['pass']);
	
		$dbname  = trim($this->config['dbname']);
		$persist = trim($this->config['persist']);
		$persist_key = trim($this->config['persist_key']);
	
		$connection_string = "mongodb://";
	
		if (empty($host)) {
			// $log_error("The Host must be set to connect to MongoDB");
			exit;
		}
	
		if (empty($dbname)) {
			// $log_error("The Database must be set to connect to MongoDB");
			exit;
		}
	
		if ( ! empty($user) && ! empty($pass)) {
			$connection_string .= "{$user}:{$pass}@";
		}
	
		if ( isset($port) && ! empty($port)) {
			$connection_string .= "{$host}:{$port}";
		} else {
			$connection_string .= "{$host}";
		}
		$connection_string = trim($connection_string);
		return $connection_string;
	}
	/**
	 * mongo结果集转换_id
	 * @param unknown_type $mongoRes
	 */
	private function mongo2Array(&$mongoRes)
	{
		if(isset($mongoRes['_id']) && is_object($mongoRes['_id']))
			$mongoRes['_id'] = $mongoRes['_id']->__toString();
	}
	/**
	 * 将数组中的_id转换成mongo的_id对象
	 * @param unknown_type $array
	 */
	private function array2Mongo(&$array)
	{
		if(isset($array['_id']) && is_string($array['_id']))
			$array['_id'] = new MongoId( $array['_id'] );
	}
}

?>