<?php
/**
 * 缓存类
 */

class cache {

    private $cache;
    // 文件缓存目录
    private $file_dir;
    // 认证数据缓存目录
    private $auth_dir;
    // 缓存临时数据
    private $data = [];
    protected $siteid;
    public $handler = SYS_CACHE_TYPE == 1 ? 'memcached' : (SYS_CACHE_TYPE == 2 ? 'redis' : 'file');

    /**
     * 构造函数,初始化变量
     */
    public function __construct() {
        $this->file_dir = CACHE_PATH.'caches_data/caches_data/'; // 设置缓存目录
        $this->auth_dir = CACHE_PATH.'caches_authcode/caches_data/'; // 认证数据缓存目录
        if (defined('IS_ADMIN') && IS_ADMIN) {
            if (pc_base::load_sys_class('param')->get_cookie('siteid')) {
                $siteid = pc_base::load_sys_class('param')->get_cookie('siteid');
            }
        } else {
            $data = $this->get_file('sitelist', 'commons');
            if(!is_array($data)) {
                $siteid = SITE_ID;
            } else {
                foreach ($data as $v) {
                    if ($v['url'] == FC_NOW_HOST) $siteid = $v['siteid'];
                }
            }
        }
        if (empty($siteid)) $siteid = SITE_ID;
        $this->siteid = $siteid;
    }

    /**
     * 分析缓存文件名
     */
    private function parse_cache_file($file_name, $cache_dir = null) {
        return ($cache_dir ? CACHE_PATH.$cache_dir.'/' : $this->file_dir).$file_name.'.cache';
    }

    /**
     * 设置缓存目录
     */
    public function init_file($dir) {
        $this->file_dir = CACHE_PATH.trim($dir, '/').'/'; // 设置缓存目录
        return $this;
    }

    /**
     * 设置缓存
     */
    public function set_file($key, $value, $cache_dir = null) {

        if (dr_is_empty($key)) {
            return false;
        }

        $cache_file = self::parse_cache_file(strtolower($key), $cache_dir ? 'caches_'.$cache_dir.'/caches_data' : ''); // 分析缓存文件
        $value = dr_array2string($value); // 分析缓存内容

        // 分析缓存目录
        $cache_dir = ($cache_dir ? CACHE_PATH.'caches_'.$cache_dir.'/caches_data/' : $this->file_dir);
        !is_dir($cache_dir) ? dr_mkdirs($cache_dir, 0777) : (!is_writeable($cache_dir) && chmod($cache_dir, 0777));

        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();
        $rt = file_put_contents($cache_file, $value, LOCK_EX);

        if ($rt === false) {
            log_message('error', '缓存文件['.$cache_file.']无法写入');
        }

        return $rt ? true : false;
    }

    /**
     * 获取一个已经缓存的变量
     */
    public function get_file($key, $cache_dir = null, $is_cache = true) {

        if (dr_is_empty($key)) {
            return false;
        }

        $cache_file = self::parse_cache_file(strtolower($key), $cache_dir ? 'caches_'.$cache_dir.'/caches_data' : ''); // 分析缓存文件
        if (!isset($this->data[$cache_file]) || !$is_cache) {
            if (is_file($cache_file)) {
                $code = file_get_contents($cache_file);
                $json = json_decode($code, true);
                if ($code && !$json) {
                    $json = $code;
                }
                $this->data[$cache_file] = $json;
            } else {
                $this->data[$cache_file] = false;
                log_message('debug', '缓存文件['.$cache_file.']不存在');
            }
        }

        return $this->data[$cache_file];
    }

    /**
     * 删除缓存
     *
     * @param string $key
     * @return void
     */
    public function del_file($key, $cache_dir = null) {

        if (dr_is_empty($key)) {
            return true;
        }

        $cache_file = self::parse_cache_file(strtolower($key), $cache_dir ? 'caches_'.$cache_dir.'/caches_data' : '');  // 分析缓存文件

        return is_file($cache_file) ? unlink($cache_file) : true;
    }

    // 删除全部文件缓存
    public function del_all($dir = 'caches_data/caches_data') {

        !$dir && $dir = 'caches_data/caches_data';
        $path = CACHE_PATH.$dir.'/';

        dr_dir_delete($path);

        return;
    }

