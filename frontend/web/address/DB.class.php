<?php

class DB{
    private $host;//主机
    private $user;//用户名
    private $password;//密码
    private $dbname;//数据库名
    private $port;//端口
    private $charset;//编码
    private $link;//保存数据库连接

    private static $instance;//私有的静态成员变量

    /**
     * 初始化 属性 连接
     * DB constructor.
     * @param $host
     * @param $user
     * @param $password
     * @param $dbname
     * @param $port
     * @param $charset
     */
    private function __construct($config)
    {
        $this->host = isset($config['host']) ? $config['host']:'127.0.0.1';
        $this->user = isset($config['user']) ? $config['user'] : 'root';
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];
        $this->port = isset($config['port']) ? $config['port']:3306;
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';

        //初始化连接数控,设置编码
        $this->connect();
        $this->setCharset();
    }
    //私有的克隆方法
    private function __clone()
    {
    }

    /**
     * 创建对象的方法
     * @param $host
     * @param $user
     * @param $password
     * @param $dbname
     * @param $port
     * @param $charset
     * @return DB
     */
    public static function getInstance($config){
        //如果存在就不创建,直接返回,如果不存在则创建 第一种
//        if(self::$instance==null){
//            self::$instance = new DB($host,$user,$password,$dbname,$port,$charset);
//        }

//        if(!self::$instance instanceof DB){//第二种
//            self::$instance = new DB($host,$user,$password,$dbname,$port,$charset);
//        }
        //self 代表自己 本身
        if(!self::$instance instanceof self){//第二种
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 该方法专门用于连数据库
     */
    private function connect(){
        //连接数据库
        $this->link = mysqli_connect($this->host,$this->user,$this->password,$this->dbname,$this->port);
        if($this->link === false){
            exit(
                "连接数据库失败!<br/>".
                "错误信息:".mysqli_connect_error()."<br/>".
                "错误编码:".mysqli_connect_errno()
            );
        }
    }
    /**
     * 该方法专门用于设置编码
     */
    private function setCharset(){
        //设置编码
        $result = mysqli_query($this->link,'set names '.$this->charset);
        if($result === false){
            exit(
                "设置编码失败!<br/>".
                "错误信息:".mysqli_error($this->link)."<br/>".
                "错误编码:".mysqli_errno($this->link)
            );
        }
    }
    /**
     * 该方法 用于执行sql 返回 结果
     * @param $sql
     */
    public function query($sql){
        //方法体,把之前面向过程的代码放在这里
        //执行sql
        $result = mysqli_query($this->link,$sql);
        if($result === false){
            exit(
                "执行sql失败!<br/>".
                "错误信息:".mysqli_error($this->link)."<br/>".
                "错误编码:".mysqli_errno($this->link)."<br/>".
                "SQL语句:".$sql
            );
        }
        //返回结果
        return $result;
    }

    /**
     * 该方法 专门用于执行sql语句,返回一个二维数组
     * @param $sql 传入的参数 sql语句
     * @return array|null
     */
    public function fetchAll($sql){
        //1.执行sql
        $result = $this->query($sql);
        //2.返回结果 二维数组
        return mysqli_fetch_all($result,MYSQLI_ASSOC);
    }

    /**
     * 该方法 专门用于执行sql语句,返回 一维数组
     * @param $sql
     * @return array|[]
     */
    public function fetchRow($sql){
        /*        //1.执行sql
                    $result = $this->query($sql);
                //2.返回 一维数组
                    return mysqli_fetch_assoc($result);*/
        //1.执行sql
        $rows = $this->fetchAll($sql);
        //2.返回 一维数组
        return empty($rows) ? null:$rows[0];

    }

    /**
     * 该方法专门用于执行sql,返回第一行第一列
     * @param $sql
     */
    public function fetchColumn($sql){
        /*        //1.执行sql
                    $result = $this->query($sql);
                //2.返回第一行第一列的值
                    $row = mysqli_fetch_row($result);
                    return $row[0];*/
        //1.执行sql
        $row = $this->fetchRow($sql);
        //2.返回第一行第一列的值
        return empty($row) ? null:array_values($row)[0];
    }

    /**
     * 析构方法
     */
    public function __destruct()
    {
        //关闭数据库连接
        mysqli_close($this->link);
    }

    /**
     * 在对象序列化的时候被调用,返回需要被序列化的非静态属性
     */
    public function __sleep()
    {
        return ['host','user','password','dbname','port','charset'];
    }

    /**
     * 该方法在对象 被 反 序列化 的时候自动调用执行,用于重新初始化操作
     */
    public function __wakeup()
    {
        //初始化连接数控,设置编码
        $this->connect();
        $this->setCharset();
    }

    public function sql_escape($param){
        return mysqli_real_escape_string($this->link,$param);
    }
}