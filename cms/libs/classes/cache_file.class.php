<?php
/**
 * 文件缓存类
 */

use Throwable;

class cache_file {

    protected $path;
    protected $mode;
    protected $prefix;

    public function __construct() {

        $this->mode = 0640;
        $this->prefix = substr(SYS_KEY, 0, 10).'-';
        $this->path = CACHE_PATH.'caches_file/caches_data/';
        $this->path = rtrim($this->path, '/') . '/';

        if (!$this->is_really_writable($this->path)) {
            log_message('debug', '缓存目录（'.$this->path.'）无权限写入，请赋予可写权限');
            return false;
        }
    }

    public function initialize() {
    }

    public function get(string $key) {

        $key  = static::validateKey($key, $this->prefix);
        $data = $this->getItem($key);

        return is_array($data) ? $data['data'] : null;
    }

    public function save(string $key, $value, int $ttl = 60) {

        $key = static::validateKey($key, $this->prefix);

        !is_dir($this->path) ? dr_mkdirs($this->path, $this->mode) : '';

        $contents = [
            'time' => SYS_TIME,
            'ttl'  => $ttl,
            'data' => $value,
        ];

        if ($this->writeFile($this->path . $key, serialize($contents))) {
            try {
                chmod($this->path . $key, $this->mode);
            } catch (Throwable $e) {
                log_message('debug', '无法设置缓存文件的模式：' . $e);
            }

            return true;
        }

        return false;
    }

    public function delete(string $key) {

        $key = static::validateKey($key, $this->prefix);

        return is_file($this->path . $key) && unlink($this->path . $key);
    }

    public function clean() {
        return $this->deleteFiles($this->path, false, true);
    }

    public function isSupported(): bool {
        return is_writable($this->path);
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

    protected function getItem(string $filename) {
        if (! is_file($this->path . $filename)) {
            return false;
        }

        $data = @unserialize(file_get_contents($this->path . $filename));

        if (! is_array($data)) {
            return false;
        }

        if (! isset($data['ttl']) || ! is_int($data['ttl'])) {
            return false;
        }

        if (! isset($data['time']) || ! is_int($data['time'])) {
            return false;
        }

        if ($data['ttl'] > 0 && SYS_TIME > $data['time'] + $data['ttl']) {
            @unlink($this->path . $filename);

            return false;
        }

        return $data;
    }

    protected function writeFile($path, $data, $mode = 'wb') {
        if (($fp = @fopen($path, $mode)) === false) {
            return false;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($data, $written))) === false) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }

    protected function deleteFiles(string $path, bool $delDir = false, bool $htdocs = false, int $_level = 0): bool {
        $path = rtrim($path, '/\\');

        if (! $currentDir = @opendir($path)) {
            return false;
        }

        while (false !== ($filename = @readdir($currentDir))) {
            if ($filename !== '.' && $filename !== '..') {
                if (is_dir($path . DIRECTORY_SEPARATOR . $filename) && $filename[0] !== '.') {
                    $this->deleteFiles($path . DIRECTORY_SEPARATOR . $filename, $delDir, $htdocs, $_level + 1);
                } elseif ($htdocs !== true || ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)) {
                    @unlink($path . DIRECTORY_SEPARATOR . $filename);
                }
            }
        }

        closedir($currentDir);

        return ($delDir === true && $_level > 0) ? @rmdir($path) : true;
    }

    protected function is_really_writable(string $file): bool {
        if (!$this->is_windows()) {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . bin2hex(random_bytes(16));
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }

            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);

            return true;
        }

        if (! is_file($file) || ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }

        fclose($fp);

        return true;
    }

    protected function is_windows(?bool $mock = null): bool {
        static $mocked;

        if (func_num_args() === 1) {
            $mocked = $mock;
        }

        return $mocked ?? DIRECTORY_SEPARATOR === '\\';
    }
}
