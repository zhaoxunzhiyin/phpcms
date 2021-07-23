<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class check extends admin {
    private $_list = array(
        '01' => '文件上传检测',
        '02' => 'PHP环境检测',
        '03' => '目录权限检测',
        '04' => '后台入口名称检测',
        '05' => '数据库权限检测',
        '06' => '数据库表结构检测',
        '07' => '网站安全性检测',
        '08' => '数据负载优化检测',
        '09' => '域名绑定检测',
        '10' => '配置文件检测',
        '11' => '服务器环境检测',
    );

    function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->db = pc_base::load_model('sitemodel_model');
        $this->siteid = $this->get_siteid();
        if(!$this->siteid) $this->siteid = 1;
    }
    
    public function init() {
        $list = $this->_list;
        include $this->admin_tpl('check_index');
    }

    public function public_do_index() {

        $id = $this->input->get('id');

        switch ($id) {

            case '01':

                $post = intval(ini_get("post_max_size"));
                $file = intval(ini_get("upload_max_filesize"));

                if ($file > $post) {
                    dr_json(0,'系统配置不合理，post_max_size值('.$post.')必须大于upload_max_filesize值('.$file.')');
                } elseif ($file < 10) {
                    dr_json(1,'系统环境只允许上传'.$file.'MB文件，可以设置upload_max_filesize值提升上传大小');
                } elseif ($post < 10) {
                    dr_json(1,'系统环境要求每次发布内容不能超过'.$post.'MB（含文件），可以设置post_max_size值提升发布大小');
                }

                break;

            case '02':

                $rt = array();
                if (!function_exists('mb_substr')) {
                    $rt[] = 'PHP不支持mbstring扩展，必须开启';
                }
                if (!function_exists('imagettftext')) {
                    $rt[] = 'PHP扩展库：GD库未安装或GD库版本太低，可能无法正常显示验证码和图片缩略图';
                }
                if (!function_exists('curl_init')) {
                    $rt[] = 'PHP不支持CURL扩展，必须开启';
                }
                if (!function_exists('mb_convert_encoding')) {
                    $rt[] = 'PHP的mb函数不支持，无法使用百度关键词接口';
                }
                if (!function_exists('imagecreatetruecolor')) {
                    $rt[] = 'PHP的GD库版本太低，无法支持验证码图片';
                }
                if (!function_exists('ini_get')) {
                    $rt[] = '系统函数ini_get未启用，将无法获取到系统环境参数';
                }
                if (!function_exists('gzopen')) {
                    $rt[] = 'zlib扩展未启用，您将无法进行在线升级、无法下载应用插件等';
                }
                if (!function_exists('gzinflate')) {
                    $rt[] = '函数gzinflate未启用，您将无法进行在线升级、无法下载应用插件等';
                }
                if (!function_exists('fsockopen')) {
                    $rt[] = 'PHP不支持fsockopen，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等';
                }
                if (!function_exists('openssl_open')) {
                    $rt[] = 'PHP不支持openssl，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等';
                }
                if (!ini_get('allow_url_fopen')) {
                    $rt[] = 'allow_url_fopen未启用，远程图片无法保存、网络图片无法上传、可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等';
                }
                if (!class_exists('ZipArchive')) {
                    $rt[] = 'php_zip扩展未开启，无法使用解压缩功能';
                }

                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }

                break;

            case '03':

                $dir = array(
                    CACHE_PATH => '无法生成系统缓存文件',
                    SYS_AVATAR_PATH => '无法上传头像',
                    CACHE_PATH.'configs/' => '无法生成系统配置文件，会导致系统配置无效',
                    CACHE_PATH.'caches_commons/' => '无法生成系统缓存文件，会导致系统无法运行',
                    SYS_THUMB_PATH => '无法生成缩略图缓存文件',
                    SYS_UPLOAD_PATH => '无法上传附件',
                );

                foreach ($dir as $path => $note) {
                    if (!$this->check_put_path($path)) {
                        dr_json(0, $note.'【'.$path.'】');
                    }
                }

                if (!is_dir(CMS_PATH.'api/ueditor/')) {
                    $this->halt('百度编辑器目录不存在：'.CMS_PATH.'api/ueditor/', 0);
                }

                break;

            case '04':
                if (SELF == 'admin.php') {
                    $this->halt('为了系统安全，请修改根目录admin.php的文件名', 0);
                }
                break;

            case '05':

                $list = $this->db->list_tables();
                if (!$list) {
                    $this->halt("无法获取到数据表结构，需要为Mysql账号开启SHOW TABLE STATUS权限", 0);
                }

                $field = $this->db->query('SHOW FULL COLUMNS FROM `'.$this->db->db_tablepre.'admin`');
                if (!$field) {
                    $this->halt("无法通获取到数据表字段结构，需要为Mysql账号开启SHOW FULL COLUMNS权限", 0);
                }

                break;

            case '06':

                $rt = CACHE_PATH.'configs/database.php';
                $my = pc_base::load_config('database');
                $my = $my['default'];
                $database = file_get_contents($rt);
                $database_data = '<?php'.PHP_EOL.'if (!defined(\'IN_CMS\')) exit(\'No direct script access allowed\');'.PHP_EOL;
                $database_data .= 'return array('.PHP_EOL;
                $database_data .= '    \'default\' => array (
        \'hostname\' => \''.$my['hostname'].'\',
        \'port\' => '.$my['port'].',
        \'database\' => \''.$my['database'].'\',
        \'username\' => \''.$my['username'].'\',
        \'password\' => \''.$my['password'].'\',
        \'tablepre\' => \''.$my['tablepre'].'\',
        \'charset\' => \'utf8mb4\',
        \'type\' => \'mysqli\',
        \'debug\' => true,
        \'pconnect\' => '.$my['pconnect'].',
        \'autoconnect\' => '.$my['autoconnect'].'
    ),';
                $database_data.= PHP_EOL.');'.PHP_EOL.'?>';
                $this->db->query('ALTER DATABASE '.$my['database'].' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
                if (!strstr($database, 'IN_CMS') || $my['charset']!='utf8mb4' || $my['type']!='mysqli') {
                    file_put_contents($rt,$database_data);
                }

                $prefix = $this->db->db_tablepre;

                // 增加长度
                $this->db->query('ALTER TABLE `'.$prefix.'admin` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member_verify` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');

                $table = $prefix.'attachment_remote';
                if (!$this->db->table_exists('attachment_remote')) {
                    $this->db->query(format_create_sql('CREATE TABLE `'.$table.'` (
                    `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
                    `type` tinyint(2) NOT NULL COMMENT \'类型\',
                    `name` varchar(50) NOT NULL COMMENT \'名称\',
                    `url` varchar(255) NOT NULL COMMENT \'访问地址\',
                    `value` text NOT NULL COMMENT \'参数值\',
                    PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'远程附件表\''));
                }

                $this->db->table_name = $prefix.'admin';
                if ($this->db->field_exists('card')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` DROP `card`');
                }

                $this->db->table_name = $prefix.'admin_panel';
                if (!$this->db->field_exists('icon')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `name`');
                }

                $this->db->table_name = $prefix.'menu';
                if (!$this->db->field_exists('icon')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `data`');
                }
                $this->db->delete(array('id' => 1043));
                $this->db->delete(array('id' => 1044));
                $this->db->delete(array('id' => 1045));
                $this->db->delete(array('m' => 'video'));
                $this->db->update(array('name' => 'email_config', 'icon' => 'fa fa-envelope'),array('id' => 980, 'name' => 'sso_config'));
                $this->db->update(array('name' => 'connect_config', 'icon' => 'fa fa-html5'),array('id' => 981, 'name' => 'email_config'));
                $this->db->update(array('name' => 'setting_keyword_enable', 'icon' => 'fa fa-cog'),array('id' => 1093, 'name' => 'connect_config'));
                $menu = $this->db->get_one(array('name' => 'site_field_manage', 'parentid' => 64, 'm' => 'content', 'c' => 'sitemodel_field', 'a' => 'init', 'data' => '&modelid=0'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'site_field_manage\', 64, \'content\', \'sitemodel_field\', \'init\', \'&modelid=0\', \'fa-puzzle-piece\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'category_field_manage', 'parentid' => 43, 'm' => 'content', 'c' => 'sitemodel_field', 'a' => 'init', 'data' => '&modelid=-1'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'category_field_manage\', 43, \'content\', \'sitemodel_field\', \'init\', \'&modelid=-1\', \'fa fa-code\', 7, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'page_field_manage', 'parentid' => 43, 'm' => 'content', 'c' => 'sitemodel_field', 'a' => 'init', 'data' => '&modelid=-2'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'page_field_manage\', 43, \'content\', \'sitemodel_field\', \'init\', \'&modelid=-2\', \'fa-list\', 8, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'recycle', 'parentid' => 822, 'm' => 'content', 'c' => 'content', 'a' => 'recycle_init'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'recycle\', 822, \'content\', \'content\', \'recycle_init\', \'\', \'fa fa-trash-o\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'restore', 'parentid' => 822, 'm' => 'content', 'c' => 'content', 'a' => 'recycle'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'restore\', 822, \'content\', \'content\', \'recycle\', \'\', \'fa fa-reply\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'update', 'parentid' => 822, 'm' => 'content', 'c' => 'content', 'a' => 'update'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'update\', 822, \'content\', \'content\', \'update\', \'\', \'fa fa-refresh\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'content_all', 'parentid' => 822, 'm' => 'content', 'c' => 'content', 'a' => 'initall'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'content_all\', 822, \'content\', \'content\', \'initall\', \'\', \'fa fa-th-large\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'create_all', 'parentid' => 873, 'm' => 'content', 'c' => 'create_all_html', 'a' => 'all_update'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'create_all\', 873, \'content\', \'create_all_html\', \'all_update\', \'\', \'fa fa-file-code-o\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'public_error', 'parentid' => 977, 'm' => 'admin', 'c' => 'index', 'a' => 'public_error'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'public_error\', 977, \'admin\', \'index\', \'public_error\', \'\', \'fa fa-shield\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'public_error_log', 'parentid' => 977, 'm' => 'admin', 'c' => 'index', 'a' => 'public_error_log'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'public_error_log\', 977, \'admin\', \'index\', \'public_error_log\', \'\', \'fa fa-shield\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'attachment', 'parentid' => 30, 'm' => 'attachment', 'c' => 'attachment', 'a' => 'init'));
                if (!$menu) {
                    $parentid = $this->db->insert(array('name'=>'attachment', 'parentid'=>30, 'm'=>'attachment', 'c'=>'attachment', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-folder', 'listorder'=>3, 'display'=>'1'), true);
                } else {
                    $parentid = $menu['id'];
                }
                $menu = $this->db->get_one(array('name' => 'remote', 'm' => 'attachment', 'c' => 'attachment', 'a' => 'remote'));
                if (!$menu) {
                    $parentid = $this->db->insert(array('name'=>'remote', 'parentid'=>$parentid, 'm'=>'attachment', 'c'=>'attachment', 'a'=>'remote', 'data'=>'', 'icon'=>'fa fa-cloud', 'listorder'=>0, 'display'=>'1'), true);
                } else {
                    $parentid = $menu['id'];
                }
                $menu = $this->db->get_one(array('name' => 'remote_add', 'm' => 'attachment', 'c' => 'attachment', 'a' => 'remote_add'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'remote_add\', '.$parentid.', \'attachment\', \'attachment\', \'remote_add\', \'\', \'fa fa-plus\', 0, \'0\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'remote_edit', 'm' => 'attachment', 'c' => 'attachment', 'a' => 'remote_edit'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'remote_edit\', '.$parentid.', \'attachment\', \'attachment\', \'remote_edit\', \'\', \'fa fa-edit\', 0, \'0\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'remote_delete', 'm' => 'attachment', 'c' => 'attachment', 'a' => 'remote_delete'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'remote_delete\', '.$parentid.', \'attachment\', \'attachment\', \'remote_delete\', \'\', \'fa fa-trash-o\', 0, \'0\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'check_bom', 'parentid' => 977, 'm' => 'admin', 'c' => 'check_bom', 'a' => 'init'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'check_bom\', 977, \'admin\', \'check_bom\', \'init\', \'\', \'fa fa-code\', 0, \'1\', 1, 1, 1, 1, 1);');
                }
                $menu = $this->db->get_one(array('name' => 'check', 'parentid' => 977, 'm' => 'admin', 'c' => 'check', 'a' => 'init'));
                if (!$menu) {
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`name`, `parentid`, `m`, `c`, `a`, `data`, `icon`, `listorder`, `display`, `project1`, `project2`, `project3`, `project4`, `project5`) VALUES (\'check\', 977, \'admin\', \'check\', \'init\', \'\', \'fa fa-wrench\', 0, \'1\', 1, 1, 1, 1, 1);');
                }

                $this->db->table_name = $prefix.'site';
                if ($this->db->field_exists('uuid')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` DROP `uuid`');
                }
                if (!$this->db->field_exists('ishtml')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `ishtml` tinyint(1) unsigned NOT NULL DEFAULT \'1\' COMMENT \'首页静态\' AFTER `domain`');
                }
                if (!$this->db->field_exists('mobileauto')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobileauto` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'自动识别\' AFTER `ishtml`');
                }
                if (!$this->db->field_exists('mobilehtml')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobilehtml` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'生成静态\' AFTER `mobileauto`');
                }
                if (!$this->db->field_exists('not_pad')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `not_pad` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'将平板端排除\' AFTER `mobilehtml`');
                }
                if (!$this->db->field_exists('mobile_domain')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobile_domain` char(255) DEFAULT \'\' COMMENT \'手机域名\' AFTER `not_pad`');
                }
                if (!$this->db->field_exists('style')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `style` varchar(5) NOT NULL COMMENT \'\' AFTER `setting`');
                }

                $this->db->table_name = $prefix.'attachment';
                if (!$this->db->field_exists('filemd5')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `filemd5` varchar(50) NULL DEFAULT NULL COMMENT \'文件md5值\' AFTER `authcode`');
                }
                if (!$this->db->field_exists('remote')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `remote` tinyint(2) unsigned NOT NULL DEFAULT \'0\' COMMENT \'远程附件id\' AFTER `filemd5`');
                }
                if (!$this->db->field_exists('attachinfo')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `attachinfo` text NOT NULL COMMENT \'附件信息\' AFTER `remote`');
                }

                $this->db->table_name = $prefix.'module';
                $this->db->update(array('version'=>'1.0'),array('module'=>'dbsource'));
                $this->db->update(array('iscore'=>1),array('module'=>'digg', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'special', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'search', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'scan', 'iscore'=>0));

                $this->db->table_name = $prefix.'model';
                $this->db->delete(array('modelid' => 11));

                $this->db->table_name = $prefix.'model_field';
                $this->db->delete(array('modelid' => 11));
                //$this->db->update(array('tips'=>'<div class="mt-checkbox-inline"><label class="mt-checkbox mt-checkbox-outline"><input name="add_introduce" type="checkbox"  value="1" checked>是否截取内容<span></span></label><input type="text" name="introcude_length" value="200" size="3">字符至内容摘要\r\n<label class="mt-checkbox mt-checkbox-outline"><input type=\'\'checkbox\'\' name=\'\'auto_thumb\'\' value="1" checked>是否获取内容第<span></span></label><input type="text" name="auto_thumb_no" value="1" size="2" class="">张图片作为标题图片\r\n<label class="mt-checkbox mt-checkbox-outline"><input type=\'\'checkbox\'\' name=\'\'is_remove_a\'\' value="1" checked>去除站外链接<span></span></label>\r\n</div>'),array('formtype'=>'editor','tips'=>'<div class="content_attr"><label><input name="add_introduce" type="checkbox"  value="1" checked>是否截取内容</label><input type="text" name="introcude_length" value="200" size="3">字符至内容摘要\r\n<label><input type=\'\'checkbox\'\' name=\'\'auto_thumb\'\' value="1" checked>是否获取内容第</label><input type="text" name="auto_thumb_no" value="1" size="2" class="">张图片作为标题图片\r\n</div>'));
                $this->db->update(array('tips'=>'<div class="mt-checkbox-inline"><label class="mt-checkbox mt-checkbox-outline"><input name="add_introduce" type="checkbox"  value="1" checked>是否截取内容<span></span></label><input type="text" name="introcude_length" value="200" size="3">字符至内容摘要\r\n<label class="mt-checkbox mt-checkbox-outline"><input type=\'\'checkbox\'\' name=\'\'auto_thumb\'\' value="1" checked>是否获取内容第<span></span></label><input type="text" name="auto_thumb_no" value="1" size="2" class="">张图片作为标题图片\r\n<label class="mt-checkbox mt-checkbox-outline"><input type=\'\'checkbox\'\' name=\'\'is_remove_a\'\' value="1" checked>去除站外链接<span></span></label>\r\n</div>'),array('formtype'=>'editor'));
                $this->db->update(array('setting'=>'{"fieldtype":"int","format":"Y-m-d H:i:s","defaulttype":"0"}', 'iscore'=>0, 'isbase'=>0),array('field'=>'updatetime', 'formtype'=>'datetime'));
                break;

            case '07':

                $rt = array();
                // 搜索根目录
                $local = dr_file_map(CMS_PATH, 1); // 搜索根目录
                foreach ($local as $file) {
                    if (in_array(strtolower(substr(strrchr($file, '.'), 1)), array('zip', 'rar', 'sql'))) {
                        $rt[] = '文件不安全【/'.$file.'】请及时清理';
                    }
                    $str = file_get_contents(CMS_PATH.$file, 0, null, 0, 9286630);
                    if ($str && strlen($str) >= 9286630) {
                        $rt[] = '存在大文件文件【/'.$file.'】请及时清理';
                    }
                }
                
                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }
                
                dr_json(1,'完成');
                break;

            case '08':

                // 数据负载
                $rt = array();
                // 模块数据检测
                $module = $this->db->select(array('siteid'=>$this->siteid,'type'=>0));
                if ($module) {
                    foreach ($module as $m) {
                        $r = $this->_check_table_counts($m['tablename'], $m['tablename'] . '模块主表');
                        $r && $rt[] = $r;
                    }
                }
                $this->category_db = pc_base::load_model('category_model');
                if ($this->category_db->count() > MAX_CATEGORY) {
                    $rt[] = '<font color="red">栏目数据量超过'.MAX_CATEGORY.'个，会影响加载速度，建议对其进行数据优化</font>';
                }
                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }

                dr_json(1,'正常');
                break;

            case '09':

                // 域名检测
                if (!function_exists('stream_context_create')) {
                    $this->halt('函数没有被启用：stream_context_create', 0);
                }

                $error = $tips = array();
                $this->sitedb = pc_base::load_model('site_model');
                $data = $this->sitedb->select();
                if ($data) {
                    foreach ($data as $t) {
                        $url = '';
                        $cname = '';
                        if ($t['mobile_domain']) {
                            $url = $t['mobile_domain'] . 'api.php';
                        } else {
                            $tips[] = '当前站点没有绑定手机域名';
                        }
                        $cname = '移动端';

                        if ($url && $cname) {
                            $code = dr_catcher_data($url, 5);
                            if ($code != 'cms ok') {
                                $error[] = '['.$cname.']域名绑定异常，无法访问：' . $url . '，可以尝试手动访问此地址，如果提示cms ok就表示成功';
                            }
                        }
                    }
                }

                // 验证附件域名
                list($a, $b) = array(SYS_THUMB_PATH, SYS_THUMB_URL);
                list($c, $d) = array(SYS_AVATAR_PATH, SYS_AVATAR_URL);
                $domain = array(
                    array('name' => '附件域名', 'path' => SYS_UPLOAD_PATH, 'url' => SYS_UPLOAD_URL),
                    array('name' => '缩略图域名', 'path' => $a, 'url' => $b),
                    array('name' => '头像域名', 'path' => $c, 'url' => $d),
                );
                foreach ($domain as $t) {
                    if (!file_put_contents($t['path'].'api.html', 'cms ok')) {
                        dr_json(0, $t['path'].' 无法写入文件');
                    }
                    $code = dr_catcher_data($t['url'].'api.html', 5);
                    if ($code != 'cms ok') {
                        $error[] = '['.$t['name'].']异常，无法访问：' . $t['url'] . 'api.html，可以尝试手动访问此地址，如果提示cms ok就表示成功';
                    }
                }

                // 重复验证
                /*if (is_file(CACHE_PATH.'caches_commons/caches_data/sitelist.cache.php')) {
                    $domains = require CACHE_PATH.'caches_commons/caches_data/sitelist.cache.php';
                    if ($domains) {
                        // 获取去掉重复数据的数组
                        $unique_arr = array_unique ( $domains );
                        // 获取重复数据的数组
                        $repeat_arr = array_diff_assoc ( $domains, $unique_arr );
                        if ($repeat_arr) {
                            foreach ($repeat_arr as $t) {
                                $error[] = '域名【'.$t.'】被多处重复配置，可能会影响到此域名的作用域或访问异常';
                            }
                        }
                    }
                }*/

                if ($error) {
                    dr_json(0, implode('<br>', $error));
                } elseif ($tips) {
                    dr_json(1, implode('<br>', $tips));
                } else {
                    dr_json(1, '完成');
                }

                break;

            case '10':

                $version = CACHE_PATH.'configs/version.php';
                if (is_file($version)) {
                    $app_version = file_get_contents($version);
                }
                $app = pc_base::load_config('version');
                $version_data = '<?php'.PHP_EOL.'if (!defined(\'IN_CMS\')) exit(\'No direct script access allowed\');'.PHP_EOL;
                $version_data .= 'return array('.PHP_EOL;
                $version_data .= '\'pc_version\' => \''.($app['pc_version'] ? $app['pc_version'] : 'V9.6.3').'\',    //版本号
