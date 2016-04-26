<?php
require_once 'IDAO.php';
require_once 'db/DBManager.php';
require_once 'const/SettingConst.php';
abstract class ABaseDAO implements IDAO {
	protected $server = SettingConst::DEF_DB_SERVER;
	protected $fields = array('*');
	protected $table = '';
	protected $jsonKeys;

	protected $conn;

	protected function __construct(){
		$this->conn = DBManager::getConnection($this->server);	}

	public function delete($where,$whereValues){
		if(!$where || !$whereValues)
			return false;
		$sql = "DELETE FROM " . $this->table . " WHERE " . createWherePlaceHolderByArray($where);
		return $this->conn->query_upd($sql,$whereValues);
	}

	public function update($update,$where,$whereValues){
		if(!$update || !$where || !$whereValues)
			return false;
		if($this->jsonKeys){
			if(is_array($this->jsonKeys)){
				foreach($this->jsonKeys as $key){
					if(isset($update[$key]) && is_array($update[$key]))
						$update[$key] = json_encode($update[$key]);
				}
			}elseif(is_string($this->jsonKeys) && isset($update[$this->jsonKeys]) && is_array($update[$this->jsonKeys])){
				$update[$this->jsonKeys] = json_encode($update[$this->jsonKeys]);
			}
		}
		$sql = "UPDATE " . $this->table . " SET " . createUpdatePlaceHolderByArray($update) . " WHERE " . createWherePlaceHolderByArray($where);
		return $this->conn->query_upd($sql,array_merge(array_values($update),$whereValues));
	}

	public function queryOne($where,$whereValues){
		if(!$where || !$whereValues){
			return null;
		}
		$sql = "SELECT " . implode(',',$this->fields) . " FROM " . $this->table . " WHERE " . createWherePlaceHolderByArray($where);
		$res = $this->conn->query_data_arr_data_json($sql,$whereValues,$this->jsonKeys);
		if($res)
			return $res[0];
		else
			return null;
	}

	public function queryList($where = array(),$whereValues=array(),$order = array(),$limit = 0,$offset = 0){
		if($where)
			$sql = "SELECT " . implode(',',$this->fields) . " FROM " . $this->table . ' WHERE ' . createWherePlaceHolderByArray($where);
		else
			$sql = "SELECT " . implode(',',$this->fields) . " FROM " . $this->table;

		if($order){
			$sql .= ' ORDER BY ' . implode(',',$order);
		}
		if($limit){
			$sql .= ' LIMIT ' . $offset . ',' . $limit;
		}
		return $this->conn->query_data_arr_data_json($sql,$whereValues,$this->jsonKeys);
	}

	public function insert($data){
		if($this->jsonKeys){
			if(is_array($this->jsonKeys)){
				foreach($this->jsonKeys as $key){
					if(isset($data[$key]) && is_array($data[$key]))
						$data[$key] = json_encode($data[$key]);
				}
			}elseif(is_string($this->jsonKeys) && isset($data[$this->jsonKeys]) && is_array($data[$this->jsonKeys])){
				$data[$this->jsonKeys] = json_encode($data[$this->jsonKeys]);
			}
		}
		$sql = "INSERT INTO " . $this->table . "(". implode(',',array_keys($data)) .") VALUES(".createPlaceHolder(count($data)).") ";
		return $this->conn->last_insert_id($sql,array_values($data));
	}

	public function begin(){
		return $this->conn->begin();
	}

	public function commit(){
		return $this->conn->commit();
	}
}
?>