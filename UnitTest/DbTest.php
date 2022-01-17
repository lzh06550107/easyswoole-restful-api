<?php

namespace UnitTest;

use App\Model\Admin\AdminModel;
use EasySwoole\Mysqli\QueryBuilder;
use PHPUnit\Framework\TestCase;
use EasySwoole\ORM\DbManager;

/**
 * 通过AdminModel来测试CacheModel、BaseModel
 * Created on 2021/8/16 16:36
 * Create by LZH
 */
class DbTest extends TestCase
{
    public function testCon()
    {
        $builder = new QueryBuilder();
        $builder->raw('select version()');
        $ret = DbManager::getInstance()->query($builder, true)->getResult();
        $this->assertArrayHasKey('version()', $ret[0]);
    }

    public function testAddAdmin()
    {
        $data = [
            'scchool_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->data($data)->save();
        $this->assertGreaterThan(0, $id, '数据插入失败');
        $adminModel->destroy($adminModel->id);
    }

    public function testAddAdminUseCustom()
    {
        $data = [
            'scchool_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];
        $adminModel = new AdminModel();
        $id = $adminModel->edit($data);
        $this->assertGreaterThan(0, $id, '插入失败');
        $adminModel->destroy($id);
    }

    public function testUpdateAdmin()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->data($data)->save();

        // 只会更新指定的字段
        $updateData = [
            'id' => $id,
            'nickname' => 'diaomao2代',
            'realname' => '李四',
            'mark' => 0
        ];

        $result = $adminModel->update($updateData);
        $this->assertTrue($result);
        $adminModel->destroy($id);
    }

    public function testUpdateAdminUseCustom()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->edit($data);

        // 只会更新指定的字段
        $updateData = [
            'id' => $id,
            'nickname' => 'diaomao2代',
            'realname' => '李四',
            'mark' => 0
        ];

        $result = $adminModel->edit($updateData);
        $this->assertGreaterThan(0, $id, '插入失败');
        $adminModel->destroy($id);
    }

    public function testGetOneAdmin()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->data($data)->save();

        $adminModel = new AdminModel();
        $returnModel = $adminModel->get(['id' => $id])->toArray();
        print_r($returnModel);
        $this->assertEquals($data['username'], $returnModel['username'], '查询失败');
        $adminModel->destroy($id);
    }

    public function testGetOneAdminUseCustom()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->edit($data);

        $adminModel = new AdminModel();
        $returnModel = $adminModel->getOne(['id' => $id]);
        $this->assertEquals($data['username'], $returnModel['username'], '查询失败');
        $adminModel->destroy($id);
    }

    public function testGetListAdmin()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->data($data)->save();

        $adminModel = new AdminModel();
        $return = $adminModel->getList();
        print_r($return);
        $adminModel->destroy($id);
    }

    public function testGetListAdminUseCustom()
    {
        $data = [
            'school_id' => 1,
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->edit($data);

        $adminModel = new AdminModel();
        $return = $adminModel->getList();
        print_r($return);
        $adminModel->destroy($id);
    }

    public function testGetOneAdminAppendFiled()
    {
        $data = [
            'school_id' => 'test',
            'nickname' => 'diaomao',
            'realname' => '张三',
            'avatar' => '/images/user/20210811/7a6ef7feb529a7ef822.jpg',
            'gender' => 1,
            'username' => 'admin1234',
            'password' => password_hash('1234567890admin1234', PASSWORD_BCRYPT),
            'mobile' => '12365498787',
            'office_number' => '0755-1234567',
            'email' => 'xxx@qq.com',
            'login_num' => 1,
            'login_time' => time(),
            'login_ip' => '127.0.0.1',
            'department_id' => 1,
            'level_id' => 1,
            'position_id' => 1,
            'status' => 1,
            'sort' => 125,
            'create_id' => 1,
            'create_time' => time(),
            'mark' => 1
        ];

        $adminModel = new AdminModel();
        $id = $adminModel->data($data)->save();

        $adminModel = new AdminModel();
        $returnModel = $adminModel->get(['id' => $id]);
        print_r($returnModel);
        $this->assertEquals($data['username'], $returnModel['username'], '查询失败');
        $adminModel->destroy($id);
    }
}