\'pc_release\' => \''.($app['pc_release'] ? $app['pc_release'] : '20170515').'\',    //更新日期
\'cms_version\' => \''.($app['cms_version'] ? $app['cms_version'] : 'V9.6.6').'\',    //cms 版本号
\'cms_release\' => \''.($app['cms_release'] ? $app['cms_release'] : dr_date(SYS_TIME, 'Ymd')).'\',    //cms 更新日期
\'update\' => \'0\',    //cms 更新';
                $version_data.= PHP_EOL.');'.PHP_EOL.'?>';
                if ($app['update'] || !strstr($app_version, 'IN_CMS') || !strstr($app_version, 'cms_version') || !strstr($app_version, 'cms_release') || !strstr($app_version, 'update')) {
                    file_put_contents($version,$version_data);
                }

                $rt = CACHE_PATH.'configs/system.php';
                $system = file_get_contents($rt);
                if (strstr($system, 'PHPCMS_PATH')) {
                    $system = str_replace("PHPCMS_PATH","CMS_PATH",$system);
                    file_put_contents($rt,$system);
                }
                $system_data = '<?php'.PHP_EOL.'if (!defined(\'IN_CMS\')) exit(\'No direct script access allowed\');'.PHP_EOL;
                $system_data .= 'return array('.PHP_EOL;
                $system_data .= '//网站路径
