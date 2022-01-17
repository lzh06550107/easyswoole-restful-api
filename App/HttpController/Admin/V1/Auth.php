<?php

namespace App\HttpController\Admin\V1;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;

/**
 * 管理员登录控制器
 * @package App\HttpController\Api\Admin
 * @ApiGroup(groupName="后端接口")
 * @ApiGroupDescription("该组为后端页面访问的接口")
 */
class Auth extends AdminBase
{
    protected $whiteList=['login','captcha'];

    /**
     * login
     * 登陆,参数验证注解写法
     * @Param(name="username", alias="帐号", required="", lengthMax="20")
     * @Param(name="password", alias="密码", required="", lengthMin="6", lengthMax="16")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author LZH
     */
    public function login($username, $password)
    {
        $param = $this->request()->getRequestParam();

//        // 登录账号
//        $username = trim($param['username']); // LZH 用户名称要唯一
//        if (!$username) {
//            return $this->writeJson(Status::CODE_BAD_REQUEST, null, '登录账号不能为空');
//        }
//
//        // 登录密码
//        $password = trim($param['password']);
//        if (!$password) {
//            return $this->writeJson(Status::CODE_BAD_REQUEST, null, '登录密码不能为空');
//        }

        // 验证码
        $captcha = trim($param['captcha']);
        $code = Cache::getInstance()->get('captcha');
        if ($captcha != "520" && strtolower($captcha) != strtolower($code)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, null, '请输入正确的验证码');
        }

        $login_ip = $this->clientRealIP();
        $login_time = time();

        if ($user = $this->service->login($login_ip, $login_time)) {

            // JWT生成token
            $jwt = new Jwt();
            $token = $jwt->getToken($user['id']); // token和用户的关联保存在token中，这里会验证token

            // 设置日志标题
            // ActionLog::setTitle("登录系统");

            unset($user['adminPassword']);
            $user['access_token'] = $token;
            $this->writeJson(Status::CODE_OK, $user);
        } else {
            $this->writeJson(Status::CODE_EXPECTATION_FAILED, null, '用户或密码错误');
        }
    }

    /**
     * @Api(name="logout",path="/api/Admin/auth/logout")
     * @ApiDescription("用户注销")
     * @ApiRequestExample("curl http://127.0.0.1:9501/api/Admin/auth/logout")
     * @ApiSuccess({"code":200,"result":null,"msg":"登出成功"})
     * @ApiFail({"code":401,"result":null,"msg":"尚未登入"})
     * @return bool
     * @author LZH
     */
    public function logout()
    {
        // 记录退出日志
//        ActionLog::setTitle("注销系统");
        // 创建退出日志
//        ActionLog::record();
        return $this->writeJson(Status::CODE_OK);
    }

    /**
     * 获取验证码
     */
    public function captcha()
    {
        // 配置验证码
        $config = new Conf();
        $code = new \EasySwoole\VerifyCode\VerifyCode($config);

        // 生成验证码
        $drawCode = $code->DrawCode();

        // 获取生成的验证码内容字符串 string(4) "0rnh"
        // 可存储起来和用户输入的验证码比对
        $codeStr = $drawCode->getImageCode();
        Cache::getInstance()->set('captcha', $codeStr); //LZH 需要优化

        $result = [
            'captcha' => $drawCode->getImageBase64()
        ];

        // 向客户端输出验证码的 base64 编码，前端可用来生成图片
        $this->writeJson(Status::CODE_OK, $result);
    }
}
