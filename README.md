# 使用手册

> 智慧校园项目，采用PHP语言、Swoole扩展的常驻内存方式开发，前端支持H5、小程序、手机App。

> 该系统运用了单页面、Websocket、Vue等技术和框架，更具良好体验。

## 新整理的框架需要解决的问题

- 直接在dev.php/produce.php文件中配置，替换在ini中配置；
- 遵循restful接口标准，控制器层添加 ResponseTrait.php 解决每次安装依赖包都需要覆盖Status.php文件问题；
- 增加服务层，解决业务代码复用问题；
- 服务层增加事务管理，解决多表操作导致的数据不一致性；
- 服务层添加 RedisTrait.php 解决在服务层添加缓存；
- 添加 LogTrait.php 统一日志记录问题；

## 技术栈

- PHP7.3
- Swoole4.5
- EasySwoole3.4
- Redis
- Mysql5.7+
- Vue2/Layui

## 目录结构

~~~
easyswoole-restful-api    项目部署目录
├─App                     应用目录
│  ├─Common               通用常量和函数目录
│  ├─Exception            异常类目录
│  ├─HttpController       控制器目录(需要自己创建)
│  │  ├─Admin             Admin模块
│  │  │  ├─V1             接口版本
│  │  ├─Device            设备接口模块，设备提交数据访问的接口层
│  │  │  ├─V1             接口版本
│  ├─Model                数据库模型层
│  ├─Service              服务层，复杂的业务处理写到这一层
│  ├─Task                 异步Task任务处理层
│  ├─Utility              工具层
│  ├─Validate             验证层
├─Bin                     应用bin文件目录
│  ├─app.sh               应用shell脚本
│  ├─easyswoole.service   easyswoole开机启动配置文件
├─Runtime                 运行时目录
│  ├─Log                  日志保存目录
│  ├─Temp                 临时信息、缓存目录
├─UnitTest                单元测试目录
├─vendor                  第三方类库目录
├─.php-cs-fixer.dist.php  php-cs-fixer代码格式规范工具配置文件
├─composer.json           Composer配置文件
├─composer.lock           Composer锁定文件
├─bootstrap.php           预处理或者是预定义
├─EasySwooleEvent.php     框架全局事件
├─easyswoole              框架管理脚本
├─phpunit.php             单元测试入口文件
├─dev.php                 开发配置文件
├─produce.php             生产配置文件
~~~

## 规范

### 基于restful api规范

### 接口响应数据格式

```json
// 基础响应格式【其他格式在此格式上进行扩展】
{
  "code": 200,
  "msg": "操作成功",
}

{
  "code": 500,
  "msg": "操作失败，如服务器内部错误",
}

// 对象格式【对象数据存在】【对象数据不存在，使用基础响应格式】
{
  "code": 200,
  "msg": "操作成功",
  "data": {
    "id": 1,
    "name": "小伟",
    "age": 20,
    "sex": 0
  }
}

// 数组格式【数组可以对象数组，字符数组】【数组不存在，使用基础响应格式】
{
  "code": 200,
  "msg": "操作成功",
  "data": [
    {
      "id": 1,
      "name": "小王",
      "age": 10
    },
    {
      "id": 1,
      "name": "小王",
      "age": 10
    }
  ]
}

// 分页列表格式【分页结果】【分页数据不存在，使用基础响应格式】
{
  "code":200,
  "msg":"操作成功",
  "data":{
    "page":1,
    "totalPage":10,
    "list":[
      {
        "id":1,
        "name":"小王",
        "age":10
      },
      {
        "id":1,
        "name":"小王",
        "age":10
      }
    ]
  }
}

```

## 包含的功能


## 测试

1. Unit Testing 单元测试。测试核心的功能，该组件是孤立的。
2. Integration Testing 集成测试。测试与其它组件之间的交互是否正确
3. Functional testing 功能测试。

* `php easyswoole phpunit tests` 执行所有测试代码
* `php easyswoole phpunit tests/DbTest.php` 执行指定测试代码
* `php easyswoole phpunit UnitTest/database/builder/BuilderTest.php --filter testXxx` 指定指定类的指定方法


https://github.com/aisuhua/phpunit-demo/demo05/

composer dump-autoload