\'web_path\' => \''.pc_base::load_config('system','web_path').'\',
//Session配置
\'session_storage\' => \'mysqli\',
\'session_ttl\' => '.pc_base::load_config('system','session_ttl').',
\'session_savepath\' => '.str_replace(CACHE_PATH, 'CACHE_PATH.\'' ,pc_base::load_config('system','session_savepath')).'\',
\'session_n\' => '.pc_base::load_config('system','session_n').',
//Cookie配置
\'cookie_domain\' => \''.pc_base::load_config('system','cookie_domain').'\', //Cookie 作用域
\'cookie_path\' => \''.pc_base::load_config('system','cookie_path').'\', //Cookie 作用路径
\'cookie_pre\' => \''.pc_base::load_config('system','cookie_pre').'\', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
\'cookie_ttl\' => '.pc_base::load_config('system','cookie_ttl').', //Cookie 生命周期，0 表示随浏览器进程
//模板相关配置
\'tpl_root\' => \''.pc_base::load_config('system','tpl_root').'\', //模板保存物理路径
\'tpl_name\' => \''.pc_base::load_config('system','tpl_name').'\', //当前模板方案目录
\'tpl_css\' => \''.pc_base::load_config('system','tpl_css').'\', //当前样式目录
\'tpl_referesh\' => '.pc_base::load_config('system','tpl_referesh').',
\'tpl_edit\'=> '.pc_base::load_config('system','tpl_edit').',//是否允许在线编辑模板

