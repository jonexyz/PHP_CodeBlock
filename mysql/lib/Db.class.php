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

    public $data;   // 存储lastSql执行执行的结果

    private $debug;   // debug模式：开启调试模式,则直接打印错误信息。关闭调试模式,则错误信息通过错误日志输出。

    private $err; //错误日志操作类

    private function __construct()
    {
        include  __DIR__.'/config.php';

        $dsn = 'mysql:dbname='.DBNAME.';host='.HOST;
        $user = USER;
        $password = PASSWORD;
        $this->debug = DEBUG;
        $this->err = ERR;


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

        try{
            $this->data = $this->db->exec($this->lastSql);

            if ($this->data === false && $this->debug){
                $errMS = $this->db->errorInfo();
                exit( '错误码：'.$errMS[0].'<br/>'.'错误编号：'.$errMS[1].'<br/>'.'错误信息：'.$errMS[2].'<br/>' );

            }elseif( $this->data === false &&  !$this->debug ){
                $errMS = $this->db->errorInfo();
                $str = '错误码：'.$errMS[0].'<br/>'.'错误编号：'.$errMS[1].'<br/>'.'错误信息：'.$errMS[2].'<br/>' ;
                (new $this->err)->test($str);
            }

        }catch (PDOException $e){

            if($this->debug){
                exit('Connection failed: ' . $e->getMessage());
                // 直接打印错误信息,并终止程序执行
            }else{
                // 将错误信息存储到错误日志,并终止执行
            }
        }
    }

}