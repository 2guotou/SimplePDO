<?php

/**
	Notice: Model 层对应的数据表规则为 AbcEfg -> abc_efg；
	如果不方便遵守，请自行传入表名

	本Model不负责数据驱动，请自行使用 PDO，mysqli，或者本框架内的 SimplePOD（base on MysqlPDO）
*/
class Model{
	protected $table;
    /**
        Init Construct
    */
	function __construct($table=''){
		if( $table ){
			$this->table = $table;
		}else{
			$modelName   = get_class($this);
			$tableName   = str_replace('Model', '', $modelName);
			$this->table = strtolower(trim(preg_replace('/([A-Z])/', '_$1', $tableName), '_'));
		}
	}
    /**
        Singleton
    */
    public static function &instance(){
        static $instance;
        if(!$instance){
            $instance = new static();
        }
        return $instance;
    }
	/**
		only for table whose primary is id
	*/
	function getOne($id, $fields='*'){
        return SimplePDO::instance()->doPrepareExecute(
        	"SELECT {$fields} FROM `{$this->table}` WHERE id=? LIMIT 1",
        	[$id], 
        	'fetchOne'
        );
    }
    /**
        
    */
    function newOne($insert, $update=[]){
        $insert['create_time'] = 
        $insert['update_time'] = date('Y-m-d H:i:s');
        return SimplePDO::instance()->doInsert($this->table, $insert, $update);
    }
    /**
        
    */
    function saveOne($id, $update){
        $update['update_time'] = date('Y-m-d H:i:s');
        return SimplePDO::instance()->doUpdate($this->table, $update, "id=?", [$id]);
    }
}