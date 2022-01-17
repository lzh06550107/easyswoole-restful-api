<?php
/**
 * 文件描述
 * Created on 2021/8/10 14:55
 * Create by LZH
 */

namespace App\HttpController\Admin\V1;

use Swoole\Http\Status;

class Level extends AdminBase
{
    /**
     * 获取职级列表
     */
    public function getLevelList()
    {
        $result = $this->service->getLevelList();
        return $this->writeJson(Status::OK, $result);
    }
}
