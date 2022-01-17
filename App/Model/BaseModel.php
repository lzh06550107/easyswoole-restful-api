<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\Model\DatabaseInnerException;
use App\Exception\Model\ModelNotExistException;
use App\Utility\Traits\LogTrait;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{
    use LogTrait;

    // 自动写入时间戳字段，true开启false关闭
    protected $autoTimeStamp = false;
    // 创建时间字段自定义，默认create_time
    protected $createTime = 'create_time';
    // 更新时间字段自定义，默认update_time
    protected $updateTime = 'update_time';

    public function __construct()
    {
        parent::__construct();
        // 给表添加前缀
        $this->tableName(Config::getInstance()->getConf('MYSQL.prefix') . $this->tableName);
    }

    /**
     * 根据主键获取模型对象
     * @param Int $id 主键值
     * @param String $columns 查询列
     * @return array
     * @throws ModelNotExistException
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function getOne(Int $id, String $columns = '*'): array
    {
        try {
            $data = $this->where('id', $id)->field($columns)->get();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        if (is_bool($data) && $data == false) {
            // 抛出异常，表示数据不存在
            throw new ModelNotExistException(); // TODO 需要测试
        } elseif (is_null($data)) {
            // 抛出异常，表示数据不存在
            throw new ModelNotExistException(); // TODO 需要测试
        }

        return $data;
    }

    /**
     * 通过条件查询一个模型
     * @param array $condition 条件
     * @param $columns 字段列
     * @return array
     * @throws DatabaseInnerException
     * @throws ModelNotExistException
     * @author LZH
     * @since 2022/01/15
     */
    public function getOneByMap(array $condition=[], $columns='*'): array
    {
        $allow = ['where', 'orWhere', 'join'];
        foreach ($condition as $k=>$v) {
            if (in_array($k, $allow)) {
                foreach ($v as $item) {
                    $this->$k(...$item);
                }
            }
        }

        try {
            $data = $this->field($columns)->get();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        if (is_bool($data) && $data == false) {
            // 抛出异常，表示数据不存在
            throw new ModelNotExistException(); // TODO 需要测试
        } elseif (is_null($data)) {
            // 抛出异常，表示数据不存在
            throw new ModelNotExistException(); // TODO 需要测试
        }

        return $data;
    }

    /**
     * 根据条件获取多个模型数组或模型集合
     * @param array $condition 条件
     * @param $columns 字段列
     * @return array
     * @throws DatabaseInnerException
     * @throws ModelNotExistException
     * @author LZH
     * @since 2022/01/15
     */
    public function getAll(array $condition=[], $columns='*'): array
    {
        $allow = ['where', 'orWhere', 'join', 'orderBy', 'groupBy'];

        foreach ($condition as $k=>$v) {
            if (in_array($k, $allow)) {
                foreach ($v as $item) {
                    $this->$k(...$item);
                }
            }
        }

        try {
            $data = $this->field($columns)->all();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage()); // TODO 需要测试
        }

        if (is_bool($data) && $data == false) {
            // 抛出异常，表示数据不存在
            throw new ModelNotExistException(); // TODO 需要测试
        }

        return $data;
    }

    /**
     * 保存一个模型对象
     * @param array $data 模型数据
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function addOne(array $data): bool
    {
        try {
            return static::create($data)->save();
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage()); // TODO 需要测试
        }
    }

    public function addMulti(array $data): bool
    {
        try {
            $result = static::create()->func(function (QueryBuilder $builder) use ($data) {
                $builder->insertAll($this->tableName(), $data);
            });
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage()); // TODO 需要测试
        }

        // TODO 需要调试来确定返回结果


        return true;
    }

    /**
     * 通过模型id来更新一个模型
     * @param Int $id 模型id
     * @param array $data 模型数据
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function updateOne(Int $id, array $data): bool
    {
        try {
            $result = $this->update($data, ['id' => $id]); // TODO 需要测试
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        return $result;
    }

    /**
     * 根据条件来更新多个模型
     * @param array $condition 条件数组
     * @param array $data 模型数据
     * @return bool
     * @throws DatabaseInnerException
     * @author LZH
     * @since 2022/01/15
     */
    public function updateByMap(array $condition, array $data): bool
    {
        $allow = ['where', 'orWhere'];
        foreach ($condition as $k=>$v) {
            if (in_array($k, $allow)) {
                foreach ($v as $item) {
                    $this->$k(...$item);
                }
            }
        }

        try {
            $result = $this->update($data);
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        return $result;
    }

    public function updateMulti(): ?bool
    {
        return true;
    }

    /**
     * 根据模型id来删除某个模型
     * @param Int $id 模型id
     * @return bool
     * @throws DatabaseInnerException
     * @throws ModelNotExistException
     * @author LZH
     * @since 2022/01/15
     */
    public function delete(Int $id): bool
    {
        try {
            $result = $this->destroy($id);
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        if (is_int($result) && $result == 0) {
            throw new ModelNotExistException('模型对象不存在'); // TODO 需要测试
        }

        return (bool)$result;
    }

    /**
     * 批量删除多个模型对象
     * @param array|string $ids 可以是数组，也可以是逗号分隔的id
     * @return bool
     * @throws DatabaseInnerException
     * @throws ModelNotExistException
     * @author LZH
     * @since 2022/01/15
     */
    public function delAll($ids): bool
    {
        try {
            $result = $this->destroy($ids);
        } catch (\Throwable $e) {
            // 记录数据库异常日志
            $this->log_error($e->getMessage());
            // 转换异常抛出，中断上层执行流
            throw new DatabaseInnerException($e->getMessage());
        }

        if (is_int($result) && $result == 0) {
            throw new ModelNotExistException('模型对象不存在'); // TODO 需要测试
        }

        return (bool)$result;
    }

}
