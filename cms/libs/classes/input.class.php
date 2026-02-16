<?php
class input {

    protected $ip_address;
    protected $_page_urlrule;

    // get post解析
    public function request($name, $xss = true) {
        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : (isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : false));
        return $xss ? $this->xss_clean($value) : $value;
    }
    
    // post解析
    public function post($name, $xss = true) {
        $value = isset($_POST[$name]) ? $_POST[$name] : false;
        return $xss ? $this->xss_clean($value) : $value;
    }

    // get解析
    public function get($name = '', $xss = true) {
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
    
    public function set_cookie($name, $value = '', $expire = 0) {
        pc_base::load_sys_class('param')::set_cookie($name, $value, $expire);
    }
    
    public function get_cookie($name, $default = false) {
        return pc_base::load_sys_class('param')::get_cookie($name, $default);
    }

    // inputip存储信息
    public function ip_info() {
        return $this->ip_address().'-'.(int)$_SERVER['REMOTE_PORT'];
    }

    // 获取访客ip地址
    public function ip_address() {

        if ($this->ip_address) {
            return $this->ip_address;
        }

        if (defined('IS_CDN_IP') && IS_CDN_IP && getenv(IS_CDN_IP)) {
            $client_ip = getenv(IS_CDN_IP);
        } elseif (getenv('HTTP_TRUE_CLIENT_IP')) {
            $client_ip = getenv('HTTP_TRUE_CLIENT_IP');
        } elseif (getenv('HTTP_ALI_CDN_REAL_IP')) {
            $client_ip = getenv('HTTP_ALI_CDN_REAL_IP');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR', true)) {
            $client_ip = getenv('REMOTE_ADDR', true);
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }

        if ($client_ip && strpos($client_ip, ',') !== false) {
            $client_ip = trim(explode(',', $client_ip)[0]);
        }

        // 验证规范
        if (!$this->is_ip($client_ip)) {
            $client_ip = '';
        }

        $this->ip_address = (string)$client_ip;
        $this->ip_address = str_replace([",", '(', ')', ',', chr(13), PHP_EOL], '', $this->ip_address);
        $this->ip_address = trim($this->ip_address);

        return $this->ip_address;
    }

    /**
     * 检测是否是合法的IP地址
     */
    public function is_ip($ip, $type = '') {

        switch (strtolower($type)) {
            case 'ipv4':
                $flag = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $flag = FILTER_FLAG_IPV6;
                break;
            default:
                $flag = 0;
                break;
        }

        return boolval(filter_var($ip, FILTER_VALIDATE_IP, $flag));
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
        return dr_safe_replace(str_replace(array('"', "'"), '', pc_base::load_sys_class('security')->xss_clean((string)$_SERVER['HTTP_USER_AGENT'], true)));
    }

    /**
     * 系统错误日志
     */
    public function log($level, $message, $context = []) {

        $message = $this->interpolate($message, $context);

        if (! is_string($message)) {
            $message = print_r($message, true);
        }

        $message = strtoupper($level) . ' - '.dr_date(SYS_TIME, 'Y-m-d H:i:s'). ' --> '.$message;

        if ($level == 'debug') {
            pc_base::load_sys_class('debug')::trace($message);
        } else {
            pc_base::load_sys_class('debug')::addmsg('<span style="color:red;">'.$message.'</span>', 2);
        }

        $path = CACHE_PATH.'caches_error/caches_data/';
        dr_mkdirs($path);
        $file = $path . 'log-' . dr_date(SYS_TIME, 'Y-m-d') . '.php';
        if (!is_file($file)) {
            file_put_contents($file, "<?php if (!defined('IN_CMS')) exit('No direct script access allowed');?>".PHP_EOL.$message);
        } else {
            file_put_contents($file, $message.PHP_EOL, FILE_APPEND);
        }

        return true;
    }
    /**
     * Replaces any placeholders in the message with variables
     * from the context, as well as a few special items like:
     *
     * {session_vars}
     * {post_vars}
     * {get_vars}
     * {env}
     * {env:foo}
     * {file}
     * {line}
     *
     * @param mixed $message
     *
     * @return mixed
     */
    protected function interpolate($message, array $context = []) {
        if (! is_string($message)) {
            return $message;
        }

        // build a replacement array with braces around the context keys
        $replace = [];

        foreach ($context as $key => $val) {
            // Verify that the 'exception' key is actually an exception
            // or error, both of which implement the 'Throwable' interface.
            if ($key === 'exception' && $val instanceof Throwable) {
                $val = $val->getMessage() . ' ' . $val->getFile() . ':' . $val->getLine();
            }

            // todo - sanitize input before writing to file?
            $replace['{' . $key . '}'] = $val;
        }

        // Add special placeholders
        $replace['{post_vars}'] = '$_POST: ' . print_r($_POST, true);
        $replace['{get_vars}']  = '$_GET: ' . print_r($_GET, true);

        // Allow us to log the file/line that we are logging from
        if (strpos($message, '{file}') !== false) {
            list($file, $line) = $this->determineFile();

            $replace['{file}'] = $file;
            $replace['{line}'] = $line;
        }

        // Match up environment variables in {env:foo} tags.
        if (strpos($message, 'env:') !== false) {
            preg_match('/env:[^}]+/', $message, $matches);

            if ($matches) {
                foreach ($matches as $str) {
                    $key                 = str_replace('env:', '', $str);
                    $replace["{{$str}}"] = $_ENV[$key] ?? 'n/a';
                }
            }
        }

        if (isset($_SESSION)) {
            $replace['{session_vars}'] = '$_SESSION: ' . print_r($_SESSION, true);
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Determines the file and line that the logging call
     * was made from by analyzing the backtrace.
     * Find the earliest stack frame that is part of our logging system.
     */
    public function determineFile(): array {
        $logFunctions = [
            'log_message',
            'log',
            'error',
            'debug',
            'info',
            'warning',
            'critical',
            'emergency',
            'alert',
            'notice',
        ];

        // Generate Backtrace info
        $trace = \debug_backtrace(0);

        // So we search from the bottom (earliest) of the stack frames
        $stackFrames = \array_reverse($trace);

        // Find the first reference to a Logger class method
        foreach ($stackFrames as $frame) {
            if (\in_array($frame['function'], $logFunctions, true)) {
                $file = isset($frame['file']) ? ($frame['file']) : 'unknown';
                $line = $frame['line'] ?? 'unknown';

                return [
                    $file,
                    $line,
                ];
            }
        }

        return [
            'unknown',
            'unknown',
        ];
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

    // 分页
    public function page($url, $total, $pagesize = 10, $cur_page = '', $first_url = '', $name = 'page', $page_name = 'page') {

        $page = pc_base::load_sys_class('page');
        if (defined('IS_ADMIN') && IS_ADMIN && !defined('IS_HTML')) {
            // 使用后台分页规则
            $config = require CONFIGPATH.'apage.php';
        } else {
            // 这里要支持移动端分页条件
            !$name && $name = 'page';
            defined('IS_MEMBER') && IS_MEMBER && $name = 'member';
            $file = 'page/'.(pc_base::load_sys_class('service')->is_mobile() ? 'mobile' : 'pc').'/'.(dr_safe_filename($name)).'.php';
            if (is_file(CONFIGPATH.$file)) {
                $config = require CONFIGPATH.$file;
            } else {
                exit('无法找到分页配置文件【'.$file.'】');
            }
        }

        $_GET[$page_name] = intval($cur_page);

        !$url && $url = '此标签没有设置urlrule参数';

        $this->_page_urlrule = str_replace(['{$page}', '[page]', '%7Bpage%7D', '%5Bpage%5D', '%7bpage%7d', '%5bpage%5d', '%7B%24page%7D', '%5B%24page%5D', '%7b%24page%7d', '%5b%24page%5d'], '{page}', $url);
        $config['base_url'] = $this->_page_urlrule;
        $config['first_url'] = $first_url ? $first_url : '';
        $config['per_page'] = $pagesize;
        $config['page_name'] = $page_name;
        $config['total_rows'] = $total;
        $config['use_page_numbers'] = TRUE;

        return $page->initialize($config)->create_links();
    }

    /**
     * XSS Clean
     */
    public function xss_clean($str, $is_image = FALSE) {
        return pc_base::load_sys_class('security')->xss_clean($str, $is_image);
    }

}