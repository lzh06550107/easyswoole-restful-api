<?php

declare(strict_types=1);

/**
 * 这里定义全局通用函数
 */

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\Request;
use EasySwoole\Jwt\Jwt;
use EasySwoole\Validate\Validate;

if (!function_exists('get_image_url')) {

    /**
     * 获取网络图片地址
     * @param string $image_url 图片地址
     * @return string 输出网络图片地址
     */
    function get_image_url($image_url)
    {
        return IMG_URL . $image_url;
    }
}

if (!function_exists('upload_image')) {

    /**
     * 上传单张图片
     * @param string $form_name 文件表单名
     * @param string $save_dir 保存文件夹名
     * @param string $error 错误信息
     */
    function upload_image($form_name = 'file', $save_dir = "", &$error = '')
    {
        // 获取文件对象
        /* @var Request $request */
        $request = ContextManager::getInstance()->get('request');
        $files = $request->getUploadedFile($form_name);
        // 判断是否有上传的文件
        if (!$files) {
            $error = "请选择图片";
            return false;
        }

        try {
            // 允许上传的后缀
            $allowext = ['gif','GIF','jpg','JPG','jpeg','JPEG','png','PNG','bmp','BMP'];
            // 上传路径
            $save_dir = empty($save_dir) ? 'temp' : $save_dir;
            $validate = new Validate();
            if (is_array($files)) {
                $data = [];
                /** @var \EasySwoole\Http\Message\UploadFile $file */
                foreach ($files as $file) {
                    $validate->addColumn('file')->allowFile($allowext)->func(function () use ($file) {
                        return $file->getSize() <= 10 * 1024 * 1024;
                    }, '文件大小限制为4M');
                    $bool = $validate->validate(['file' => $file]);
                    if ($bool) {
                        // 上传到本地服务器
                        [$filename, $ext] = explode('.', $files->getClientFilename());
                        $filename = $save_dir.'/'.date("Ymd").'/'.substr(md5(time() . rand(0, 999999)), 8, 16) . rand(100, 999).'.'.$ext;
                        $file->moveTo(ATTACHMENT_PATH . '/' . $filename);
                        if ($filename) {
                            // 拼接路径
                            $path = str_replace('\\', '/', '/' . $filename);
//                        $data[] = [
//                            'filepath' => $path,
//                            'filename' => $file->getOriginalName(),
//                            'fileext' => $file->extension(),
//                            'filesize' => $file->getSize(),
//                        ];
                            $data[] = $path;
                        }
                    }
                }
                return $data;
            } else {
                // 使用验证器验证上传的文件
                $validate->addColumn('file')->allowFile($allowext, false, '文件格式错误')->func(function () use ($files) {
                    return $files->getSize() <= 10 * 1024 * 1024;
                }, '文件大小限制为4M');

                $bool = $validate->validate(['file' => $files]);

                if ($bool) {
                    // 上传到本地服务器
                    [$filename, $ext] = explode('.', $files->getClientFilename());
                    $filename = $save_dir.'/'.date("Ymd").'/'.substr(md5(time() . rand(0, 999999)), 8, 16) . rand(100, 999).'.'.$ext;
                    /** @var \EasySwoole\Http\Message\UploadFile $files */
                    $files->moveTo(ATTACHMENT_PATH . '/' . $filename);

                    if ($filename) {
                        // 拼接路径
                        $path = str_replace('\\', '/', '/' . $filename);
//                    $data = [
//                        'filepath' => $path,
//                        'filename' => $files->getOriginalName(),
//                        'fileext' => $files->extension(),
//                        'filesize' => $files->getSize(),
//                    ];
                        return $path;
                    }
                }
            }
        } catch (Exception $e) {
            // 上传异常
            $error = $e->getMessage();
        }
        return false;
    }
}

if (!function_exists('upload_file')) {

    /**
     * 上传单个文件
     * @param string $form_name 文件表单名
     * @param string $save_dir 存储文件夹名
     * @param string $error 错误信息
     */
    function upload_file($form_name = 'file', $save_dir = "", &$error = '')
    {
        // 获取文件对象
        /* @var Request $request */
        $request = ContextManager::getInstance()->get('request');
        $files = $request->getUploadedFile($form_name);
        // 判断是否有上传的文件
        if (!$files) {
            $error = "请选择文件";
            return false;
        }

        try {
            // 允许上传的后缀
            $allowext = 'xls,xlsx,doc,docx,ppt,pptx,zip,rar,mp3,txt,pdf,sql,js,css,chm,';
            // 上传路径
            $save_dir = empty($save_dir) ? 'temp' : $save_dir;
            $validate = new Validate();
            if (is_array($files)) {
                $data = [];
                foreach ($files as $file) {
                    // 使用验证器验证上传的文件

                    $validate->addColumn('file')->allowFile([$allowext])->func(function () use ($file) {
                        return $file->getSize() <= 10 * 1024 * 1024;
                    }, '文件大小限制为4M');

                    $bool = $validate->validate(['file' => $file]);

                    if ($bool) {
                        [$filename, $ext] = explode('.', $files->getClientFilename());
                        $filename = $save_dir.'/'.date("Ymd").'/'.substr(md5(time() . rand(0, 999999)), 8, 16) . rand(100, 999).'.'.$ext;
                        // 上传到本地服务器
                        $file->moveTo($filename);
                        if ($filename) {
                            // 拼接路径
                            $path = str_replace('\\', '/', '/' . $filename);
                            $data[] = [
                                    'fileName' => $file->getOriginalName(),
                                    'filePath' => $path,
                            ];
                        }
                    }
                }
                return $data;
            } else {
                // 使用验证器验证上传的文件
                $validate->addColumn('file')->allowFile([$allowext])->func(function () use ($files) {
                    return $files->getSize() <= 10 * 1024 * 1024;
                }, '文件大小限制为4M');

                $bool = $validate->validate(['file' => $files]);

                if ($bool) {
                    [$filename, $ext] = explode('.', $files->getClientFilename());
                    $filename = $save_dir.'/'.date("Ymd").'/'.substr(md5(time() . rand(0, 999999)), 8, 16) . rand(100, 999).'.'.$ext;
                    // 上传到本地服务器
                    $files->moveTo($filename);
                    if ($filename) {
                        // 拼接路径
                        $path = str_replace('\\', '/', '/' . $filename);
                        $result = [
                            'fileName' => $files->getOriginalName(),
                            'filePath' => $path,
                        ];
                        return $result;
                    }
                }
            }
        } catch (Exception $e) {
            // 上传异常
            $error = $e->getMessage();
        }
    }
}

