<?php

declare(strict_types=1);

namespace App\Utility\Traits;

use App\Utility\ResponseCode;

/**
 * Trait ResponseTrait
 * @package App\Traits
 */
trait ResponseTrait
{
    /**
     * 拼装成功数据
     * @param null $data
     * @param int $code
     * @param string $message
     * @param int $count
     */
    public function success($data = null, $code = null, $message = null, $count = 0)
    {
        $code = $code ?: 200;
        $msg = $message ?: ResponseCode::getMessage($code);

        $result = null;
        if (!empty($data) && is_array($data)) {
            if (count($data) > 1) {
                $result = $data;
            } else { // 单个数组元素转换为对象
                $result = json_decode(json_encode($data), false); // 数组转换为对象
            }
        } else {
            $result = $data;
        }

        $count = $count || count($result);

        return $this->customWriteJson($code, $result, $msg, $count);
    }

    /**
     * 异常返回
     * @param int $code
     * @param string $message
     */
    public function fail($code = null, $message = null)
    {
        $code = $code ?: 100;
        $msg = $message ?: ResponseCode::getMessage($code);
        return $this->customWriteJson($code, null, $msg);
    }

    //--------------------------------------------------------------------
    // Response 帮助函数
    //--------------------------------------------------------------------

    /**
     * 查询成功资源后使用
     *
     * @return mixed
     */
    public function respondQuery($data = null, string $message = '')
    {
        return $this->success($data, ResponseCode::CODE_OK, $message);
    }

    /**
     * 成功创建新资源后使用
     *
     * @return mixed
     */
    public function respondCreated($data = null, string $message = '')
    {
        return $this->success($data, ResponseCode::CODE_CREATED, $message);
    }

    /**
     * 在成功删除资源后使用
     *
     * @return mixed
     */
    public function respondDeleted($data = null, string $message = '')
    {
        return $this->success($data, ResponseCode::CODE_OK, $message);
    }

    /**
     * 在成功更新资源后使用
     *
     * @return mixed
     */
    public function respondUpdated($data = null, string $message = '')
    {
        return $this->success($data, ResponseCode::CODE_OK, $message);
    }

    /**
     * 在成功执行命令但没有有意义的回复发送回客户端时使用
     *
     * @return mixed
     */
    public function respondNoContent(string $message = '')
    {
        return $this->success(null, ResponseCode::CODE_NO_CONTENT, $message);
    }

    /**
     * 在客户端未发送授权信息或授权凭据错误时使用。 鼓励用户使用正确的信息再试一次。
     *
     * @return mixed
     */
    public function failUnauthorized(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_UNAUTHORIZED, $message);
    }

    /**
     * 当始终拒绝访问此资源并且再试一次也无济于事时使用
     *
     * @return mixed
     */
    public function failForbidden(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_FORBIDDEN, $message);
    }

    /**
     * 当指定的资源找不到的时候使用
     *
     * @return mixed
     */
    public function failNotFound(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_NOT_FOUND, $message);
    }

    /**
     * 当客户端提交的一个或多个字段验证失败时使用
     *
     * @return mixed
     */
    public function failValidationErrors(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_BAD_REQUEST, $message);
    }

    /**
     * 当尝试创建一个已经存在的资源的时候使用
     *
     * @return mixed
     */
    public function failResourceExists(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_CONFLICT, $message);
    }

    /**
     * 当一个资源已经被删除。这和找不到资源不同，因为我们知道这个资源先前存在，而现在不存在了，
     * 找不到是简单的找不到这个资源。
     *
     * @return mixed
     */
    public function failResourceGone(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_GONE, $message);
    }

    /**
     * 当用户最近频繁请求一个资源的时候使用
     *
     * @return mixed
     */
    public function failTooManyRequests(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_TOO_MANY_REQUESTS, $message);
    }

    /**
     * 当服务器存在一个错误的时候使用
     *
     * @return mixed
     */
    public function failServerError(string $message = '')
    {
        return $this->fail(ResponseCode::CODE_INTERNAL_SERVER_ERROR, $message);
    }

    // 重写该方法
    protected function customWriteJson($statusCode = 200, $data = null, $msg = null, $count = 0)
    {
        if (!$this->response()->isEndResponse()) {
            $result = [
                "code" => $statusCode,
                "data" => $data,
                "msg" => $msg
            ];
            if ($count > 1) {
                $result['count'] = $count;
            }
            $this->response()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }
}
