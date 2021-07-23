<?php
class input {

    protected $ip_address;

    // get post解析
    public function request($name, $xss = false) {
        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : (isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : false));
        return $xss ? $this->xss_clean($value) : $value;
    }
    
    // post解析
    public function post($name, $xss = false) {
        $value = isset($_POST[$name]) ? $_POST[$name] : false;
        return $xss ? $this->xss_clean($value) : $value;
    }

    // get解析
    public function get($name = '', $xss = false) {
        $value = !$name ? $_GET : (isset($_GET[$name]) ? $_GET[$name] : false);
        return $xss ? $this->xss_clean($value) : $value;
    }

    // 通过post格式化ids
    public function get_post_ids($name = 'ids') {

        $in = array();
        $ids = self::post($name);
        if (!$ids) {
            return $in;
        }

        foreach ($ids as $i) {
            $i && $in[] = (int)$i;
        }

        return $in;
    }

    // 获取访客ip地址
    public function ip_address() {

        if ($this->ip_address) {
            return $this->ip_address;
        }

        if (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR', true)) {
            $client_ip = getenv('REMOTE_ADDR', true);
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // 验证规范
        if (!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $client_ip)) {
            $client_ip = '';
        }

        $this->ip_address = $client_ip ? $client_ip : '';
        $this->ip_address = str_replace(array(",", '(', ')', ',', chr(13), PHP_EOL), '', $this->ip_address);
        $this->ip_address = trim($this->ip_address);

        return $this->ip_address;
    }
    
    // ip转为实际地址
    public function ip2address($ip) {
        $ip_area = pc_base::load_sys_class('ip_area');
        return $ip_area->address($ip);
    }

    // 当前ip实际地址
    public function ip_address_info() {
        $ip_area = pc_base::load_sys_class('ip_area');
        return $ip_area->address($this->ip_address());
    }
    
    // 安全过滤
    public function get_user_agent() {
        $security = pc_base::load_sys_class('security');
        return str_replace(array('"', "'"), '', $security->xss_clean($_SERVER['HTTP_USER_AGENT'], true));
    }

    // 服务器ip地址
    public function server_ip() {

        if (isset($_SERVER['SERVER_ADDR'])
            && $_SERVER['SERVER_ADDR']
            && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            return $_SERVER['SERVER_ADDR'];
        }

        return gethostbyname($_SERVER['HTTP_HOST']);
    }

    // 后台分页
    public function page($url, $total, $dir = '') {

        $page = pc_base::load_sys_class('page');
        $config = require CACHE_PATH.'configs/apage.php';

        $config['base_url'] = $url.'&page={page}';
        $config['per_page'] = 10;
        $config['total_rows'] = $total;

        return $page->initialize($config)->create_links();
    }

    // Ftable分页
    public function table_page($url, $total, $config, $size) {

        $page = pc_base::load_sys_class('page');
        $config['base_url'] = $url;
        $config['per_page'] = $size;
        $config['total_rows'] = $total;

        return $page->initialize($config)->create_links();
    }

    /**
     * XSS Clean
     */
    public function xss_clean($str, $is_image = FALSE) {
        $security = pc_base::load_sys_class('security');
        return $security->xss_clean($str, $is_image);
    }

}