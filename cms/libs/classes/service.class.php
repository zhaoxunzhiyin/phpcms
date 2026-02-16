<?php

/**
 * CMS模板标签解析
 */
class service {

    private $_is_return; // 是否返回模板名称不输出模板

    private $_dir; // 模板目录

    private $_cache; // 模板缓存目录

    private $_options; // 模板变量
    private $_include_file; // 引用计数

    private $_view_time; // 模板的运行时间
    private $_view_files; // 累计模板的引用文件
    private $_view_file; // 当前模板的引用文件

    public $_is_mobile; // 是否是移动端模板
    private $_is_pc; // 是否是pc模板

    public $call_value; // 动态模板返回调用

    static private $apps = [
        1 => [],
        0 => [],
    ];

    /**
     * 初始化环境
     */
    public function init($name = 'pc') {
        $this->_is_pc = $name == 'pc'; // 标记为pc端模板
        $this->_is_mobile = $name == 'mobile'; // 标记为移动端模板
        $this->_dir = $name ? $name : 'pc';
    }

    // 判断是pc端模板
    public function is_pc() {
        return $this->_is_pc;
    }

    // 判断是移动端模板
    public function is_mobile() {
        return $this->_is_mobile;
    }

    // 外部获取当前应用的模板路径
    public function get_dir() {
        return $this->_dir;
    }

    /**
     * 设置是否返回模板名称不显示
     */
    public function set_return($is) {
        $this->_is_return = $is;
    }

    /**
     * 当前模板对应的URL地址
     */
    public function now_php_url() {

        if (isset($this->_options['my_php_url']) && $this->_options['my_php_url']) {
            return $this->_options['my_php_url'];
        } elseif (isset($this->_options['my_web_url']) && $this->_options['my_web_url']) {
            return $this->_options['my_web_url'];
        }

        return FC_NOW_URL;
    }

    /**
     * 输出模板
     */
    public function display($module = 'content', $template = 'index', $style = '') {

        if ($this->_is_return) {
            return $template;
        }

        $cms_start = microtime(true);

        // 定义当前模板的url地址
        if (!isset($this->_options['my_web_url']) or !$this->_options['my_web_url']) {
            $this->_options['my_web_url'] = isset($this->_options['fix_html_now_url']) && $this->_options['fix_html_now_url'] ? $this->_options['fix_html_now_url'] : dr_now_url();
        }

        define('SITEID', $this->_options['siteid'] ? $this->_options['siteid'] : get_siteid());

        // 定义当前url参数值
        if (!isset($this->_options['get'])) {
            $this->_options['get'] = pc_base::load_sys_class('input')->get();
        }

        // 挂钩点 模板加载之前
        pc_base::load_sys_class('hooks')::trigger('cms_view_display', $this->_options, $template, $module);

        extract($this->_options, EXTR_SKIP);

        // 加载编译后的缓存文件
        $this->_view_file = $_view_file = template($module, $template, $style);

        $is_dev = 0;
        if ((IS_DEV || (IS_ADMIN && SYS_DEBUG))
            && !pc_base::load_sys_class('input')->get('callback') && !pc_base::load_sys_class('input')->get('is_ajax')
            && !IS_AJAX) {
            $is_dev = 1;
            echo '<!--当前页面的模板文件是：'.(strpos($_view_file, '.tpl.php') !== false ? $_view_file : str_replace(array(CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR, '.php'), array(TPLPATH, '.html'), $_view_file)).' （本代码只在开发者模式下显示）-->'.PHP_EOL;
        }

        $_temp_file = $this->load_view_file($_view_file);

        // 挂钩点 模板加载之后
        pc_base::load_sys_class('hooks')::trigger('cms_view', $this->_options, $_temp_file);

        include $_temp_file;

        // 挂钩点 模板结束之后
        pc_base::load_sys_class('hooks')::trigger('cms_view_end');

        $this->_view_time = round(microtime(true) - $cms_start, 2);

        // 消毁变量
        unset($this->_include_file);
        if (!$is_dev) {
            unset($this->_options);
        }
    }

    /**
     * 后台输出模板
     */
    public function admin_display($file, $m = '') {

        if ($this->_is_return) {
            return $file;
        }

        $cms_start = microtime(true);

        // 定义当前模板的url地址
        if (!isset($this->_options['my_web_url']) or !$this->_options['my_web_url']) {
            $this->_options['my_web_url'] = isset($this->_options['fix_html_now_url']) && $this->_options['fix_html_now_url'] ? $this->_options['fix_html_now_url'] : dr_now_url();
        }

        // 定义当前url参数值
        if (!isset($this->_options['get'])) {
            $this->_options['get'] = pc_base::load_sys_class('input')->get();
        }

        // 挂钩点 模板加载之前
        pc_base::load_sys_class('hooks')::trigger('cms_view_display', $this->_options, $file, $m);

        extract($this->_options, EXTR_SKIP);

        // 加载编译后的缓存文件
        $this->_view_file = $_view_file = admin_template($file, $m);

        $is_dev = 0;
        if ((IS_DEV || (IS_ADMIN && SYS_DEBUG))
            && !pc_base::load_sys_class('input')->get('callback') && !pc_base::load_sys_class('input')->get('is_ajax')
            && !IS_AJAX) {
            $is_dev = 1;
            echo '<!--当前页面的模板文件是：'.(strpos($_view_file, '.tpl.php') !== false ? $_view_file : str_replace(array(CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR, '.php'), array(TPLPATH, '.html'), $_view_file)).' （本代码只在开发者模式下显示）-->'.PHP_EOL;
        }

        $_temp_file = $this->load_view_file($_view_file);

        // 挂钩点 模板加载之后
        pc_base::load_sys_class('hooks')::trigger('cms_view', $this->_options, $_temp_file);

        include $_temp_file;

        // 挂钩点 模板结束之后
        pc_base::load_sys_class('hooks')::trigger('cms_view_end');

        $this->_view_time = round(microtime(true) - $cms_start, 2);

        // 消毁变量
        unset($this->_include_file);
        if (!$is_dev) {
            unset($this->_options);
        }
    }

