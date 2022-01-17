<?php

namespace App\HttpController\Admin\V1;

use Swoole\Http\Status;
use function upload_file;
use function upload_image;
use const IMG_URL;

/**
 * 上传文件
 */
class Upload extends AdminBase
{
    /**
     * 上传图片（支持多图片上传）
     * 备注：1、单文件：file
     *      2、多文件：file[],file[]
     */
    public function uploadImage()
    {
        // 错误提示语
        $error = "";
        // 上传图片
        $result = upload_image('file', '', $error);
        if (!$result) {
            return $this->writeJson(Status::BAD_REQUEST, null, $error);
        }
        // 多图片上传处理
        $list = [];
        if (is_array($result)) {
            foreach ($result as $val) {
                $list[] = IMG_URL . $val;
            }
        } else {
            $list = IMG_URL . $result;
        }
        return $this->writeJson(Status::OK, $list);
    }

    /**
     * 上传文件(支持多文件上传)
     * 备注：1、单文件：file
     *      2、多文件：file[],file[]
     */
    public function uploadFile()
    {
        $error = "";
        // 上传文件(非图片)
        $result = upload_file('file', '', $error);
        if (!$result) {
            return $this->writeJson(Status::BAD_REQUEST, null, $error);
        }
        return $this->writeJson(Status::OK, $result);
    }
}
