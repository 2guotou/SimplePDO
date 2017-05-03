# SimplePDO

简单的 PHP/MysqlPDO 封装，依赖 Yaf 框架

### 步骤

1. 将 application.ini 合并进你自己的 application.ini 中；
2. 创建自己的Model类，譬如数据表Sample，则类名设定为SampleModel；
3. 调用，如下代码示例 -- 获取Sample表全部数据；

```php
include './library/SimplePDO.php';

New SimplePDO("mysql:host=127.0.0.1;port=3306;dbname=test;","user","pass");
$tables = SimplePDO::instance()->doQuery("show tables;");
print_r($table);

/* output: Array (
    [0] => Array (
        [Tables_in_ad] => table1
    )
) */
```
