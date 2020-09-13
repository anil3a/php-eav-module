<?php 
namespace EAV;

class Database {

	public $db;

	public $queryCount = 0;

	public $queries = array();
	public $queryPrepared;

	static private $instance;

	public function __construct()
	{
		$this->db = mysqli_connect( DBHOST, DBUSER, DBPASS );
		mysqli_select_db($this->db, DBNAME );
		mysqli_set_charset($this->db, 'utf8');
	}

	static public function instance()
	{
		if (!isset(self::$instance)) {
			$name = __CLASS__;
			self::$instance = new $name;
		}
		return self::$instance;
	}

	public function esc($str)
	{  
		if (is_null($str)) return null; 
		return mysqli_real_escape_string($this->db, $str); 
	}

	public function escapeStr($str)
	{ 
		if (is_null($str)) return null; 
		return mysqli_real_escape_string($this->db, $str); 
	} 

	public function escapeArray($arr)
	{
        foreach ($arr as $k => $v) {
        	if (!is_null($v)) {
            	$arr[$k] = $this->escapeStr($v);
            }
        }
        return $arr;
	}

	public function query($query)
	{
		$this->queryCount++;
		$this->queries[] = $query;

		$result = mysqli_query($this->db, $query);

		if (mysqli_num_rows($result) == 0) {
			return array();
		}

		$salida = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$salida[] = $row;
		}

		mysqli_free_result($result);

		return $salida;
	}

	public function insert_id()
	{
		return mysqli_insert_id($this->db);
	}

	public function insert($table, $data)
	{
		$this->queryCount++;
		
		$fields = $this->escapeArray(array_keys($data));
		$values = $this->escapeArray(array_values($data));

		foreach ($values as $k => $val) {
			if (is_null($val)) {
				$values[$k] = 'NULL';
			} else {
				$values[$k] = "'$val'";
			}
		}

		$query = "INSERT INTO $table(`".join("`,`",$fields)."`) VALUES(".join(",", $values).")";

		$this->queries[] = $query;

		return mysqli_query($this->db, $query);
	}

	public function getAffectedRows()
	{
		return mysqli_affected_rows($this->db);
	}

	public function execute($query)
	{
		$this->queryCount++;
		$this->queries[] = $query;

		return mysqli_query($this->db, $query);
	}

	public function update($table, $data, $where = array())
	{
		$qwhere = "";
		if (!empty($where)) {
			$qwhere = $this->where($where);
		}

		$update = array();
		foreach ($data as $field => $value) {
			if (is_null($value)) {
				$update[] = "`$field` = NULL";
			} else {
				$update[] = "`$field` = '".$this->esc($value)."'";
			}
		}

		$query = "UPDATE $table SET ".join(" , ", $update)." $qwhere";

		return $this->execute($query);
	}

	public function where($conditions)
	{
		$where = "";
		if (!empty($conditions) && is_array($conditions)) {
			$where = array();
			foreach ($conditions as $field => $value) {
				if (is_numeric($field) || empty($field)) {
					$where[] = " $value ";
				} else if (is_null($value)) {
					$where[] = " $field is null ";
				} else {
					$where[] = " $field = '".$this->escapeStr($value)."' ";
				}
			}
			if (!empty($where)) {
				$where = " WHERE " . join(" AND ", $where);
			}
		} else if (!empty($conditions)) {
			$where = " WHERE " . $conditions;
		}
		return $where;
    }

    public function get($table, $fields = null, $where = [], $opts = null)
	{
		if (!empty($fields)) {
			$query = "SELECT $fields FROM $table";
		} else {
			$query = "SELECT * FROM $table";
        }
		if( !empty( $where ) )
		{
			$query .= $this->where( $where );
		}
		if( !empty( $opts ) )
		{
			$query .= " ". $opts;
		}
		return $this->query($query);
	}

	public function prepareQuery($table, $fields = null, $where = [], $opts = null)
	{
		if (!empty($fields)) {
			$query = "SELECT $fields FROM $table";
		} else {
			$query = "SELECT * FROM $table";
        }
		if( !empty( $where ) )
		{
			$query .= $this->where( $where );
		}
		if( !empty( $opts ) )
		{
			$query .= " ". $opts;
		}
		$this->queryPrepared = $query;
		return $this;
	}

	public function result_row( $query = null )
	{
		$sql = '';
		if( !empty( $query ) )
		{
			$sql = $query;
		}
		if( !empty( $this->queryPrepared ) )
		{
			$sql = $this->queryPrepared;
		}
		if( empty( $sql ) )
		{
			throw new \Exception( "Empty query for result row" );
		} else {
			$this->queryCount++;
			$this->queries[] = $sql;
		}

		$result = mysqli_query($this->db, $sql);
		if (!$result) {
			return false;
		}

		if (mysqli_num_rows($result) == 0) {
			return false;
		}

		$row = mysqli_fetch_assoc($result);

		mysqli_free_result($result);

		return $row;
	}

	public function result_array( $query = null )
	{
		$sql = '';
		if( !empty( $query ) )
		{
			$sql = $query;
		}
		if( !empty( $this->queryPrepared ) )
		{
			$sql = $this->queryPrepared;
		}
		if( empty( $sql ) )
		{
			throw new \Exception( "Empty query for result array" );
		}

		return $this->query( $sql );
	}

}