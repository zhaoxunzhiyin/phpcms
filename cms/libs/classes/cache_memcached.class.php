<?php
/**
 * Memcached缓存类
 */

use Error;
use Exception;
use Memcache;
use Memcached;

class cache_memcached {

    protected $path;
    protected $mode;
    protected $prefix;
    protected $memcached;
    protected $config = [
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'weight' => 1,
        'raw'    => false,
    ];

    public function __construct() {

        $this->prefix = substr(SYS_KEY, 0, 10).'-';

        if (is_file(CONFIGPATH.'memcached.php')) {
            $this->config = require CONFIGPATH.'memcached.php';
        }
    }

    public function __destruct() {
        if ($this->memcached instanceof Memcached) {
            $this->memcached->quit();
        } elseif ($this->memcached instanceof Memcache) {
            $this->memcached->close();
        }
    }

    public function initialize() {

        try {
            if (class_exists(Memcached::class)) {
                $this->memcached = new Memcached();
                if ($this->config['raw']) {
                    $this->memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                }

                $this->memcached->addServer(
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['weight']
                );

                $stats = $this->memcached->getStats();

                if (! isset($stats[$this->config['host'] . ':' . $this->config['port']])) {
                    throw new Error(L('缓存：Memcached连接失败。'));
                }
            } elseif (class_exists(Memcache::class)) {
                $this->memcached = new Memcache();

                $canConnect = $this->memcached->connect(
                    $this->config['host'],
                    $this->config['port']
                );

                if ($canConnect === false) {
                    throw new Error(L('缓存：Memcache连接失败。'));
                }

                $this->memcached->addServer(
                    $this->config['host'],
                    $this->config['port'],
                    true,
                    $this->config['weight']
                );
            } else {
                throw new Error(L('缓存：不支持Memcache（d）扩展。'));
            }
        } catch (Exception $e) {
            throw new Error(L('缓存：Memcache（d）连接被拒绝（' . $e->getMessage() . '）。'));
        }
    }

    public function get(string $key) {

        $data = [];
        $key  = static::validateKey($key, $this->prefix);

        if ($this->memcached instanceof Memcached) {
            $data = $this->memcached->get($key);

            if ($this->memcached->getResultCode() === Memcached::RES_NOTFOUND) {
                return null;
            }
        } elseif ($this->memcached instanceof Memcache) {
            $flags = false;
            $data  = $this->memcached->get($key, $flags);

            if ($flags === false) {
                return null;
            }
        }

        return is_array($data) ? $data[0] : $data;
    }

    public function save(string $key, $value, int $ttl = 60) {

        $key = static::validateKey($key, $this->prefix);

        if (! $this->config['raw']) {
            $value = [
                $value,
                SYS_TIME,
                $ttl,
            ];
        }

        if ($this->memcached instanceof Memcached) {
            return $this->memcached->set($key, $value, $ttl);
        }

        if ($this->memcached instanceof Memcache) {
            return $this->memcached->set($key, $value, 0, $ttl);
        }

        return false;
    }

    public function delete(string $key) {

        $key = static::validateKey($key, $this->prefix);

        return $this->memcached->delete($key);
    }

    public function clean() {
        return $this->memcached->flush();
    }

    public function isSupported(): bool {
        return extension_loaded('memcached') || extension_loaded('memcache');
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
