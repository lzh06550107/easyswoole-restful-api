<?php

declare(strict_types=1);

namespace App\Utility\Traits;

use DateInterval;
use DateTime;
use DateTimeInterface;
use EasySwoole\EasySwoole\Config;

/**
 * Redis缓存驱动，适合单机部署、有前端代理实现高可用的场景，性能最好
 * 有需要在业务层实现读写分离、或者使用RedisCluster的需求，请使用Redisd驱动
 */
trait RedisTrait
{
    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name): bool
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();
        return (bool)$redis->exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name    缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();
        $key   = $this->getCacheKey($name);
        $value = $redis->get($key);

        if (false === $value || is_null($value)) {
            return $default;
        }

        return $this->unserialize($value);
    }

    /**
     * 写入缓存
     * @access public
     * @param string            $name   缓存变量名
     * @param mixed             $value  存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null): bool
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();

        if (is_null($expire)) {
            $redisConfig = Config::getInstance()->getConf('REDIS');
            $expire = $redisConfig['expire'];
        }

        $key    = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);
        $value  = $this->serialize($value);

        if ($expire) {
            $redis->setex($key, $expire, $value);
        } else {
            $redis->set($key, $value);
        }

        return true;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    public function inc(string $name, int $step = 1)
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();

        $key = $this->getCacheKey($name);

        return $redis->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    public function dec(string $name, int $step = 1)
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();

        $key = $this->getCacheKey($name);

        return $redis->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function del($name): bool
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();

        $key    = $this->getCacheKey($name);
        $result = $redis->del($key);
        return $result > 0;
    }

    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public function clear(): bool
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();
        $redis->flushDB();
        return true;
    }

    /**
     * 追加（数组）缓存数据
     * @access public
     * @param string $name  缓存标识
     * @param mixed  $value 数据
     * @return void
     */
    public function push(string $name, $value): void
    {
        $redis = \EasySwoole\RedisPool\RedisPool::defer();
        $key = $this->getCacheKey($name);
        $redis->sAdd($key, $value);
    }

    private function getCacheKey(string $name): string
    {
        $redisConfig = Config::getInstance()->getConf('REDIS');
        return $redisConfig['prefix'] . $name;
    }

    /**
     * 序列化数据
     * @access protected
     * @param mixed $data 缓存数据
     * @return string
     */
    private function serialize($data): string
    {
        if (is_numeric($data)) {
            return (string) $data;
        }
        $redisConfig = Config::getInstance()->getConf('REDIS');
        $serialize = $redisConfig['serialize'][0] ?? "serialize";

        return $serialize($data);
    }

    /**
     * 反序列化数据
     * @access protected
     * @param string $data 缓存数据
     * @return mixed
     */
    private function unserialize(string $data)
    {
        if (is_numeric($data)) {
            return $data;
        }
        $redisConfig = Config::getInstance()->getConf('REDIS');
        $unserialize = $redisConfig['serialize'][1] ?? "unserialize";

        return $unserialize($data);
    }

    private function getExpireTime($expire): int
    {
        if ($expire instanceof DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        } elseif ($expire instanceof DateInterval) {
            $expire = DateTime::createFromFormat('U', (string) time())
                    ->add($expire)
                    ->format('U') - time();
        }

        return (int) $expire;
    }

    protected function getRedis()
    {
        return \EasySwoole\RedisPool\RedisPool::defer();
    }
}