//附件相关配置
\'attachment_stat\' => \''.pc_base::load_config('system','attachment_stat').'\',//是否记录附件使用状态 0 统计 1 统计， 注意: 本功能会加重服务器负担
\'attachment_file\' => \''.(pc_base::load_config('system','attachment_file') ? pc_base::load_config('system','attachment_file') : 0).'\',//附件是否使用分站 0 否 1 是
\'sys_attachment_save_id\' => '.(pc_base::load_config('system','sys_attachment_save_id') ? pc_base::load_config('system','sys_attachment_save_id') : 0).', //附件存储策略
\'sys_attachment_safe\' => '.(pc_base::load_config('system','sys_attachment_safe') ? pc_base::load_config('system','sys_attachment_safe') : 0).', //附件上传安全模式
\'sys_attachment_path\' => \''.(pc_base::load_config('system','sys_attachment_path') ? pc_base::load_config('system','sys_attachment_path') : '').'\', //附件上传路径
\'sys_attachment_save_type\' => '.(pc_base::load_config('system','sys_attachment_save_type') ? pc_base::load_config('system','sys_attachment_save_type') : 0).', //附件存储方式
\'sys_attachment_save_dir\' => \''.(pc_base::load_config('system','sys_attachment_save_dir') ? pc_base::load_config('system','sys_attachment_save_dir') : '').'\', //附件存储目录
\'sys_attachment_url\' => \''.(pc_base::load_config('system','sys_attachment_url') ? pc_base::load_config('system','sys_attachment_url') : '').'\', //附件访问地址
\'sys_avatar_path\' => \''.(pc_base::load_config('system','sys_avatar_path') ? pc_base::load_config('system','sys_avatar_path') : '').'\', //头像上传路径
\'sys_avatar_url\' => \''.(pc_base::load_config('system','sys_avatar_url') ? pc_base::load_config('system','sys_avatar_url') : '').'\', //头像访问地址
\'sys_thumb_path\' => \''.(pc_base::load_config('system','sys_thumb_path') ? pc_base::load_config('system','sys_thumb_path') : '').'\', //缩略图存储目录
\'sys_thumb_url\' => \''.(pc_base::load_config('system','sys_thumb_url') ? pc_base::load_config('system','sys_thumb_url') : '').'\', //缩略图访问地址

