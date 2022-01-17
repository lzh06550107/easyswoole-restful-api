<?php
/**
 * 文件描述
 * Created on 2021/8/10 15:05
 * Create by LZH
 */

namespace App\HttpController\Admin\V1;

use Swoole\Http\Status;

class Position extends AdminBase
{

    /**
     * 获取岗位列表
     */
    public function getPositionList()
    {
        $result = $this->service->getPositionList();
        return $this->writeJson(Status::OK, $result);
    }


}