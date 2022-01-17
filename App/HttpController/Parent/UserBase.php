<?php

namespace App\HttpController\Parent;

use App\HttpController\Api\ApiBase;
use App\HttpController\BaseController;
use App\Model\User\UserModel;
use EasySwoole\Http\Message\Status;

// 普通用户基础控制器定义
class UserBase extends BaseController
{
    protected $who;
    //session的cookie头
    protected $sessionKey = 'userSession';
    //白名单
    protected $whiteList = ['login', 'register'];

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
            //白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }
            //获取登入信息
            if (!$data = $this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
                return false;
            }
            //刷新cookie存活
            $this->response()->setCookie($this->sessionKey, $data->userSession, time() + 3600, '/');

            return true;
        }
        return false;
    }

    /**
     * 获取会员用户对象
     * @author LZH
     */
    public function getWho(): ?UserModel
    {
        if ($this->who instanceof UserModel) {
            return $this->who;
        }
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }
        if (empty($sessionKey)) {
            return null;
        }
        $userModel = new UserModel();
        $userModel->userSession = $sessionKey;
        $this->who = $userModel->getOneBySession();
        return $this->who;
    }
}