\'js_path\' => \''.pc_base::load_config('system','js_path').'\', //CDN JS
\'css_path\' => \''.pc_base::load_config('system','css_path').'\', //CDN CSS
\'img_path\' => \''.pc_base::load_config('system','img_path').'\', //CDN img
\'mobile_js_path\' => \''.(pc_base::load_config('system','mobile_js_path') ? pc_base::load_config('system','mobile_js_path') : pc_base::load_config('system','app_path').'mobile/statics/js/').'\', //CDN JS
\'mobile_css_path\' => \''.(pc_base::load_config('system','mobile_css_path') ? pc_base::load_config('system','mobile_css_path') : pc_base::load_config('system','app_path').'mobile/statics/css/').'\', //CDN CSS
\'mobile_img_path\' => \''.(pc_base::load_config('system','mobile_img_path') ? pc_base::load_config('system','mobile_img_path') : pc_base::load_config('system','app_path').'mobile/statics/images/').'\', //CDN img
\'app_path\' => \''.pc_base::load_config('system','app_path').'\',//动态域名配置地址
\'mobile_path\' => \''.(pc_base::load_config('system','mobile_path') ? pc_base::load_config('system','mobile_path') : pc_base::load_config('system','app_path').'mobile/').'\',//动态手机域名配置地址
\'editor\' => \''.(pc_base::load_config('system','editor') ? pc_base::load_config('system','editor') : 0).'\',    //编辑器模式    0 UEditor 1 CKEditor

