<?php
/**
 * 文件描述
 * Created on 2021/8/10 14:45
 * Create by LZH
 */

namespace App\HttpController\Admin\V1;

use Swoole\Http\Status;

class Role extends AdminBase
{
    /**
     * 获取角色列表
     */
    public function getRoleList()
    {
        $result = $this->service->getRoleList();
        return $this->writeJson(Status::OK, $result);
    }
}