    /**
     * 设置模板变量
     */
    public function assign($key, $value = '') {

        if (!$key) {
            return FALSE;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_options[$k] = $v;
            }
        } else {
            $this->_options[$key] = $value;
        }
    }

    /**
     * 获取模板变量
     */
    public function get_value($key) {

        if (!$key) {
            return '';
        }

        return $this->_options[$key];
    }

    /**
     * 重新赋值模板变量
     */
    public function set_value($key, $value = '', $replace = '') {

        if (!$key) {
            return '';
        }

        $this->_options[$key] = $replace ? str_replace($replace, $value, $this->_options[$key]) : $value;
    }

    /**
     * 模板标签include/template
     *
     * @param   string  $dir    模块目录
     * @param   string  $name   模板文件
     * @param   string  $style  模板风格
     * @return  bool
     */
    public function _include($dir = 'content', $name = 'index', $style = '') {

        $dir = $dir == 'ROUTE_M' ? ROUTE_M : $dir;
        $file = template($dir, $name, $style);

        $fname = md5($file);
        isset($this->_include_file[$fname]) ? $this->_include_file[$fname] ++ : $this->_include_file[$fname] = 0;

        if ($this->_include_file[$fname] > 500) {
            $this->show_error('模板文件标签template引用文件目录结构错误', $file);
        }

        return $this->load_view_file($file);
    }

    /**
     * 模板标签load
     *
     * @param   string  $file   模板文件
     * @return  bool
     */
    public function _load($file) {

        $fname = md5($file);
        $this->_include_file[$fname] ++;

        if ($this->_include_file[$fname] > 500) {
            $this->show_error('模板文件标签load引用文件目录结构错误', $file);
        }

        return $this->load_view_file($file);
    }

    /**
     * 加载缓存文件
     *
     * @param   string
     * @return  string
     */
    public function load_view_file($file) {

        $this->_view_files[$file] = [
            'name' => pathinfo($file, PATHINFO_BASENAME),
            'path' => (strpos($file, '.tpl.php') !== false ? $file : str_replace(array(CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR, '.php'), array(TPLPATH, '.html'), $file)),
        ];

        return $file;
    }

    // 将模板代码转化为php
    public function code2php($code, $time = 0, $include = 1) {

        // 模板缓存目录
        $this->_cache = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR;
        if(!is_dir($this->_cache)) {
            dr_mkdirs($this->_cache);
        }

        $file = md5($code).$time.'.code.php';
        if (!$include) {
            $code = preg_replace([
                '#{template.*}#Uis',
                '#{load.*}#Uis'
            ], [
                '--',
                '--',
            ], $code);
        }
        if (!is_file($this->_cache.$file)) {
            file_put_contents($this->_cache.$file, str_replace('$this->', 'pc_base::load_sys_class(\'service\')->', pc_base::load_sys_class('template_cache')->template_parse($code)));
        }

        return $this->_cache.$file;
    }

    // 模板中的全部变量
    public function get_data() {
        return $this->_options;
    }

    // 模板中的文件数
    public function get_view_files() {
        return $this->_view_files;
    }

    // 模板中的运行时间
    public function get_view_time() {
        return $this->_view_time;
    }

    // 错误提示
    public function show_error($msg, $file = '', $fixfile = '') {

        if (CI_DEBUG || defined('IS_HTML') && IS_HTML) {
            // 开发者模式下，静态生成模式下，显示详细错误
            if ($file) {
                $msg.= '（'.$file.'）';
                if ($fixfile) {
                    $msg.= '<br>你可以将PC模板（'.$fixfile.'）手动复制过来作为本模板';
                }
            }
            log_message('error', $this->_options['my_web_url'].'：'.$msg);
            if (defined('IS_HTML') && IS_HTML) {
                dr_json(0, $this->_options['my_web_url'].'：'.$msg);
            }
        }

        dr_show_error($msg);
    }

    // 获取模块目录
    public static function apps($is_install = 0) {

        $is_install = $is_install ? 1 : 0;

        if (isset(static::$apps[$is_install]) && static::$apps[$is_install]) {
            return static::$apps[$is_install];
        }

        static::$apps[$is_install] = [];
        $source_dir = PC_PATH.'modules/';
        if ($fp = opendir($source_dir)) {
            while (FALSE !== ($file = readdir($fp))) {
                $path = PC_PATH.'modules/'.$file.'/';
                if ($file === '.' OR $file === '..'
                    OR $file[0] === '.'
                    OR !is_dir($path)) {
                    continue;
                }
                if ($is_install && !module_exists($file)) {
                    continue;
                }
                static::$apps[$is_install][$file] = $path;
            }
            closedir($fp);
        }

        if (function_exists('dr_get_extend')) {
            $extend = dr_get_extend($is_install);
           if ($extend) {
               foreach ($extend as $i => $t) {
                   static::$apps[$is_install][$i] = $t;
               }
           }
        }

        return static::$apps[$is_install];
    }
}