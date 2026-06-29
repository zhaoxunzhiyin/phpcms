<?php
class admin_bdts {

    private $zzurl = [
        'add' => 'http://data.zz.baidu.com/urls',
        'edit' => 'http://data.zz.baidu.com/urls',
    ];


    private $input,$config,$cache,$siteid,$zzconfig;
    
    public function __construct() {
        $this->input = pc_base::load_sys_class('input');
        $this->config = pc_base::load_sys_class('config');
        $this->cache = pc_base::load_sys_class('cache');
        $this->siteid = get_siteid();
    }

    // 配置信息
    public function getConfig() {

        if ($this->zzconfig) {
            return $this->zzconfig;
        }

        if (is_file(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'.php')) {
            $this->zzconfig = require CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'.php';
            return $this->zzconfig;
        }

        return [];
    }

    // 配置信息
    public function setConfig($data) {

        $this->config->file(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'.php', '站长配置文件', 32)->to_require($data);

    }

    // 手动提交url
    public function url_bdts($url, $name) {

        $config = $this->getConfig();
        if (!$config) {
            return dr_return_data(0, L('百度推送配置为空，不能推送'));
        }

        $uri = parse_url($url);
        $site = $uri['host'];
        if (!$site) {
            return dr_return_data(0, L('百度推送没有获取到内容url（'.$url.'）的host值，不能推送'));
        }

        $token = '';
        foreach ($config['bdts'] as $t) {
            if ($t['site'] == $site && !$token) {
                $token = $t['token'];
            }
        }
        if (!$token) {
            return dr_return_data(0, L('百度推送没有获取到内容url的Token，不能推送'));
        }

        $api = 'http://data.zz.baidu.com/urls?site='.$site.'&token='.$token;
        $urls = [$url];
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $rt = json_decode(curl_exec($ch), true);
        if ($rt['error']) {
            // 错误日志
            @file_put_contents(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php', date('Y-m-d H:i:s').' '.$name.'['.$url.'] - 失败 - '.$rt['message'].PHP_EOL, FILE_APPEND);
        } else {
            // 推送成功
            @file_put_contents(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php.php', date('Y-m-d H:i:s').' '.$name.'['.$url.'] - 成功'.PHP_EOL, FILE_APPEND);
        }

        return dr_return_data(1, 'ok');
    }

    // 进行百度推送
    public function module_bdts($mid, $url, $action = 'add') {

        $config = $this->getConfig();
        if (!$config) {
            log_message('error', '百度推送配置为空，不能推送');
            return;
        } elseif (!dr_in_array($mid, $config['use'])) {
            log_message('debug', '模块【'.$mid.'】百度推送配置没有开启，不能推送');
            return;
        }

        // pc域名
        $purl = $url;
        $uri = parse_url($purl);
        $site = $uri['host'];
        if (!$site) {
            log_message('error', '百度推送没有获取到内容url（'.$purl.'）的host值，不能推送');
            return;
        }

        // 获取移动端域名
        $murl = str_replace(siteurl($this->siteid), sitemobileurl($this->siteid), $url);
        if (dr_site_info('mobilemode', $this->siteid)==0 && dr_site_info('mobile_dirname', $this->siteid)) {
            $m_site = $site.'/'.dr_site_info('mobile_dirname', $this->siteid);
        } else {
            $uri = parse_url($murl);
            $m_site = $uri['host'];
        }
        if ($m_site && $m_site != $site) {
            $murl = str_replace($site, $m_site, $purl); // 替换移动端url
        } else {
            $m_site = '';
        }

        if (dr_site_info('mobilemode', $this->siteid)==0 && dr_site_info('mobile_dirname', $this->siteid) && strpos($m_site, dr_site_info('mobile_dirname', $this->siteid)) !== false) {
            list($m_site) = explode('/', $m_site);
        }

        // 百度主动推送
        if ($config['bdts']) {
            $token = '';
            $m_token = '';
            foreach ($config['bdts'] as $t) {
                if ($t['site'] == $site && !$token) {
                    $token = $t['token'];
                }
                if ($m_site && $t['site'] == $m_site && !$m_token) {
                    $m_token = $t['token'];
                }
            }

            if ($token) {
                // pc域名
                $api = $this->zzurl[$action].'?site='.$site.'&token='.$token;
                $urls = [$purl];
                $ch = curl_init();
                $options =  array(
                    CURLOPT_URL => $api,
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => implode("\n", $urls),
                    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                );
                curl_setopt_array($ch, $options);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $rt = json_decode(curl_exec($ch), true);
                if ($rt['error']) {
                    // 错误日志
                    @file_put_contents(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php.php', date('Y-m-d H:i:s').' PC端['.$purl.'] - '.$action.' - 失败 - '.$rt['message'].PHP_EOL, FILE_APPEND);
                } else {
                    // 推送成功
                    @file_put_contents(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php.php', date('Y-m-d H:i:s').' PC端['.$purl.'] - '.$action.' - 成功'.PHP_EOL, FILE_APPEND);
                }
            }
            if ($m_token && $m_site) {
                // 移动端
                $api = $this->zzurl[$action] . '?site=' . $m_site . '&token=' . $m_token;
                $urls = [$murl];
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => $api,
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => implode("\n", $urls),
                    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                );
                curl_setopt_array($ch, $options);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $rt = json_decode(curl_exec($ch), true);
                if ($rt['error']) {
                    // 错误日志
                    @file_put_contents(CACHE_PATH . 'caches_bdts/bdts'.$this->siteid.'_log.php.php', date('Y-m-d H:i:s') . ' 移动端[' . $murl . '] - '.$action.' - 失败 - ' . $rt['message'] . PHP_EOL, FILE_APPEND);
                } else {
                    // 推送成功
                    @file_put_contents(CACHE_PATH . 'caches_bdts/bdts'.$this->siteid.'_log.php.php', date('Y-m-d H:i:s') . ' 移动端[' . $murl . '] - '.$action.' - 成功' . PHP_EOL, FILE_APPEND);
                }
            }

        }

    }

}