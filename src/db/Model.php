<?php
namespace Sf\Db;

use PDO;
use PHPMailer\PHPMailer\Exception;
use Sf;
use Sf\Base\Error;

/**
 * 数据模型的基类
 */
class Model implements ModelInterface
{

    /**
     * @var $pdo PDO实例
     */
    public static $pdo;
    public static $error;//保存error
    public static $sql;//保存sql

    /**
     * 获取PDO实例
     * 用static变量可以保证所有继承该Model的类用的都是同一个PDO实例
     * getDb方法实现了单例模式（其中的配置暂时hard在这里，在之后的博客里会抽出来），保证了一个请求中，使用getDb只会取到一个PDO实例。
     * @return PDO
     */
    public static function getDb()
    {
        if (empty(static::$pdo)) {
            /*调用创建对象类，进行数据库连接，优先级是先子再公共*/
            $path = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/'.'db.php';
            if(file_exists($path)){
                static::$pdo = Sf::createObject2($path)->getDb();
            }else{
                static::$pdo = Sf::createObject('db')->getDb();
            }

            /*pdo默认为静默模式，错就错，不报错.故须以下调整报错方式，并且在php中须使用try catch进行异常处理*/
            static::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            static::$pdo->exec("set names 'utf8'");
        }

        return static::$pdo;
    }

    /**
     * 声明与此模型类关联的数据库表的名称。
     * @return string 表名
     */
    public static function tableName()
    {
        return get_called_class();
    }

