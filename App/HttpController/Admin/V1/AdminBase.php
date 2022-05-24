<?php

declare(strict_types=1);

namespace App\HttpController\Admin\V1;

use App\HttpController\BaseController;
use App\Model\Admin\AdminModel;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

// 后台管理基础控制器定义
class AdminBase extends BaseController
{
    //对象池模式只重置非静态 public 属性，public属性在返回池中的时候清除
    public $who;

    protected $service;

    //白名单
    protected $whiteList = [];

    /**
     * onRequest
     * @param null|string $action
     * @return bool|null
     * @throws \Throwable
     * @author LZH
     */
    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            //白名单判断，即不需要登录就可访问
            if (in_array($action, $this->whiteList)) {
                return true;
            }
            //获取登入信息
            if (!$this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '用户未登录');
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * getWho
     * @return bool
     * @author LZH
     */
    public function getWho(): ?AdminModel
    {
        if ($this->who instanceof AdminModel) {
            return $this->who;
        }

        $token = $this->request()->getHeader("authorization")[0];

        if ($token && strpos($token, 'Bearer ') !== false) {
            $token = str_replace("Bearer ", '', $token);
            $jwtObject = parse_token($token);

            if ($jwtObject->getStatus() == -2) { // 如果过期，则返回跳转到登录界面提示
                return $this->writeJson(Status::CODE_BAD_REQUEST, [], 'token已过期');
            } elseif ($jwtObject->getStatus() == -1) { // 如果无效，则返回无效提示
                return $this->writeJson(Status::CODE_BAD_REQUEST, [], 'token无效');
            }

            $this->who = AdminModel::create()->get($jwtObject->getData()); // token自定义数据是用户id
            ContextManager::getInstance()->set('user', $this->who);
            return $this->who;
        }
        return null;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        return null;
        // TODO: Implement getValidateRule() method.
    }
}
