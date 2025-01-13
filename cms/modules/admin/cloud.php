<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

// 云服务
class cloud extends admin {
    private $input,$cache,$config,$db,$menu_db,$cloud;
    private $admin_url;
    private $service_url;

    function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->cache = pc_base::load_sys_class('cache');
        $this->config = pc_base::load_sys_class('config');
        $this->db = pc_base::load_model('site_model');
        $this->menu_db = pc_base::load_model('menu_model');
        // 不是超级管理员
        if (!cleck_admin(param::get_session('roleid'))) {
            dr_admin_msg(0,L('需要超级管理员账号操作'));
        }
        define('CMS_VERSION', pc_base::load_config('version','cms_version'));
        define('CMS_RELEASE', pc_base::load_config('version','cms_release'));
        define('CMS_ID', pc_base::load_config('license','cms_id'));
        define('CMS_LICENSE', pc_base::load_config('license','cms_license') ? pc_base::load_config('license','cms_license') : 'dev');
        define('CMS_UPDATETIME', pc_base::load_config('version','cms_updatetime'));
        define('CMS_DOWNTIME', pc_base::load_config('version','cms_downtime'));
        $this->cloud = CACHE_PATH.'cloud/';
        dr_mkdirs($this->cloud);

        list($this->admin_url) = explode('?', FC_NOW_URL);
        $this->service_url = CMS_CLOUD.'index.php?m=cloud&c=index&a=cloud&domain='.dr_get_domain_name(ROOT_URL).'&admin='.urlencode($this->admin_url).'&version='.CMS_VERSION.'&cms='.CMS_ID.'&license='.CMS_LICENSE.'&updatetime='.strtotime(CMS_UPDATETIME).'&downtime='.strtotime(CMS_DOWNTIME).'&sitename='.base64_encode(dr_site_info('name', 1)).'&siteurl='.urlencode(dr_site_info('domain', 1)).'&php='.PHP_VERSION.'&mysql='.$this->db->version().'&os='.PHP_OS;
    }

    // 我的网站
    public function init() {
        $show_header = true;
        include $this->admin_tpl('cloud_index');exit;
    }

    // 常用配置
    public function config() {
        $show_header = true;
        include $this->admin_tpl('cloud_config');exit;
    }

    // 程序升级
    public function upgrade() {
        $show_header = true;

        if (CMS_LICENSE == 'dev') {
            include $this->admin_tpl('cloud_login');exit;
        }

        $backup = str_replace('\\', '/', $this->_is_backup_file(CACHE_PATH.'bakup/update/cms/'));

        include $this->admin_tpl('cloud_update');exit;
    }

    // 验证授权
    public function login() {
        $show_header = true;
        if (IS_POST) {
            $post = $this->input->post('data');
            $surl = $this->service_url.'&action=update_login&username='.$post['username'].'&password='.md5($post['password']);
            $json = dr_catcher_data($surl);
            if (!$json) {
                dr_json(0, '本站：没有从服务端获取到数据，建议尝试离线方式');
            }
            $rt = dr_string2array($json);
            if (!$rt) {
                dr_json(0, '本站：从服务端获取到的数据不规范');
            }
            if (!$rt['code']) {
                dr_json(0, '服务端：'.$rt['msg']);
            }

            if (dr_strlen($rt['data']) > 8) {
                $myfile = CONFIGPATH.'license.php';
                if (is_file($myfile)) {
                    // 存在就更新id
                    if ($rt['data'] != CMS_LICENSE) {
                        $text = array('cms_id' => ''.$rt['id'].'','cms_license' => ''.$rt['data'].'','cms_name' => 'CMS','cms_url' => 'https://gitee.com/zhaoxunzhiyin/phpcms');
                        $this->config->file($myfile, '此文件是安装授权文件，每次下载安装包会自动生成，请勿修改', 32)->to_require($text);
                    }
                } else {
                    $text = array('cms_id' => ''.$rt['id'].'','cms_license' => ''.$rt['data'].'','cms_name' => 'CMS','cms_url' => 'https://gitee.com/zhaoxunzhiyin/phpcms');
                    $this->config->file($myfile, '此文件是安装授权文件，每次下载安装包会自动生成，请勿修改', 32)->to_require($text);
                }
                if (!is_file($myfile)) {
                    dr_json(0, '本站：'.str_replace(CMS_PATH, '', $myfile).'目录无法写入文件，请给于777权限');
                }
            }
            dr_json(1, $rt['msg'], $rt['data']);
        }

        include $this->admin_tpl('cloud_login_ajax');exit;
    }

    // 下载程序
    function down_file() {
        $show_header = true;
        $ls = intval($this->input->get('ls'));
        include $this->admin_tpl('cloud_down_file');exit;
    }
    
    // 判断备份目录是否有效
    private function _is_backup_file($path) {
        
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if (strpos($file, '.zip') !== false){
                        closedir($dh);
                        return $path;
                    }
                }
                closedir($dh);
            }
        }
        
        return '';
    }

    // 检查服务器版本
    public function check_version() {

        $cid = dr_safe_replace($this->input->get('id'));

        if ($cid == 'cms') {
            // 目录权限检查
            $dir = [
                CACHE_PATH,
                PC_PATH,
                CMS_PATH,
            ];
            foreach ($dir as $t) {
                if (!dr_check_put_path($t)) {
                    dr_json(0, L('目录【'.$t.'】不可写'));
                }
            }
        }

        $vid = dr_safe_replace($this->input->get('version'));
        $surl = $this->service_url.'&action=check_version&id='.$cid.'&version='.$vid;
        $json = dr_catcher_data($surl);
        if (!$json) {
            dr_json(0, '本站：没有从服务端获取到数据');
        }
        $rt = json_decode($json, true);
        dr_json($rt['code'], $rt['msg']);
    }

    // 执行更新程序的界面
    public function todo_update() {
        $show_header = true;
        $ls = dr_safe_replace($this->input->get('ls'));
        $dir = dr_safe_replace($this->input->get('dir'));
        $is_bf = intval($this->input->get('is_bf'));
        include $this->admin_tpl('cloud_todo_update');exit;
    }

    // 备份本站文件
    public function update_backup() {

        $dir = dr_safe_filename($this->input->get('dir'));
        if (!$dir) {
            dr_json(0, '本站：没有选择任何升级程序');
        }

        $is_bf = intval($this->input->get('is_bf'));
        if ($is_bf) {
            dr_json(1, '你选择不备份直接升级程序');
        }

        if ($dir == 'cms') {
            // 主程序备份
            $rt = pc_base::load_sys_class('file')->zip(
                CACHE_PATH.'bakup/update/cms/'.date('Y-m-d-H-i-s').'.zip',
                PC_PATH,
                [TPLPATH]
            );
        }

        if ($rt) {
            dr_json(0, '本站：文件备份失败（'.$rt.'）');
        }

        // 备份api
        pc_base::load_sys_class('file')->zip(
            CACHE_PATH.'bakup/update/api/'.date('Y-m-d-H-i-s').'.zip',
            CMS_PATH.'api'
        );

        // 备份模板
        pc_base::load_sys_class('file')->zip(
            CACHE_PATH.'bakup/update/template/'.date('Y-m-d-H-i-s').'.zip',
            TPLPATH
        );

        dr_json(1, 'ok');
    }

    // 服务器下载升级文件
    public function update_file() {

        $id = dr_safe_replace($this->input->get('id'));
        if (!$id) {
            dr_json(0, '本站：没有选择任何升级程序');
        }

        $surl = $this->service_url.'&action=update_file&ls='.dr_safe_replace($this->input->get('ls'));
        $json = dr_catcher_data($surl);
        if (!$json) {
            dr_json(0, '本站：没有从服务端获取到数据，建议尝试离线方式', $surl);
        }

        $data = dr_string2array($json);
        if (!$data) {
            CI_DEBUG && log_message('error', '服务端['.$surl.']返回数据异常：'.$json);
            dr_json(0, '本站：服务端数据异常，请重新下载', $json);
        } elseif (!$data['code']) {
            dr_json(0, $data['msg']);
        } elseif (!$data['data']['size']) {
            dr_json(0, '本站：服务端文件总大小异常');
        } elseif (!$data['data']['url']) {
            dr_json(0, '本站：服务端文件下载地址异常');
        }

        $this->cache->set_data('cloud-update-'.$id, $data['data'], 3600);

        dr_json(1, 'ok', $data['data']);
    }

    // 开始下载脚本
    public function update_file_down() {

        $id = dr_safe_replace($this->input->get('id'));
        $cache = $this->cache->get_data('cloud-update-'.$id);
        if (!$cache) {
            dr_json(0, '本站：授权验证缓存过期，请重试');
        } elseif (!$cache['size']) {
            dr_json(0, '本站：关键数据不存在，请重试');
        } elseif (!function_exists('fsockopen')) {
            dr_json(0, '本站：PHP环境不支持fsockopen，建议尝试离线方式');
        }

        // 执行下载文件
        $file = $this->cloud.$id.'.zip';

        set_time_limit(0);
        touch($file);
        // 做些日志处理
        if ($fp = fopen($cache['url'], "rb")) {
            if (!$download_fp = fopen($file, "wb")) {
                dr_json(0, '本站：无法写入远程文件', $cache['url']);
            }
            while (!feof($fp)) {
                if (!is_file($file)) {
                    // 如果临时文件被删除就取消下载
                    fclose($download_fp);
                    dr_json(0, '本站：临时文件被删除', $cache['url']);
                }
                fwrite($download_fp, fread($fp, 1024 * 8 ), 1024 * 8);
            }
            fclose($download_fp);
            fclose($fp);

            dr_json(1, 'ok');
        } else {
            unlink($file);
            dr_json(0, '本站：fopen打开远程文件失败，建议尝试离线方式', $cache['url']);
        }
    }

    // 检测下载进度
    public function update_file_check() {

        $id = dr_safe_replace($this->input->get('id'));
        $cache = $this->cache->get_data('cloud-update-'.$id);
        if (!$cache) {
            dr_json(0, '本站：授权验证缓存过期，请重试');
        } elseif (!$cache['size']) {
            dr_json(0, '本站：关键数据不存在，请重试');
        }

        // 执行下载文件
        $file = $this->cloud.$id.'.zip';
        if (is_file($file)) {
            $now = max(1, filesize($file));
            $jd = max(1, round($now / $cache['size'] * 100, 0)); // 进度百分百
            $count = $this->input->get('is_count') ? intval($this->input->get('is_count')) : 0; // 表示请求次数
            if (($count > 3 && $jd > 98) || ($this->input->get('is_jd') && $this->input->get('is_jd') == $jd)) {
                $jd = 100;
            }
            dr_json($jd, '<p><label class="rleft">需下载文件大小：'.format_file_size($cache['size']).'，已下载：'.format_file_size($now).'</label><label class="rright"><span class="ok">'.$jd.'%</span></label></p>');
        } else {
            dr_json(0, '本站：文件下载失败，建议尝试离线方式');
        }
    }

    // 升级程序
    public function update_file_install() {

        $id = dr_safe_replace($this->input->get('id'));
        $cache = $this->cache->get_data('cloud-update-'.$id);
        if (!$cache) {
            dr_json(0, '本站：授权验证缓存过期，请重试');
        }

        $file = $this->cloud.$id.'.zip';
        if (!is_file($file)) {
            dr_json(0, '本站：文件还没有被下载');
        } elseif (!class_exists('ZipArchive')) {
            dr_json(0, '本站：php_zip扩展未开启，无法在线安装功能，建议尝试离线方式');
        }

        // 解压目录
        $cmspath = $this->cloud.$id.'/';
        if (!pc_base::load_sys_class('file')->unzip($file, $cmspath)) {
            dr_json(0, '本站：文件解压失败');
        }

        unlink($file);
        // cms
        // api目录
        if (is_dir($cmspath.'api')) {
            $this->_copy_dir($cmspath.'api', CMS_PATH.'api');
            dr_dir_delete($cmspath.'api', 1);
        }
        // 缓存目录
        if (is_dir($cmspath.'caches')) {
            $this->_copy_dir($cmspath.'caches', CACHE_PATH);
            dr_dir_delete($cmspath.'caches', 1);
        }
        // CMS框架目录
        if (is_dir($cmspath.'cms')) {
            $this->_copy_dir($cmspath.'cms', PC_PATH);
            dr_dir_delete($cmspath.'cms', 1);
        }
        // 模板目录
        if (is_dir($cmspath.'templates')) {
            $this->_copy_dir($cmspath.'templates', TPLPATH);
            dr_dir_delete($cmspath.'templates', 1);
        }
        // statics目录
        if (is_dir($cmspath.'statics')) {
            $this->_copy_dir($cmspath.'statics', CMS_PATH.'statics');
            dr_dir_delete($cmspath.'statics', 1);
        }
        $this->_copy_dir($cmspath, CMS_PATH);
        dr_dir_delete($cmspath, 1);

        // 完成
        $this->cache->clear('cloud-update-'.$id);
        dr_json(1, '<p><label class="rleft">升级完成</label><label class="rright"><span class="ok">完成</span></label></p>');
    }

    // 文件对比
    public function compare() {
        $show_header = true;
        include $this->admin_tpl('cloud_bf');exit;
    }

    public function bf_count() {

        $surl = $this->service_url.'&action=bf_count';
        $json = dr_catcher_data($surl);
        if (!$json) {
            dr_json(0, '本站：没有从服务端获取到数据');
        }

        $data = dr_string2array($json);
        if (!$data) {
            CI_DEBUG && log_message('error', '云端返回数据异常：'.$json);
            dr_json(0, '本站：服务端数据异常，请重新再试');
        } elseif (!$data['code']) {
            dr_json(0, $data['msg']);
        }

        $this->cache->set_data('cloud-bf', $data['data'], 3600);

        dr_json(dr_count($data['data']), $data['msg']);
    }

    public function bf_check() {

        $page = max(1, intval($this->input->get('page')));
        $cache = $this->cache->get_data('cloud-bf');
        if (!$cache) {
            dr_json(0, '本站：数据缓存不存在');
        }

        $data = $cache[$page];
        if ($data) {
            $html = '';
            foreach ($data as $filename => $value) {
                if (strpos($filename, '/cms') === 0) {
                    $cname = 'PC_PATH'.substr($filename, 4);
                    $ofile = PC_PATH.substr($filename, 5);
                } else {
                    $cname = 'CMS_PATH'.$filename;
                    $ofile = CMS_PATH.substr($filename, 1);
                }
                $class = '';
                if (!is_file($ofile)) {
                    $ok = "<span class='error'>不存在</span>";
                    $class = 'p_error';
                } elseif (md5_file($ofile) != $value) {
                    $ok = "<span class='error'>有变化</span>";
                    $class = 'p_error';
                } else {
                    $ok = "<span class='ok'>正常</span>";
                }
                $html.= '<p class="'.$class.'"><label class="rleft">'.$cname.'</label><label class="rright">'.$ok.'</label></p>';
                if ($class) {
                    $html.= '<p class="rbf" style="display: none"><label class="rleft">'.(CI_DEBUG ? $ofile : $cname).'</label><label class="rright">'.$ok.'</label></p>';
                }
            }
            dr_json($page + 1, $html);
        }

        // 完成
        $this->cache->clear('cloud-bf');
        dr_json(100, '');
    }


    // 复制目录
    private function _copy_dir($src, $dst) {

        $dir = opendir($src);
        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        $src = rtrim($src, '/');
        $dst = rtrim($dst, '/');

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file) ) {
                    dr_mkdirs($dst . '/' . $file);
                    $this->_copy_dir($src . '/' . $file, $dst . '/' . $file);
                    continue;
                } else {
                    dr_mkdirs(dirname($dst . '/' . $file));
                    $rt = copy($src . '/' . $file, $dst . '/' . $file);
                    if (!$rt) {
                        // 验证目标是不是空文件
                        if (filesize($src . '/' . $file) > 1) {
                            $this->_error_msg($dst . '/' . $file, '移动失败，检查文件目录权限');
                        }

                    }
                }
            }
        }
        closedir($dir);
    }

    // 版本日志
    function log_show() {
        $url = $this->service_url.'&action=log_show&id='.$this->input->get('id', true).'&version='.$this->input->get('version', true);
        include $this->admin_tpl('cloud_online');exit;
    }

    // 错误进度
    private function _error_msg($filename, $msg) {
        $html = '<p class=" p_error"><label class="rleft">'.$filename.'</label><label class="rright">'.$msg.'</label></p>';
        dr_json(0, $html);
    }
}
