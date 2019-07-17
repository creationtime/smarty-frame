<?php
namespace Sf\Db;

use PDO;
use Sf\Base\Component;

/**
 * 连接类
 * 通过[pdo]（php.net/manual/en/book.pdo.php）与数据库的连接。
 */
class Connection extends Component
{
    /**
     * @var string 数据源名称或DSN包含连接到数据库所需的信息。
     * 请参阅上的[php手册]（http://www.php.net/manual/en/function.pdo-construct.php）DSN字符串的格式。
     * @see 字符集
     */
    public $dsn;

    /**
     * @var string 用于建立数据库连接的用户名。默认为“空”，表示没有要使用的用户名。
     */
    public $username;

    /**
     * @var string 建立数据库连接的密码。默认为“空”，表示没有要使用的密码。
     */
    public $password;

    /**
     * @var array PDO attributes (name => value)
     * 建立数据库连接。请参阅[PHP手册]（http://www.php.net/manual/en/function.pdo-setattribute.php）用于有关可用属性的详细信息。
     */
    public $attributes;

    public function getDb()
    {
        $attributes = [
            PDO::ATTR_EMULATE_PREPARES => false,//提取的时候将数值转换为字符串。 需要 bool 类型。
            PDO::ATTR_STRINGIFY_FETCHES => false,//启用或禁用预处理语句的模拟。 有些驱动不支持或有限度地支持本地预处理。使用此设置强制PDO总是模拟预处理语句（如果为 TRUE ），或试着使用本地预处理语句（如果为 FALSE）。如果驱动不能成功预处理当前查询，它将总是回到模拟预处理语句上。 需要 bool 类型。
    ];

        return new PDO($this->dsn, $this->username, $this->password, $attributes);
    }
}