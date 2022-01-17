<?php

namespace App\HttpController\Admin\V1;

use EasySwoole\Http\Message\Status;

/**
 * 后台会员管理控制器，后台管理员登录之后，可通过此文件的接口，去进行会员的增删改查操作 (即 CURD)
 */
class Admin extends AdminBase
{

    /**
     * 重置密码
     */
    public function resetPwd()
    {
        $result = $this->service->resetPwd();
        if (is_bool($result) && $result) {
            return $this->writeJson(Status::CODE_OK, null, '操作成功');
        } elseif (is_bool($result) && !$result) {
            return $this->writeJson(Status::CODE_EXPECTATION_FAILED, null, '操作失败');
        }
        return $this->writeJson(Status::CODE_EXPECTATION_FAILED, $result, '操作失败');
    }
}