if (!function_exists('save_image')) {

    /**
     * 保存图片
     * @param string $img_url 网络图片地址
     * @param string $save_dir 图片保存目录
     * @return string 返回路径
     */
    function save_image($img_url, $save_dir = '/')
    {
        if (!$img_url) {
            return false;
        }
        $save_dir = trim($save_dir, "/");
        $imgExt = pathinfo($img_url, PATHINFO_EXTENSION); // 文件后缀
        // 是否是本站图片
        if (strpos($img_url, IMG_URL) !== false) {
            // 是否是临时文件
            if (strpos($img_url, 'temp') === false) {
                return str_replace(IMG_URL, "", $img_url);
            }
            $new_path = create_image_path($save_dir, $imgExt);
            $old_path = ATTACHMENT_PATH .  str_replace(IMG_URL, '', $img_url);
            if (!file_exists($old_path)) {
                return false;
            }
            rename($old_path, ATTACHMENT_PATH . $new_path);
            return $new_path;
        } else {
            // 保存远程图片
            $new_path = save_remote_image($img_url, $save_dir);
        }
        return $new_path;
    }
}

if (!function_exists('create_image_path')) {

    /**
     * 创建图片存储目录
     * @param string $save_dir 存储目录
     * @param string $image_ext 图片后缀
     * @param string $image_root 图片存储根目录路径
     * @return string 返回文件目录
     */
    function create_image_path($save_dir = "", $image_ext = "", $image_root = IMG_PATH)
    {
        $image_dir = date("/Ymd/");
        if ($image_dir) {
            $image_dir = ($save_dir ? "/" : '') . $save_dir . $image_dir;
        }
        // 未指定后缀默认使用JPG
        if (!$image_ext) {
            $image_ext = "jpg";
        }
        $image_path = $image_root . $image_dir;
        if (!is_dir($image_path)) {
            // 创建目录并赋予权限
            mkdir($image_path, 0777, true);
        }
        $file_name = substr(md5(time() . rand(0, 999999)), 8, 16) . rand(100, 999) . ".{$image_ext}";
        $file_path = str_replace(ATTACHMENT_PATH, "", IMG_PATH) . $image_dir . $file_name;
        return $file_path;
    }
}

if (!function_exists('save_remote_image')) {

    /**
     * 保存网络图片到本地
     * @param string $img_url 网络图片地址
     * @param string $save_dir 保存目录
     * @return bool|string 图片路径
     */
    function save_remote_image($img_url, $save_dir = '/')
    {
        $content = file_get_contents($img_url);
        if (!$content) {
            return false;
        }
        if ($content{0} . $content{1} == "\xff\xd8") {
            $image_ext = 'jpg';
        } elseif ($content{0} . $content{1} . $content{2} == "\x47\x49\x46") {
            $image_ext = 'gif';
        } elseif ($content{0} . $content{1} . $content{2} == "\x89\x50\x4e") {
            $image_ext = 'png';
        } else {
            // 不是有效图片
            return false;
        }
        $save_path = create_image_path($save_dir, $image_ext);
        return file_put_contents(IMG_PATH . $save_path, $content) ? str_replace(ATTACHMENT_PATH, "", IMG_PATH) . $save_path : false;
    }

    /**
     * @param array $conf
     * @return string
     * @author LZH
     * @since 2022/01/15
     */
    function generate_token(array $conf)
    {
        $jwtObject = Jwt::getInstance()
            ->setSecretKey($conf['key'] ?? 'easyswoole') // 秘钥
            ->publish();

        $jwtObject->setAlg($conf['alg']); // 加密方式
        $jwtObject->setAud('user'); // 用户
        $jwtObject->setExp($conf['exp']); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('easyswoole'); // 发行人
        $jwtObject->setJti(md5(time() + '')); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time() + 60*5); // 在此之前不可用
        $jwtObject->setSub('login'); // 主题

        // 自定义数据
        $jwtObject->setData($conf['custom']);

        // 最终生成的token
        return $jwtObject->__toString();
    }

    /**
     * 解析token
     * @param string $token token字符串
     * @param string $key 秘钥字符串
     * @throws \EasySwoole\Jwt\Exception
     * @since 2022/01/15
     * @author LZH
     */
    function parse_token(string $token, string $key = 'easyswoole')
    {
        try {
            $jwt = Jwt::getInstance();

            // 如果encode设置了秘钥,decode 的时候要指定
            /* @var \EasySwoole\Jwt\JwtObject $tokenObj */
            return $jwt->setSecretKey($key)->decode($token);
        } catch (\EasySwoole\Jwt\Exception $e) {
            throw $e;
        }
    }
}