\'charset\' => \''.pc_base::load_config('system','charset').'\', //网站字符集
\'timezone\' => \''.(pc_base::load_config('system','timezone')=='Etc/GMT-8' ? 8 : (pc_base::load_config('system','timezone') ? pc_base::load_config('system','timezone') : 8)).'\', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
\'debug\' => '.pc_base::load_config('system','debug').', //是否显示调试信息
\'needcheckcomeurl\' => \''.(pc_base::load_config('system','needcheckcomeurl') ? pc_base::load_config('system','needcheckcomeurl') : 1).'\',    //是否需要检查外部访问，1为启用，0为禁用
\'admin_log\' => '.pc_base::load_config('system','admin_log').', //是否记录后台操作日志
\'errorlog\' => '.pc_base::load_config('system','errorlog').', //1、保存错误日志到 cache/error_log.php | 0、在页面直接显示
\'gzip\' => '.pc_base::load_config('system','gzip').', //是否Gzip压缩后输出
\'auth_key\' => \''.pc_base::load_config('system','auth_key').'\', //密钥
\'lang\' => \''.pc_base::load_config('system','lang').'\',  //网站语言包
\'lock_ex\' => \''.pc_base::load_config('system','lock_ex').'\',  //写入缓存时是否建立文件互斥锁定（如果使用nfs建议关闭）