    //------------------------------------------------

    // 存储内容
    public function set_auth_data($name, $value, $siteid = '') {

        $siteid = $siteid ? $siteid : $this->siteid;

        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();

        dr_mkdirs($this->auth_dir);

        file_put_contents($this->auth_dir.md5($siteid.$name), is_array($value) ? dr_array2string($value) : $value, LOCK_EX);

        return $value;
    }

    // 获取内容
    public function get_auth_data($name, $siteid = '', $time = 0) {

        $siteid = $siteid ? $siteid : $this->siteid;

        $code_file = $this->auth_dir.md5($siteid.$name);
        if (is_file($code_file)) {
            if ($time) {
                $ft = filemtime($code_file);
                if ($ft) {
                    $st = SYS_TIME - $ft;
                    if ($st > $time) {
                        unlink($code_file);
                        log_message('debug', '缓存（'.$name.'）自动失效（'.FC_NOW_URL.'）超时: '.$st.'秒');
                        return ''; // 超出了指定的时间时
                    }
                }
            }
            $rt = file_get_contents($code_file);
            if ($rt) {
                $arr = dr_string2array($rt);
                if (is_array($arr)) {
                    return $arr;
                }
                return $rt;
            }
        }

        return '';
    }

    // 删除内容
    public function del_auth_data($name, $siteid = '') {

        $siteid = $siteid ? $siteid : $this->siteid;

        $code_file = $this->auth_dir.md5($siteid.$name);
        if (!is_file($code_file)) {
            return;
        }

        unlink($code_file);

        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();
    }

    // 验证内容
    public function check_auth_data($name, $time = 3600, $siteid = '') {

        $siteid = $siteid ? $siteid : $this->siteid;

        $code_file = $this->auth_dir.md5($siteid.$name);
        if (is_file($code_file)) {
            if (SYS_TIME - filemtime($code_file) > $time) {
                return '';
            }
            $rt = file_get_contents($code_file);
            if ($rt) {
                return $rt;
            }
        }

        return '';
    }

    //------------------------------------------------

    // 调用框架缓存
    public function init() {

        if ($this->cache) {
            return $this->cache;
        }

        $this->cache = pc_base::load_sys_class('cache_'.$this->handler);

        if (! $this->cache->isSupported()) {
            $this->cache = pc_base::load_sys_class('cache_file');

            if (! $this->cache->isSupported()) {
                $this->cache = pc_base::load_sys_class('cache_dummy');
            }
        }

        try {
            $this->cache->initialize();
        } catch (Error $e) {
            log_message('critical', $e . ' Resorting to using file handler.');
            $this->cache = pc_base::load_sys_class('cache_dummy');
        }

        return $this->cache;
    }

    //------------------------------------------------

    // 存储内容
    public function set_data($name, $value, $time = 3600) {

        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();

        $time && self::init()->save(md5(SYS_KEY.$this->siteid.$name), $value, $time);

        return $value;
    }

    // 获取内容
    public function get_data($name) {
        return self::init()->get(md5(SYS_KEY.$this->siteid.$name));
    }

    // 删除内容
    public function del_data($name) {
        function_exists('opcache_reset') && opcache_reset();
        return self::init()->delete(md5(SYS_KEY.$this->siteid.$name));
    }

    // 删除缓存
    public function clear($name) {
        if (!$name) {
            return;
        }
        $this->del_data($name);
    }

    // 使用框架
    public function get() {

        $param = func_get_args();
        if (!$param) {
            return null;
        }

        // 取第一个参数作为缓存变量名称
        $name = strtolower(array_shift($param));
        if (SYS_CACHE) {
            $result = $this->get_data($name);
            if (!$result) {
                // 缓存不存在时重写缓存
                $result = self::get_file($name);
                // 仍然不存在就表示没有数据
                if (!$result) {
                    return null;
                }
                // 存储缓存
                $this->set_data($name, $result, 3600);
            }
        } else {
            // 读取配置文件
            $result = self::get_file($name);
        }

        if (!$param) {
            return $result;
        }

        return dr_get_param_var($result, $param);
    }
}
?>