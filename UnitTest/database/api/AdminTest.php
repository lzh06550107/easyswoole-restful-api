<?php

namespace UnitTest\database\api;

use UnitTest\BaseTest;

class AdminTest extends BaseTest
{
    public $access_token;

    public function testLogin()
    {
        $data = [
            'username' => 'admin888',
            'password' => '1234567890',
            'captcha' => '520'
        ];
        $reponse = $this->request('api/admin/login/login', $data);
        return $reponse->result->access_token;
    }

    /**
     * @depends testLogin
     */
    public function testIndex($access_token)
    {
//        $response = $this->request('api/admin/index/getMenuList', [], null, ['Authorization' => 'Bearer ' . $access_token, 'Accept' => 'application/json']);
        $this->curl->setHeader('Authorization', 'Bearer ' . $access_token);
        $response = $this->request('api/admin/index/getUserInfo');
        var_dump($this->curl->getRequestHeaders());
        $this->assertEquals('200', $response->code);
//        print_r(json_encode($response, JSON_UNESCAPED_UNICODE));

    }
}