\'admin_founders\' => \''.pc_base::load_config('system','admin_founders').'\', //网站创始人ID，多个ID逗号分隔
\'execution_sql\' => '.pc_base::load_config('system','execution_sql').', //EXECUTION_SQL

\'html_root\' => \''.pc_base::load_config('system','html_root').'\',//生成静态文件路径
\'mobile_root\' => \''.(pc_base::load_config('system','mobile_root') ? pc_base::load_config('system','mobile_root') : '/mobile').'\',//生成手机静态文件路径

\'connect_enable\' => \''.pc_base::load_config('system','connect_enable').'\',    //是否开启外部通行证
\'sina_akey\' => \''.pc_base::load_config('system','sina_akey').'\',    //sina AKEY
\'sina_skey\' => \''.pc_base::load_config('system','sina_skey').'\',    //sina SKEY

\'snda_akey\' => \''.pc_base::load_config('system','snda_akey').'\',    //盛大通行证 akey
\'snda_skey\' => \''.pc_base::load_config('system','snda_skey').'\',    //盛大通行证 skey

\'qq_akey\' => \''.pc_base::load_config('system','qq_akey').'\',    //qq skey
\'qq_skey\' => \''.pc_base::load_config('system','qq_skey').'\',    //qq skey

\'qq_appkey\' => \''.pc_base::load_config('system','qq_appkey').'\',    //QQ号码登录 appkey
\'qq_appid\' => \''.pc_base::load_config('system','qq_appid').'\',    //QQ号码登录 appid
\'qq_callback\' => \''.pc_base::load_config('system','qq_callback').'\',    //QQ号码登录 callback

\'keywordapi\' => \''.(pc_base::load_config('system','keywordapi') ? pc_base::load_config('system','keywordapi') : 0).'\',    //关键词提取    0 百度 1 讯飞
\'baidu_aid\' => \''.pc_base::load_config('system','baidu_aid').'\',    //百度关键词提取 APPID
\'baidu_skey\' => \''.pc_base::load_config('system','baidu_skey').'\',    //百度关键词提取 APIKey
\'baidu_arcretkey\' => \''.pc_base::load_config('system','baidu_arcretkey').'\',    //百度关键词提取 Secret Key
\'baidu_qcnum\' => \''.(pc_base::load_config('system','baidu_qcnum') ? pc_base::load_config('system','baidu_qcnum') : 10).'\',    //百度关键词提取 百度分词数量
\'xunfei_aid\' => \''.pc_base::load_config('system','xunfei_aid').'\',    //讯飞关键词提取 APPID
\'xunfei_skey\' => \''.pc_base::load_config('system','xunfei_skey').'\',    //讯飞关键词提取 APIKey

