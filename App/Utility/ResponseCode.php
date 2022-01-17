<?php

namespace App\Utility;

/**
    GET（SELECT）：从服务器取出资源（一项或多项），天生幂等。
    POST（CREATE）：在服务器新建一个资源，非幂等。
    PUT（UPDATE）：在服务器更新资源（客户端提供改变后的完整资源），天生幂等，如果更新的数据不存在，则插入（PUT比較正确的定义是 Replace (Create or Update)）。
    PATCH（UPDATE）：在服务器更新资源（客户端提供改变的属性,put虽然也是更新资源，
                   但要求前端提供的一定是一个完整的资源对象，理论上说，如果你用了put，但却没有提供完整的UserInfo，那么缺了的那些字段应该被清空
 *                 当更新的数据不存在，则不会更新，非幂等）。
    DELETE（DELETE）：从服务器删除资源，天生幂等。
 */
class ResponseCode
{
    // Success 2xx
    public const CODE_OK = 200;
    public const CODE_CREATED = 201;
    public const CODE_ACCEPTED = 202;
    public const CODE_NO_CONTENT = 204;

    // Redirection 3xx
    public const CODE_NOT_MODIFIED = 304;

    // Client Error 4xx
    public const CODE_BAD_REQUEST = 400;
    public const CODE_UNAUTHORIZED = 401;
    public const CODE_FORBIDDEN = 403;
    public const CODE_NOT_FOUND = 404;
    public const CODE_CONFLICT = 409;
    public const CODE_GONE = 410;
    public const CODE_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const CODE_TOO_MANY_REQUESTS = 429;

    // Server Error 5xx
    public const CODE_INTERNAL_SERVER_ERROR = 500;
    public const CODE_SERVICE_UNAVAILABLE = 503;

    /**
        1xx：相关信息
        2xx：操作成功
        3xx：重定向
        4xx：客户端错误
        5xx：服务器错误
     */
    private static $_MESSAGE = [

        // 成功类别
        200 => '操作成功', // [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）。
        201 => '保存成功', // [POST/PUT/PATCH]：用户创建或修改实例成功时，应返回此状态代码。
        202 => 'Accepted', // [*]：表示一个请求已经进入后台排队（异步任务）
        204 => 'No Content', // [DELETE]：内容不存在，表示请求已被成功处理，但并未返回任何内容。

        // 重定向类别
        304 => 'Not Modified', // [GET]：未修改，表示客户端的响应已经在其缓存中。 因此，不需要再次传送相同的数据。

        // 客户端错误类别
        400 => '无效的请求', // [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。
        401 => '用户未认证', // [*]：未授权：表示客户端不被允许访问该资源，需要使用指定凭证重新请求（令牌、用户名、密码等）。
        403 => '用户没有该权限', // [*]：禁止访问，表示请求是有效的并且客户端已通过身份验证（与401错误相对），但客户端不被允许以任何理由访问对应页面或资源。 例如，有时授权的客户端不被允许访问服务器上的目录。
        404 => '找不到该请求动作', // [*]：未找到，表示所请求的资源现在不可用。例如，用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
        409 => '冲突',
        410 => '资源被永久移除', // [GET]： 资源不可用，表示所请求的资源后续不再可用，该资源已被永久移动。
        422 => '实体对象验证错误', // [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
        429 => '请求太频繁',

        // 服务器错误类别
        500 => '服务端内部错误', // 服务器内部错误，表示请求已经被接收到了，但服务器被要求处理某些未预设的请求而完全混乱。
        503 => '服务不可用', // 服务不可用表示服务器已关闭或无法接收和处理请求。大多数情况是服务器正在进行维护。
    ];

    /**
     * @param int $code
     * @return mixed|string
     */
    public static function getMessage($code = 0): string
    {
        if (isset(self::$_MESSAGE[$code])) {
            return self::$_MESSAGE[$code] ?? '';
        }

        return '未知消息';
    }
}
