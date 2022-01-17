<?php
/**
 * 文件描述
 * Created on 2022/1/15 14:51
 * Create by LZH
 */

declare(strict_types=1);

namespace App\Service;

use App\Exception\Model\DatabaseInnerException;
use App\Utility\Traits\LogTrait;
use App\Utility\Traits\RedisTrait;
use EasySwoole\ORM\DbManager;

class BaseService
{
    use LogTrait;
    use RedisTrait;

    /**
     * 启动事务
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function startTrans(): bool
    {
        try {
            return DbManager::getInstance()->startTransactionWithCount();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }
    }

    /**
     * 回滚事务
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function rollback()
    {
        try {
            return DbManager::getInstance()->rollbackWithCount();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }
    }

    /**
     * 提交事务
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function commit()
    {
        try {
            return DbManager::getInstance()->commitWithCount();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }
    }

    /**
     * 获取数据列表
     * @param array $condition 查询条件
     * @return array
     * @author LZH
     * @since 2022/01/17
     */
    public function list(array $condition): array
    {
        return [];
    }

    /**
     * 添加模型数据
     * @param array $modelData 需要添加的模型数据
     * @return bool
     * @author LZH
     * @since 2022/01/17
     */
    public function add(array $modelData): bool
    {
        return true;
    }

    /**
     * 更新模型数据
     * @param array $modelData 需要更新的模型数据
     * @param array $condition 更新条件
     * @return bool
     * @author LZH
     * @since 2022/01/17
     */
    public function update(array $modelData, array $condition): bool
    {
        return true;
    }

    /**
     * 删除模型数据
     * @param int $id 需要删除的模型id
     * @return bool
     * @author LZH
     * @since 2022/01/17
     */
    public function delete(int $id): bool
    {
        return true;
    }

    /**
     * 批量删除模型数据
     * @param array|string $id 需要删除的模型id数组或者逗号分隔的字符串
     * @return bool
     * @author LZH
     * @since 2022/01/17
     */
    public function delAll($id): bool
    {
        return true;
    }
}
