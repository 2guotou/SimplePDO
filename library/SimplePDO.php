<?php
/**
 *  SimplePDO 是基于 PDO 类封装的 易用PDO 类
 *  主要用于完成一些常规操作：
 *      ::instance();         //获得一个PDO单例
 *      ->doQuery();          //执行一段SQL
 *      ->doPrepareExecute(); //执行一段预加载的SQL
 *      ->doInsert();         //插入一条数据
 *      ->doUpdate();         //更新一条数据
 *  @author 刘东阳
 *  @wrote  2015 spring
 *  @repo   https://github.com/2guotou/SimplePDO
 */
class SimplePDO extends PDO{
    
    private static $pdo;

    function __construct($dsn, $user, $pass){
        parent::__construct($dsn,$user,$pass,[
                //PDO::ATTR_PERSISTENT => TRUE, //keep persistent connect
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",// charset
        ]);
        self::$pdo = $this;
    }

    /**
     * 获取一个单例对象
     */
    static function &instance(){
        if( !(self::$pdo instanceof PDO) ){
            self::$pdo = new SimplePDO;
        }
        return self::$pdo;
    }

    /**
     * doQuery 完成 Query 操作，并依据 mode 返回相应的数据
     */
    public function doQuery($sql, $mode='fetchAll'){
        $stmt = $this->query($sql);
        $stmt or self::throwException("Prepare Return Empty");
        switch( $mode ){
            case 'fetchAll'    : return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'fetchOne'    : return $stmt->fetch(PDO::FETCH_ASSOC);
            case 'rowCount'    : return $stmt->rowCount();
            case 'lastInsertId': return $this->lastInsertId();
            default: return $stmt;
        }
    }

    /**
     * doPrepareExecute 完成 Prepare & Execute 系列操作，并根据 mode 返回相应的数据
     */
    public function doPrepareExecute($prepare, $values, $mode='fetchAll'){
        $stmt = $this->prepare($prepare);
        $stmt or self::throwException("Prepare Return Empty");
        $stmt ->execute($values);
        switch( $mode ){
            case 'fetchAll'    : return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'fetchOne'    : return $stmt->fetch(PDO::FETCH_ASSOC);
            case 'rowCount'    : return $stmt->rowCount();
            case 'lastInsertId': return $this->lastInsertId();
            default: return $stmt;
        }
    }

    /**
     * doInsert 完成插入操作
     */
    public function doInsert($table, array $insert, array $update=array()){
        $fields  = implode('`, `', array_keys($insert));
        $holder  = implode(',',array_pad([], count($insert), '?'));
        $prepare = "INSERT INTO `{$table}`(`{$fields}`) VALUES({$holder})";
        $execute = array_values($insert);
        $update_count = count($update);
        if( $update_count ){
            $prepare .= " ON DUPLICATE KEY UPDATE ";
            $i = 0;
            foreach($update as $key=>$val){
                $i++;
                $prepare .= "{$key}=?".($i<$update_count ? ',' : '');
                array_push($execute, $val);
            }
        }
        return $this->doPrepareExecute($prepare, $execute,'lastInsertId');
    }

    /**
     * doUpdate 完成更新操作
     * $where 和 $whereValues 有待合并简化
     */
    public function doUpdate($table, $update, $where='', array $whereValues=array()){
        $fields_list = [];
        foreach($update as $k => $v){
            $fields_list[] = "`{$k}`=?";
            if( $v === null ){
                self::throwException('SimplePDO不允许null值传入$update');
            }
        }
        $fields = implode(', ', $fields_list);
        $holder = array_values($update);
        $prepare = "UPDATE `{$table}` set {$fields}";
        if( $where ){
            $prepare .= " WHERE $where";
            foreach($whereValues as $v){
                $holder[] = $v;
            }
        }
        return $this->doPrepareExecute($prepare, $holder, 'rowCount');
    }

    /**
     * throwException 方便程序当行内抛出异常
     * Sample：$cond or SimplePDO::throwExecption($msg);
     */
    public static function throwException($msg, $code=1){
        throw new PDOException($msg, $code);
    }
}
