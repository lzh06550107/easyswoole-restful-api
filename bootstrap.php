<?php

//全局bootstrap事件
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Core;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Config\RedisConfig;

date_default_timezone_set('Asia/Shanghai');

//用户想要执行自己需要的初始化业务代码：如 注册命令行支持、全局通用函数、启动前调用协程 API等功能，就可以在 bootstrap.php 中进行编写实现。
//\EasySwoole\Command\CommandManager::getInstance()->addCommand(new \App\Command\Generate());

//在 bootstrap 事件 中注册自定义命令。
require_once './App/Common/constant.php';
require_once './App/Common/functions.php';

Core::getInstance()->initialize();
// 数据库连接池配置
$config = new \EasySwoole\ORM\Db\Config(Config::getInstance()->getConf('MYSQL'));
$config->setReturnCollection(true);
DbManager::getInstance()->addConnection(new Connection($config));

// Redis连接池配置
$redisConfig = new RedisConfig(Config::getInstance()->getConf('REDIS'));
$poolConfig = new \EasySwoole\Pool\Config();
// 注册连接池管理对象
Manager::getInstance()->register(new \App\Utility\Pool\RedisPool($config, $redisConfig), 'redis');