    /**
     * 返回此模型类的主键**名称**。
     * @return string[] 主要关键的名字（S）为这类模型。
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * 通过主键或列值数组返回单个模型实例。
     *
     * // 找到第一个年龄为30岁、状态为1的客户
     * $customer = Customer::findOne(['age' => 30, 'status' => 1]);
     *
     * @param mixed $condition 一组列值
     * @return static|null 模型实例与条件匹配，如果没有匹配，则为空。
     */
    public static function findOne($condition)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);

        if ($rs) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                return static::arr2Model($row);
            }
        }

        return null;
    }

    /**
     * 返回与指定的主键值或一组列值匹配的模型列表。
     *
     * // 找到第一个年龄为30岁、状态为1的客户
     * $customers = Customer::findAll(['age' => 30, 'status' => 1]);
     *
     * @param mixed $condition 一组列值
     * @return array 模型实例的数组，如果没有匹配的，则为空数组。
     */
    public static function findAll($condition)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);
        $models = [];

        if ($rs) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (!empty($row)) {
                    $model = static::arr2Model($row);
                    array_push($models, $model);
                }
            }
        }

        return $models;
    }

    /**
     * 构建SQL Where部分
     * @param mixed $condition a set of column values
     * @return string
     */
    public static function buildWhere($condition, $params = null)
    {
        if (is_null($params)) {
            $params = [];
        }

        $where = '';
        if (!empty($condition)) {
            $where .= ' where ';
            $keys = [];
            foreach ($condition as $key => $value) {
                array_push($keys, "$key = ?");
                array_push($params, $value);
            }
            $where .= implode(' and ', $keys);
        }
        return [$where, $params];
    }

    /**
     * 将数组转换为模型
     * @param  mixed $row 数据库中的行数据
     */
    public static function arr2Model($row)
    {
        $model = new static();
        foreach ($row as $rowKey => $rowValue) {
            $model->$rowKey = $rowValue;
        }
        return $model;
    }

    /**
     * 使用提供的属性值和条件更新模型。
     * 例如，要将状态为1的所有客户的状态更改为2：
     *
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     *
     * @param array $attributes 要为模型保存的属性值（名称-值对）。
     * @param array $condition 与应更新的模型匹配的条件。
     * 空条件将匹配所有模型。
     * @return integer 更新的行数
     */
    public static function updateAll($condition, $attributes)
    {
        $sql = 'update ' . static::tableName();
        $params = [];

        if (!empty($attributes)) {
            $sql .= ' set ';
            $params = array_values($attributes);
            $keys = [];
            foreach ($attributes as $key => $value) {
                array_push($keys, "$key = ?");
            }
            $sql .= implode(' , ', $keys);
        }

        list($where, $params) = static::buildWhere($condition, $params);
        $sql .= $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            // 获取更新的行数
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }

    /**
     * 使用提供的条件删除模型。
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * 例如，要删除状态为3的所有客户：
     *
     * Customer::deleteAll([status = 3]);
     *
     * @param array $condition 与应删除的模型匹配的条件。
     * 空条件将匹配所有模型。
     * @return integer 删除的行数
     */
    public static function deleteAll($condition)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'delete from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            // 获取删除的行数
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }

    /**
     * 使用此记录的属性值将模型插入数据库。
     *
     * 使用实例
     *
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     *
     * @return boolean 模型是否插入成功。
     */
    public function insert()
    {
        $sql = 'insert into ' . static::tableName();
        $params = [];
        $keys = [];
        foreach ($this as $key => $value) {
            array_push($keys, $key);
            array_push($params, $value);
        }
        // 构建由？组成的数组，其个数与参数相等数相同
        $holders = array_fill(0, count($keys), '?');
        $sql .= ' (' . implode(' , ', $keys) . ') values ( ' . implode(' , ', $holders) . ')';

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        // 将一些自增值赋回Model中
        $primaryKeys = static::primaryKey();
        foreach ($primaryKeys as $name) {
            // Get the primary key
            $lastId = static::getDb()->lastInsertId($name);
            $this->$name = (int) $lastId;
        }
        return $execResult;
    }

    /**
     * 将对此模型的更改保存到数据库中。
     *
     * 使用试例
     *
     * $customer = Customer::findOne(['id' => $id]);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     *
     * @return integer|boolean 受影响的行数。
     * 请注意，即使更新执行成功，受影响的行数也可能是0。
     */
    public function update()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        $attributes = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $primaryKeys, true)) {
                $attributes[$key] = $value;
            }
        }

        return static::updateAll($condition, $attributes) !== false;
    }

    /**
     * 从数据库中删除模型。
     *
     * @return integer|boolean 删除的行数
     * 请注意，删除的行数可能为0，即使删除执行成功。
     */
    public function delete()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        return static::deleteAll($condition) !== false;
    }

    /**
     * 使用原生sql，获取数据
     * @param $sql
     * @return array|null
     */
    public static function selectSql($sql){
        self::$sql = $sql;
        $stmt = static::getDb()->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data ?? null;
    }

    /**
     * 使用原生sql，获取数据
     * @param $sql
     * @return array|null
     */
    public static function selectRowSql($sql){
        self::$sql = $sql;
        $stmt = static::getDb()->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

        return $data ?? null;
    }

    /**
     * 使用原生sql，插入数据
     * @param $sql
     * @return bool
     */
    public static function insertSql($sql){
        self::$sql = $sql;
        $sql = self::fanXSS($sql);
        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute();dd($execResult);
        if(!$execResult){
            return false;
        }
        $id = self::selectSql("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()'];
        return $id;
    }

    /**
     * 返回insert最新的自增id
     * @return mixed
     */
    public static function lastInsertId(){
        return self::selectSql("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()'];;
    }

    /**
     * 使用原生sql，插入数据
     * @param $sql
     * @return bool
     */
    public static function insertSql_arr($table,$row){
        if(count($row) != count($row,1)){
            Error::error('insertSql_arr data is many dimensions array');
        }
        $keys = [];
        foreach (array_keys($row) as $key) {
            $keys[] = $key;
        }
        $columnCount = count($row);
        if ($columnCount > 0) {
            foreach ($row as $v) {
                $values[] = $v;
            }
        } else {
            Error::error('insertSql_arr data is null');
        }
        $keys = implode(',',$keys);
        $values = implode('\',\'',$values);
        $sql = "INSERT INTO $table ($keys) VALUES ('$values')";
//        var_dump($sql);
        $sql = self::fanXSS($sql);
        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute();
        if(!$execResult){
            return false;
        }
        $id = self::selectSql("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()'];
        return $id;
    }

    /**
     * 使用原生sql，修改数据
     * @param $sql
     * @return bool
     */
    public static function updateSql($sql){
        self::$sql = $sql;
        $sql = self::fanXSS($sql);
        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute();
        return $execResult;
    }

    /**
     * 使用原生sql，删除数据
     * @param $sql
     * @return bool
     */
    public static function deleteSql($sql){
        self::$sql = $sql;
        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute();
        return $execResult;
    }

    /**
     * 开启事务操作
     * @return bool
     */
    public static function startTransaction(){
        return static::getDb()->beginTransaction();
    }

    /**
     * 事务回滚操作
     * @return bool
     */
    public static function rollBack(){
        return static::getDb()->rollBack();
    }

    /**
     * 事务提交操作
     * @return bool
     */
    public static function commit(){
        return static::getDb()->commit();
    }

    public static function fanXSS($sql){
        self::$sql = $sql;
        return fanXSS($sql);
    }

    /**
     * 获取error
     * @return mixed
     */
    public static function getError(){
        return self::$error;
    }

    /**
     * 获取sql
     * @return mixed
     */
    public static function getSql(){
        return self::$sql;
    }
}