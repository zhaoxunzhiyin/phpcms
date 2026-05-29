<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class system_cache extends admin {
    private $input,$config;
    public function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->config = pc_base::load_sys_class('config');
    }

    public function init() {
        $show_header = true;
        $file = CONFIGPATH.'cache.php';
        if (IS_AJAX_POST) {
            $config = $this->input->post('data');
            if (!$this->config->file($file, '缓存配置文件')->to_require_one($config)) {
                dr_json(0, L('配置文件写入失败'));
            }
            dr_json(1, L('操作成功'));
        }
        $data = is_file($file) ? require $file : [];
        if (!isset($data['SYS_CACHE_CRON']) or empty($data['SYS_CACHE_CRON'])) {
            $data['SYS_CACHE_CRON'] = 3;
        }
        $page = intval($this->input->get('page'));
        $run_time = '';
        if (is_file(CACHE_PATH.'run_time.php')) {
            $run_time = file_get_contents(CACHE_PATH.'run_time.php');
        }
        $cache_var = ['SHOW' => '缓存时间'];
        include $this->admin_tpl('system_cache');
    }

    /**
     * 测试缓存是否可用
     */
    public function public_test_cache() {
        $data = $this->input->post('data');
        if (!isset($data)) {
            dr_json(0, L('参数错误'));
        }

        $type = intval(isset($data['SYS_CACHE_TYPE']) ? $data['SYS_CACHE_TYPE'] : 0);
        switch ($type) {
            case 1:
                $name = 'memcached';
                if (!extension_loaded('memcached') && !extension_loaded('memcache')) {
                    dr_json(0, L('PHP环境没有安装['.$name.']扩展'));
                }
                break;
            case 2:
                $name = 'redis';
                if (!extension_loaded('redis')) {
                    dr_json(0, L('PHP环境没有安装['.$name.']扩展'));
                }
                break;
            default:
                $name = 'file';
                if (!dr_check_put_path(CACHE_PATH.'caches_file/caches_data/')) {
                    dr_json(0, L('请分配caches/caches_file目录的可读写权限'));
                }
                break;
        }

		$cache = pc_base::load_sys_class('cache_'.$name);
        if (!$cache->isSupported()) {
            dr_json(0, L('PHP环境没有安装['.$name.']扩展'));
        }

		$cache->initialize();
		$rt = $cache->save(md5('test'), 'cms', 60);
        if (!$rt) {
            dr_json(1, L('缓存方式['.$name.']存储失败'));
        } elseif ($cache->get(md5('test')) == 'cms') {
            dr_json(1, L('缓存方式['.$name.']已生效'));
        } else {
            dr_json(0, L('缓存方式['.$name.']未生效'));
        }
    }
}
