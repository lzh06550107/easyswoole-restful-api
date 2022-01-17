<?php

declare(strict_types=1);

namespace App\HttpController;

use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;

// 全局基础控制器定义
class BaseController extends AnnotationController
{
    // 找不到控制器默认会调用这个方法
    public function index()
    {
        $this->actionNotFound('index');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->writeJson(Status::CODE_NOT_FOUND, [], 'action is miss');
    }

    public function onRequest(?string $action): ?bool
    {
        // 解决easyswoole不能解析application/json格式的post提交
        if ($this->request()->getHeader('content-type')[0] == 'application/json;charset=UTF-8') { //根据内容类型来转换参数

            $json = $this->request()->getBody()->__toString();
            $json = $json ? json_decode($json, true) : [];
            $this->request()->withParsedBody($json);
        }

        if (!parent::onRequest($action)) {
            return false;
        };

        return true;
    }

    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的标头名称
     * @return string
     */
    protected function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeader($headerName);
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) {
                    $clientAddress = $list[0];
                }
            }
        }
        return $clientAddress;
    }

    /**
     * 获取请求参数
     * @param string $name 参数名称
     * @param null $default 参数默认值
     * @return array|mixed|object|null
     */
    protected function input($name, $default = null)
    {
        $value = $this->request()->getRequestParam($name);
        return $value ?? $default;
    }

    /**
     * 拦截异常
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ParamValidateError) { // 参数验证异常
            $msg = $throwable->getValidate()->getError()->getErrorRuleMsg();
            $this->writeJson(Status::CODE_BAD_REQUEST, null, "{$msg}");
        } else {
            if (Core::getInstance()->runMode() == 'dev') {
                $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getFile(). ':' .$throwable->getLine().':'.$throwable->getMessage());
//                print_r($throwable->getTrace());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, '系统内部错误，请稍后重试');
            }
        }
    }
}
