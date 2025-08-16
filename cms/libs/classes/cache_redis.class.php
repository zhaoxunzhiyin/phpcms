<?php
/**
 * Redis缓存类
 */

use Error;
use Redis;
use RedisException;

class cache_redis {

    protected $path;
    protected $mode;
    protected $prefix;
    protected $redis;
    protected $config = [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'timeout'  => 0,
        'database' => 0,
    ];

    public function __construct() {

        $this->prefix = substr(SYS_KEY, 0, 10).'-';

        if (is_file(CONFIGPATH.'redis.php')) {
            $this->config = require CONFIGPATH.'redis.php';
        }
    }

    public function __destruct() {
        if (isset($this->redis)) {
            $this->redis->close();
        }
    }

    public function initialize() {

        $config = $this->config;

        $this->redis = new Redis();

        try {

            if (! $this->redis->connect($config['host'], ($config['host'][0] === '/' ? 0 : $config['port']), $config['timeout'])) {
                log_message('error', L('缓存：Redis连接失败。检查您的配置。'));

                throw new Error(L('缓存：Redis连接失败。检查您的配置。'));
            }

            if (isset($config['password']) && ! $this->redis->auth($config['password'])) {
                log_message('error', L('缓存：Redis身份验证失败。'));

                throw new Error(L('缓存：Redis身份验证失败。'));
            }

            if (isset($config['database']) && ! $this->redis->select($config['database'])) {
                log_message('error', L('缓存：Redis选择数据库失败。'));

                throw new Error(L('缓存：Redis选择数据库失败。'));
            }
        } catch (RedisException $e) {
            throw new Error(L('缓存：发生RedisException，消息为 (' . $e->getMessage() . ')。'));
        }
    }

    public function get(string $key) {

        $key  = static::validateKey($key, $this->prefix);
        $data = $this->redis->hMGet($key, ['cms_type', 'cms_value']);

        if (! isset($data['cms_type'], $data['cms_value']) || $data['cms_value'] === false) {
            return null;
        }

        switch ($data['cms_type']) {
            case 'array':
            case 'object':
                return unserialize($data['cms_value']);

            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
            case 'NULL':
                return settype($data['cms_value'], $data['cms_type']) ? $data['cms_value'] : null;

            case 'resource':
            default:
                return null;
        }
    }

    public function save(string $key, $value, int $ttl = 60) {

        $key = static::validateKey($key, $this->prefix);

        switch ($dataType = gettype($value)) {
            case 'array':
            case 'object':
                $value = serialize($value);
                break;

            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
            case 'NULL':
                break;

            case 'resource':
            default:
                return false;
        }

        if (! $this->redis->hMSet($key, ['cms_type' => $dataType, 'cms_value' => $value])) {
            return false;
        }

        if ($ttl) {
            $this->redis->expireAt($key, SYS_TIME + $ttl);
        }

        return true;
    }

    public function delete(string $key) {

        $key = static::validateKey($key, $this->prefix);

        return $this->redis->del($key) === 1;
    }

    public function clean() {
        return $this->redis->flushDB();
    }

    public function isSupported(): bool {
        return extension_loaded('redis');
    }

    public static function validateKey($key, $prefix = ''): string {
        if (! is_string($key)) {
            log_message('debug', '缓存键必须是字符串');
        }
        if ($key === '') {
            log_message('debug', '缓存键不能为空。');
        }

        $reserved = '{}()/\@:';
        if ($reserved && strpbrk($key, $reserved) !== false) {
            log_message('debug', '缓存键包含保留字符 ' . $reserved);
        }

        return dr_strlen($prefix . $key) > 255 ? $prefix . md5($key) : $prefix . $key;
    }
}