\'admin_login_path\' => \''.pc_base::load_config('system','admin_login_path').'\',//自定义的后台登录地址';
                $system_data.= PHP_EOL.');'.PHP_EOL.'?>';
                if (!strstr($system, 'IN_CMS') || strstr($system, 'admin_url') || strstr($system, 'safe_card') || strstr($system, 'phpsso') || strstr($system, 'phpsso_appid') || strstr($system, 'phpsso_api_url') || strstr($system, 'phpsso_auth_key') || strstr($system, 'phpsso_version') || strstr($system, '\'timezone\' => \'Etc/GMT-8\'') || !strstr($system, 'attachment_file') || !strstr($system, 'sys_attachment_save_id') || !strstr($system, 'sys_attachment_safe') || !strstr($system, 'sys_attachment_path') || !strstr($system, 'sys_attachment_save_type') || !strstr($system, 'sys_attachment_save_dir') || !strstr($system, 'sys_attachment_url') || !strstr($system, 'sys_avatar_path') || !strstr($system, 'sys_avatar_url') || !strstr($system, 'sys_thumb_path') || !strstr($system, 'sys_thumb_url') || !strstr($system, 'mobile_js_path') || !strstr($system, 'mobile_css_path') || !strstr($system, 'mobile_img_path') || !strstr($system, 'mobile_path') || !strstr($system, 'editor') || !strstr($system, 'needcheckcomeurl') || !strstr($system, 'mobile_root') || !strstr($system, 'keywordapi') || !strstr($system, 'baidu_aid') || !strstr($system, 'baidu_skey') || !strstr($system, 'baidu_arcretkey') || !strstr($system, 'baidu_qcnum') || !strstr($system, 'xunfei_aid') || !strstr($system, 'xunfei_skey') || !strstr($system, 'admin_login_path')) {
                    file_put_contents($rt,$system_data);
                }

                if (pc_base::load_config('system','admin_login_path')) {
                    if (!is_dir(CMS_PATH.pc_base::load_config('system','admin_login_path'))) {
                        create_folder(CMS_PATH.pc_base::load_config('system','admin_login_path'));
                        $admin = file_get_contents(TEMPPATH.'web/admin.php');
                        $admin = str_replace("index.php","../index.php",$admin);
                        file_put_contents(CMS_PATH.pc_base::load_config('system','admin_login_path').'/index.php',$admin);
                    }
                    $index = file_get_contents(CMS_PATH.'cms/modules/admin/index.php');
                    if (!strstr($index, 'public function '.pc_base::load_config('system','admin_login_path'))) {
                        $admin_index = file_get_contents(TEMPPATH.'admin/index.php');
                        $admin_index = str_replace("public function login","public function ".pc_base::load_config('system','admin_login_path'),$admin_index);
                        file_put_contents(CMS_PATH."cms/modules/admin/index.php",$admin_index);
                    }
                }

                if (pc_base::load_config('system','tpl_edit')) {
                    dr_json(0, '系统开启了在线编辑模板权限，建议关闭此权限');
                }

                dr_json(1,'完成');
                break;

            case '11':
                // 服务器环境
                if (is_file(CMS_PATH.'test.php')) {
                    dr_json(0, '当网站正式上线后，根目录的test.php建议删除');
                }

                dr_json(1, '完成');
                break;

            case '99':

                break;

        }

        dr_json(1,'完成');
    }

    // 检查目录权限
    public function check_put_path($dir) {

        if (!$dir) {
            return 0;
        } elseif (!is_dir($dir)) {
            return 0;
        }

        $size = file_put_contents($dir.'test.html', 'test');
        if ($size === false) {
            return 0;
        } else {
            unlink($dir.'test.html');
            return 1;
        }
    }

    private function halt($msg, $code) {
        dr_json($code, $msg);
    }

    private function _check_table_counts($table, $name) {
        $this->db = pc_base::load_model('content_model');
        $this->db->table_name = $this->db->db_tablepre.$table;
        if (!$this->db->table_exists($table)) {
            return '数据表【'.$name.'/'.$this->db->db_tablepre.$table.'】不存在，请创建';
        }
        $counts = $this->db->count();
        if ($counts > 100000) {
            return '<font color="green">数据表【'.$name.'/'.$this->db->db_tablepre.$table.'】数据量超过10万，会影响加载速度，建议对其进行数据优化</font>';
        }
    }

}
?>