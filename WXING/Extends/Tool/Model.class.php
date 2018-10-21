<?php
class Model {
    // 保存连接信息(静态变量，程序在一次运行结束之前都不会释放空间；普通变量，随着对象消失而释放)
    public static $link = null;
    // 保存表名
    protected $table = null;
    // 初始化表信息
    private $opt;
    // 记录发送的sql
    public static $sqls = array();


    public function __construct($table=null) {
        $this->table = is_null($table) ? C('DB_PREFIX').$this->table : C('DB_PREFIX').$table;
        $this->_connect();
        // 初始化表信息
        $this->_opt();
    }

    // 原生sql查询,find方法和findAll方法最终都是调用query方法，这个query方法最终调用原生sql的query方法
    public function query($sql) {
        self::$sqls[] = $sql;
        $link = self::$link;
        $result = $link->query($sql);
        if($link->errno) halt('mysql错误: '. $link->error . '<br>Sql: '.$sql);
        $rows = array();
        while ($row = $result->fetch_assoc()) {  // 一行行取出数据
            $rows[] = $row;
        }
        $result->free();
        $this->_opt(); //重新初始化
        return $rows;
    }

    // all方法，返回二维数组
    public function all() {
        $sql = 'SELECT ' . $this->opt['field'] . ' FROM ' .
            $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] .
            $this->opt['order'] . $this->opt['limit'];
        return $this->query($sql);
    }

    /**
     * all方法的别名
     * @return array
     */
    public function findAll() {
        return $this->all();
    }

    /**
     * 返回一维数组
     * @return mixed
     */
    public function find() {
        $data = $this->limit(1)->all();  // 前面的this对象可能在其他程序中被调用过 而赋予了属性opt的值
        return current($data);
    }

    /**
     * find方法的别名
     * @return mixed
     */
    public function one() {
        return $this->find();
    }


    /**
     * 初始化
     */
    private function _opt() {
        $this->opt = array(
            'field' => '*',
            'where' => '',
            'group' => '',
            'having' => '',
            'order' => '',
            'limit' => '',
        );
    }

    // 以下为链式方法的实现：① 赋予opt数组对应的值  ② 返回当前对象
    public function field($field) {
        $this->opt['field'] = $field;
        return $this;
    }

    public function where($where) { // 外部调用时实际上是利用这些方法给opt属性赋值，最终给all方法组合使用
        $this->opt['where'] = ' WHERE ' . $where;
        return $this;
    }

    public function group($group) {
        $this->opt['group'] = ' GROUP BY ' . $group;
        return $this;
    }

    public function having($having) {
        $this->opt['having'] = ' HAVING ' . $having;
        return $this;
    }

    public function order($order) {
        $this->opt['order'] = ' ORDER BY ' . $order;
        return $this;
    }

    public function limit($limit) {
        $this->opt['limit'] = ' LIMIT ' . $limit;
        return $this;
    }


    private function _connect() {
        if(is_null(self::$link)) {
            if(empty(C('DB_DATABASE')))   halt('请先设置数据库');
            $link = new mysqli(C('DB_HOST'), C('DB_USER'), C('DB_PASSWORD'), C('DB_DATABASE'), C('DB_PORT'));
            if($link->connect_error) halt('数据库连接错误，请检查数据库配置');
            $link->set_charset(C('DB_CHARSET'));
            self::$link = $link;
        }
    }

    public function exe($sql) {
        self::$sqls[] = $sql;
        $link = self::$link;
        $bool = $link->query($sql);
        $this->_opt();  // 重置语句构造参数
        if(is_object($bool)) {
            halt('sql为查询语句，请用query方法');
        }
        if($bool) {
            return $link->insert_id ? $link->insert_id : $link->affected_rows; // 插入 或者 删改 语句返回不同
        }else {
            halt('mysql错误：' . $link->error . '<br>SQL：' . $sql);
        }
    }

    public function delete() {
        if(empty($this->opt['where'])) halt('删除方法必须有where条件');
        $sql = "DELETE FROM " . $this->table . $this->opt['where'];
        return $this->exe($sql);
    }

    /**
     * 安全过滤函数，用于添加和修改操作
     * @param $str
     * @return mixed
     */
    private function _safe_str($str) {
        if(get_magic_quotes_gpc()) $str = stripcslashes($str);
        return self::$link->real_escape_string($str);
    }

    public function add($data=null) {
        if(is_null($data)) $data = $_POST; // 没传data数组的时候
        $fields = '';
        $values = '';
        foreach ($data as $f => $v) {
            $fields .= "`" . $this->_safe_str($f) . "`,";
            $values .= "'" . $this->_safe_str($v) . "',";
        }
        $fields = rtrim($fields, ',');
        $values = rtrim($values, ',');
        $sql = 'INSERT INTO ' . $this->table . '(' . $fields . ') VALUES (' . $values . ')';
        return $this->exe($sql);
    }

    public function update($data=null) {
        if(empty($this->opt['where'])) halt('更新sql必须要有where条件');
        if(is_null($data)) $data = $_POST; // 没传data数组的时候
        $values = '';
        foreach ($data as $f => $v) {
            $values .= "`" . $this->_safe_str($f) . "`='" . $this->_safe_str($v) . "',";
        }
        $values = rtrim($values, ',');
        $sql = "UPDATE " . $this->table . " SET " . $values . $this->opt['where'];
        return $this->exe($sql);
    }



}

