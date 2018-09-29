<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-29
 * Time: 14:19
 */


/**
 * 简单封装PDO数据库操作
 * 三私一公的单例模式
 * @license https://www.jb51.net/article/84202.htm
 */
class Db
{
    private static $obj;

    private $db;

    public $lastSql;  // 最后一次执行的sql

    private $where;  // 查询条件wehre参数

    private $table_nmae;  // 表名

    public $data;   // sql语句执行结果

    private function __construct()
    {
        $dsn = 'mysql:dbname=test;host=127.0.0.1';
        $user = 'root';
        $password = 'root';

        try {
            $this->db = new PDO($dsn, $user, $password);
            $this->db->exec('SET character_set_connection='.'utf8'.', character_set_results='.'utf8'.', character_set_client=binary');
        } catch (PDOException $e) {
            exit('Connection failed: ' . $e->getMessage());
        }
    }

    private function __clone()  {  }

    // 初始化方法
    public static function init()
    {
        if (!self::$obj instanceof self) {
            self::$obj = new self();
        }

        return self::$obj;
    }



    private function debug()
    {
        exit;
    }


    /**
     * 获取单例的pdo对象
     */
    public function pdo()
    {
        return $this->db;
    }

    /**
     * 选择操作的表名
     */
    public function tableName($table)
    {
        $this->table_nmae = $table;

        return $this;
    }

    /**
     * where语句过滤条件
     */
    public function where($where='')
    {
        $this->where = $where;

        return $this;
    }


    /**
     * 插入操作
     * @param $data
     */
    public function insert($data)
    {
        $this->lastSql = "";

        $this->execute();

        return $this;

    }

    /**
     * 更新操作
     */
    public function update($data)
    {
        $this->lastSql = "";

        $this->execute();

        return $this;
    }

    /**
     * 查询操作
     */
    public function query()
    {
        $this->lastSql = "";

        $this->execute();

        return $this;
    }

    /**
     * 删除操作
     */
    public function delete()
    {

            $this->lastSql = "DELETE FROM `$this->table_nmae` ";

            if($this->where) $this->lastSql .= " WHERE $this->where ";

            $this->execute();

            return $this;

    }

    /**
     * 执行组装好的sql
     */
    private function execute()
    {
        $this->data = $this->db->exec($this->lastSql);
    }

}