<?php
/**
 * 文件描述
 * Created on 2021/8/9 18:42
 * Create by LZH
 */

namespace App\HttpController\Admin\V1;

use App\Service\Admin\AdminService;
use App\Service\Admin\MenuService;
use EasySwoole\Http\Message\Status;

class Index extends AdminBase
{
    /**
     * 获取菜单列表
     */
    public function getMenuList()
    {
        $menuService = new MenuService();
        $result = $menuService->getPermissionList($this->who->id);
        return $this->writeJson(Status::CODE_OK, $result);
    }

    /**
     * 获取管理员详细信息
     */
    public function getUserInfo()
    {
        $adminService = new AdminService();
        $result = $adminService->getUserInfo($this->who->id);
        return $this->writeJson(Status::CODE_OK, $result, 'success');
    }
}
