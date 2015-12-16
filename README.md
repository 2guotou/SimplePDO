# SimplePDO

简单的 PHP/MysqlPDO 封装，依赖 Yaf 框架

**步骤**

1 将 application.ini 合并进你自己的 application.ini 中；
2 创建自己的Model类，譬如数据表Sample，则类名设定为SampleModel；
3 调用，如下代码示例 -- 获取Sample表全部数据；

```php
Class IndexController extends Yaf_Controller_Abstract{
	
	function sampleAction(){
		$data = SampleModel::instance()->getAll();
		var_dump($data);
	}
}
```