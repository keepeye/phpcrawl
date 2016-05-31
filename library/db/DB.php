<?php
/**
 * DB.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library\db;

use PDO;


class DB
{
    /**
     * @var DB
     */
    public static $instance;

    public $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    //插入数据
    public function insert($table,$values)
    {
        $fields = array_keys($values);
        $values = array_values($values);
        $placeholders = array_fill(0,count($fields),'?');
        $sql = "insert into `{$table}` (`".implode("`,`",$fields)."`) values (".implode(",",$placeholders).")";
        return $this->execute($sql,$values);
    }

    //更新数据
    public function update($table,$id,$data)
    {
        $sql = "update `{$table}` SET ";
        $sets = [];
        $params = [];
        foreach ($data as $k=>$v) {
            $sets[] = $k.'=:'.$k;
            $params[':'.$k] = $v;
        }
        $sql .= implode(",",$sets);
        $sql .= " where id=:id";
        $params[':id'] = $id;
        return $this->execute($sql,$params);
    }

    //select 参数绑定查询
    public function query($sql,$params=[])
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    //insert,update,delete 参数绑定操作
    public function execute($sql,$params=[])
    {
        $sth = $this->dbh->prepare($sql);
        return $sth->execute($params);
    }

    //直接执行sql语句
    public function exec($sql)
    {
        return $this->dbh->exec($sql);
    }

    //连接数据库并初始化实例
    public static function connect($dns)
    {
        self::$instance = new static(new PDO($dns));
    }

    //调用不存在的静态方法
    public static function __callStatic($name, $arguments)
    {
        if (!self::$instance) {
            throw new \Exception("请先通过connect方法初始化");
        }
        return call_user_func_array([self::$instance, $name], $arguments);
    }
}