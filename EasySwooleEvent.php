<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        // 开发者自定义设置 错误级别
        Di::getInstance()->set(SysConst::ERROR_REPORT_LEVEL, E_ALL);

        // 开发者自定义设置 HttpException 全局处理器
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, function ($throwable, Request $request, Response $response) {
            $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
            $response->write(nl2br($throwable->getMessage() . "\n" . $throwable->getTraceAsString()));
            Trigger::getInstance()->throwable($throwable);
        });

        // 开发者自定义设置 onRequest v3.4.x+
        // 实现 onRequest 事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response): bool {

            ###### 对请求进行拦截 ######
            // 不建议在这拦截请求，可增加一个控制器基类进行拦截
//            $requestMsg = $request->getBody()->__toString();
//            Logger::getInstance()->console('接收请求：' . $requestMsg);

            ###### 处理请求的跨域问题 ######
            $response->withHeader('Access-Control-Allow-Origin', '*');
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            if ($request->getMethod() === 'OPTIONS') {
                $response->withStatus(Status::CODE_OK);
                return false;
            }
            return true;
        });

        // 开发者自定义设置 afterRequest v3.4.x+
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_AFTER_REQUEST, function (Request $request, Response $response): void {

            // 示例：获取此次请求响应的内容
//            TrackerManager::getInstance()->getTracker()->endPoint('request');
//            $responseMsg = $response->getBody()->__toString();
//            Logger::getInstance()->console('响应内容:' . $responseMsg);
            // 响应状态码：
            // var_dump($response->getStatusCode());

            // tracker 结束，结束之后，能看到中途设置的参数，调用栈的运行情况
//            TrackerManager::getInstance()->closeTracker();
        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
    }
}
