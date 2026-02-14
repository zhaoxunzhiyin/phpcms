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
        '06' => '模板完整性检测',
        '07' => '数据库表结构检测',
        '08' => '网站安全性检测',
        '09' => '数据负载优化检测',
        '10' => '域名绑定检测',
        '11' => '配置文件检测',
        '12' => '服务器环境检测',
        '13' => '模块兼容性检测',
    );
    private $input,$db,$siteid,$content_db,$sitedb;

    function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->db = pc_base::load_model('sitemodel_model');
        $this->siteid = $this->get_siteid();
    }
    
    public function init() {
        $show_header = true;
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
                if (!fopen(CMS_CLOUD, "rb")) {
                    $rt[] = 'fopen无法获取远程数据，无法使用在线升级';
                }

                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }

                break;

            case '03':

                $rt = array();
                $dir = array(
                    CACHE_PATH => '无法生成系统缓存文件',
                    SYS_AVATAR_PATH => '无法上传头像',
                    CONFIGPATH => '无法生成系统配置文件，会导致系统配置无效',
                    CACHE_PATH.'caches_commons/' => '无法生成系统缓存文件，会导致系统无法运行',
                    CACHE_PATH.'caches_file/' => '无法生成系统缓存文件，会导致系统无法运行',
                    CACHE_PATH.'cloud/' => '无法在线升级',
                    SYS_THUMB_PATH => '无法生成缩略图缓存文件',
                    SYS_UPLOAD_PATH => '无法上传附件',
                    TPLPATH => '无法创建模块模板和模型模板',
                );

                foreach ($dir as $path => $note) {
                    if (!is_dir($path)) {
                       dr_mkdirs($path);
                    }
                    if (!$this->check_put_path($path)) {
                        $rt[] = $note.'【'.(IS_DEV ? $path : dr_safe_replace_path($path)).'】';
                    }
                }

                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
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

                $rt = array();

                // 模板文件
                define('SITE_TEMPLATE', dr_site_info('default_style', $this->siteid));
                if (!is_file(TPLPATH.SITE_TEMPLATE.'/pc/content/index.html')) {
                    $rt[] = '前端模板【电脑版】不存在：TPLPATH/'.SITE_TEMPLATE.'/pc/content/index.html';
                }
                if (!is_file(TPLPATH.SITE_TEMPLATE.'/pc/member/index.html')) {
                    $rt[] = '用户中心模板【电脑版】不存在：TPLPATH/'.SITE_TEMPLATE.'/pc/member/index.html';
                }
                // 必备模板检测
                foreach (array('message.html', 'msg.html') as $tt) {
                    if (!is_file(TPLPATH.SITE_TEMPLATE.'/pc/content/'.$tt)) {
                        $rt[] = '前端模板【电脑版】不存在：TPLPATH/'.SITE_TEMPLATE.'/pc/content/'.$tt;
                    }
                }
                
                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }

                // 移动端模板检测
                if (!is_file(TPLPATH.SITE_TEMPLATE.'/mobile/content/index.html')) {
                    $this->halt('前端模板【手机版】不存在：TPLPATH/'.SITE_TEMPLATE.'/mobile/content/index.html', 1);
                }
                
                dr_json(1,'完成');

                break;

            case '07':

                $rt = CONFIGPATH.'database.php';
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

                $datas = $this->db->select(array('type'=>0,'disabled'=>0));
                foreach ($datas as $r) {
                    $number = $this->_table_counts($r['tablename']);
                    $this->db->update(array('items'=>$number),array('modelid'=>$r['modelid']));
                }

                $prefix = $this->db->db_tablepre;

                // 增加长度
                $this->db->query('ALTER TABLE `'.$prefix.'admin` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');
                $this->db->query('ALTER TABLE `'.$prefix.'admin` CHANGE `email` `email` VARCHAR(50) NOT NULL COMMENT \'邮箱地址\';');
                $this->db->query('ALTER TABLE `'.$prefix.'admin` CHANGE `lastloginip` `lastloginip` VARCHAR(200) NOT NULL COMMENT \'最后登录Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'admin` CHANGE `roleid` `roleid` VARCHAR(255) NOT NULL COMMENT \'权限id\';');
                $this->db->query('ALTER TABLE `'.$prefix.'category` CHANGE `items` `items` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'数据量\';');
                $this->db->query('ALTER TABLE `'.$prefix.'menu` CHANGE `a` `a` char(210) NOT NULL default \'\' COMMENT \'方法名\';');
                $this->db->query('ALTER TABLE `'.$prefix.'model` CHANGE `items` `items` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'数据量\';');
                $this->db->query('ALTER TABLE `'.$prefix.'model_field` CHANGE `modelid` `modelid` smallint(5) NOT NULL DEFAULT \'0\' COMMENT \'模型id\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `email` `email` char(50) NOT NULL COMMENT \'邮箱地址\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `regip` `regip` char(200) NOT NULL COMMENT \'注册Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `lastip` `lastip` char(200) NOT NULL COMMENT \'登录Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member` CHANGE `connectid` `connectid` char(255) NOT NULL DEFAULT \'\' COMMENT \'快捷登录\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member_verify` CHANGE `encrypt` `encrypt` VARCHAR(50) NOT NULL COMMENT \'随机加密码\';');
                $this->db->query('ALTER TABLE `'.$prefix.'member_verify` CHANGE `regip` `regip` char(200) NOT NULL COMMENT \'注册Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'attachment` CHANGE `filename` `filename` VARCHAR(255) NOT NULL COMMENT \'原文件名\';');
                $this->db->query('ALTER TABLE `'.$prefix.'attachment` CHANGE `uploadip` `uploadip` char(200) NOT NULL COMMENT \'上传Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'ipbanned` CHANGE `ip` `ip` char(200) NOT NULL COMMENT \'Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'log` CHANGE `ip` `ip` VARCHAR(200) NOT NULL COMMENT \'Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'pay_account` CHANGE `ip` `ip` char(200) NOT NULL DEFAULT \'0.0.0.0\' COMMENT \'Ip\';');
                $this->db->query('ALTER TABLE `'.$prefix.'times` CHANGE `ip` `ip` char(200) NOT NULL COMMENT \'Ip\';');

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
                if (!$this->db->field_exists('login_attr')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `login_attr` varchar(100) NOT NULL DEFAULT \'\' COMMENT \'登录附加验证字符\' AFTER `password`');
                }
                if (!$this->db->field_exists('phone')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `phone` varchar(20) NOT NULL COMMENT \'手机号码\' AFTER `email`');
                }
                if (!$this->db->field_exists('islock')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `islock` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'账号锁定标识\' AFTER `phone`');
                }

                $table = $prefix.'admin_login';
                if (!$this->db->table_exists('admin_login')) {
                    $this->db->query(format_create_sql('CREATE TABLE `'.$table.'` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `uid` mediumint(8) unsigned DEFAULT NULL COMMENT \'会员uid\',
                    `is_login` int(10) unsigned DEFAULT NULL COMMENT \'是否首次登录\',
                    `is_repwd` int(10) unsigned DEFAULT NULL COMMENT \'是否重置密码\',
                    `updatetime` int(10) unsigned NOT NULL COMMENT \'修改密码时间\',
                    `logintime` int(10) unsigned NOT NULL COMMENT \'最近登录时间\',
                    PRIMARY KEY (`id`),
                    KEY `uid` (`uid`),
                    KEY `logintime` (`logintime`),
                    KEY `updatetime` (`updatetime`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'账号记录\''));
                }

                $this->db->table_name = $prefix.'admin_role_priv';
                if (!$this->db->field_exists('menuid')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `menuid` mediumint(8) UNSIGNED NOT NULL COMMENT \'菜单id\' AFTER `roleid`');
                }

                $this->db->table_name = $prefix.'member';
                if (!$this->db->field_exists('login_attr')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `login_attr` varchar(100) NOT NULL DEFAULT \'\' COMMENT \'登录附加验证字符\' AFTER `password`');
                }

                $this->db->table_name = $prefix.'member_group';
                if (!$this->db->field_exists('allowdownfile')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `allowdownfile` tinyint(1) NOT NULL COMMENT \'附件下载权限\' AFTER `allowattachment`');
                }
                if (!$this->db->field_exists('filesize')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `filesize` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'附件总空间\' AFTER `allowdownfile`');
                }

                $table = $prefix.'member_login';
                if (!$this->db->table_exists('member_login')) {
                    $this->db->query(format_create_sql('CREATE TABLE `'.$table.'` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `uid` mediumint(8) unsigned DEFAULT NULL COMMENT \'会员uid\',
                    `is_login` int(10) unsigned DEFAULT NULL COMMENT \'是否首次登录\',
                    `is_repwd` int(10) unsigned DEFAULT NULL COMMENT \'是否重置密码\',
                    `updatetime` int(10) unsigned NOT NULL COMMENT \'修改密码时间\',
                    `logintime` int(10) unsigned NOT NULL COMMENT \'最近登录时间\',
                    PRIMARY KEY (`id`),
                    KEY `uid` (`uid`),
                    KEY `logintime` (`logintime`),
                    KEY `updatetime` (`updatetime`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'账号记录\''));
                }

                $this->db->table_name = $prefix.'admin_panel';
                if (!$this->db->field_exists('icon')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `name`');
                }

                $this->db->table_name = $prefix.'category';
                if (!$this->db->field_exists('disabled')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `disabled` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'状态\' AFTER `letter`');
                }
                $categorys = $this->db->select();
                foreach ($categorys as $r) {
                    $this->db->update(array('setting'=>dr_array2string($this->string2array($r['setting']))),array('catid'=>$r['catid']));
                }

                $this->db->table_name = $prefix.'menu';
                if (!$this->db->field_exists('icon')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `data`');
                }
                for ($i = 1; $i <= 5; $i++) {
                    if ($this->db->field_exists('project'.$i)) {
                        $this->db->query('ALTER TABLE `'.$this->db->table_name.'` DROP `project'.$i.'`');
                    }
                }

                $this->db->table_name = $prefix.'site';
                if ($this->db->field_exists('uuid')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` DROP `uuid`');
                }
                if (!$this->db->field_exists('site_close')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `site_close` tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'网站状态\' AFTER `domain`');
                }
                if (!$this->db->field_exists('site_close_msg')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `site_close_msg` char(255) DEFAULT \'\' COMMENT \'网站关闭理由\' AFTER `site_close`');
                }
                if (!$this->db->field_exists('ishtml')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `ishtml` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'首页静态\' AFTER `site_close_msg`');
                }
                if (!$this->db->field_exists('mobilemode')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobilemode` tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'访问模式\' AFTER `ishtml`');
                } else {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` CHANGE `mobilemode` `mobilemode` tinyint(1) NOT NULL default \'0\' COMMENT \'访问模式\';');
                }
                if (!$this->db->field_exists('mobileauto')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobileauto` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'自动识别\' AFTER `mobilemode`');
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
                if (!$this->db->field_exists('mobile_dirname')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `mobile_dirname` char(255) DEFAULT \'\' COMMENT \'手机目录\' AFTER `mobile_domain`');
                }
                if (!$this->db->field_exists('style')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `style` varchar(5) NOT NULL COMMENT \'\' AFTER `setting`');
                }
                $sites = $this->db->select();
                foreach ($sites as $r) {
                    if (isset($r['setting']) && $r['setting']) {
                        $this->db->update(array('setting'=>dr_array2string($this->string2array($r['setting']))),array('siteid'=>$r['siteid']));
                    }
                    if (!is_dir(TPLPATH.$r['default_style'].'/pc/') && is_dir(TPLPATH.$r['default_style'].'/mobile/') && !is_dir(TPLPATH.$r['default_style'].'_style/mobile/content/')) {
                        pc_base::load_sys_class('file')->copy_dir(TPLPATH.$r['default_style'].'/mobile/', TPLPATH.$r['default_style'].'/mobile/', TPLPATH.$r['default_style'].'_style/mobile/content/');
                        dr_dir_delete(TPLPATH.$r['default_style'].'/mobile/', TRUE);
                    }
                    if (!is_dir(TPLPATH.$r['default_style'].'/pc/') && is_dir(TPLPATH.$r['default_style'].'/mobile_search/') && !is_dir(TPLPATH.$r['default_style'].'_style/mobile/search/')) {
                        pc_base::load_sys_class('file')->copy_dir(TPLPATH.$r['default_style'].'/mobile_search/', TPLPATH.$r['default_style'].'/mobile_search/', TPLPATH.$r['default_style'].'_style/mobile/search/');
                        dr_dir_delete(TPLPATH.$r['default_style'].'/mobile_search/', TRUE);
                    }
                    if (!is_dir(TPLPATH.$r['default_style'].'/pc/') && is_dir(TPLPATH.$r['default_style'].'/') && !is_dir(TPLPATH.$r['default_style'].'_style/pc/')) {
                        pc_base::load_sys_class('file')->copy_dir(TPLPATH.$r['default_style'].'/', TPLPATH.$r['default_style'].'/', TPLPATH.$r['default_style'].'_style/pc/');
                    }
                    if (!is_dir(TPLPATH.$r['default_style'].'/pc/') && is_dir(TPLPATH.$r['default_style'].'_style/') && is_dir(TPLPATH.$r['default_style'].'/')) {
                        dr_dir_delete(TPLPATH.$r['default_style'].'/', TRUE);
                        rename(TPLPATH.$r['default_style'].'_style/', TPLPATH.$r['default_style'].'/');
                        copy(TPLPATH.$r['default_style'].'/pc/config.php', TPLPATH.$r['default_style'].'/config.php');
                        unlink(TPLPATH.$r['default_style'].'/pc/config.php');
                    }
                }

                $this->db->table_name = $prefix.'attachment';
                if (!$this->db->field_exists('isadmin')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `isadmin` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'判断用户前端权限\' AFTER `userid`');
                }
                if (!$this->db->field_exists('filemd5')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `filemd5` varchar(50) NOT NULL COMMENT \'文件md5值\' AFTER `authcode`');
                }
                if (!$this->db->field_exists('remote')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `remote` tinyint(2) unsigned NOT NULL DEFAULT \'0\' COMMENT \'远程附件id\' AFTER `filemd5`');
                }
                if (!$this->db->field_exists('attachinfo')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `attachinfo` text NOT NULL COMMENT \'附件信息\' AFTER `remote`');
                }
                if (!$this->db->field_exists('related')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `related` varchar(50) NOT NULL COMMENT \'相关表标识\' AFTER `attachinfo`');
                }

                $this->db->table_name = $prefix.'module';
                $this->db->update(array('version'=>'1.0'),array('module'=>'dbsource', 'version'=>''));
                $this->db->update(array('iscore'=>1),array('module'=>'digg', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'special', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'search', 'iscore'=>0));
                $this->db->update(array('iscore'=>1),array('module'=>'scan', 'iscore'=>0));
                if (!$this->db->count(array('module'=>'404'))) {
                    $this->db->insert(array('module' => '404', 'name' => '404错误', 'url' => '', 'iscore' => 1, 'version' => '1.0', 'description' => '', 'setting' => '', 'listorder' => '0', 'disabled' => '0', 'installdate' => dr_date(SYS_TIME, 'Y-m-d'), 'updatedate' => dr_date(SYS_TIME, 'Y-m-d')));
                }
                if ($this->db->count(array('module'=>'mobile'))) {
                    $this->db->delete(array('module' => 'mobile'));
                }
                $modules = $this->db->select();
                foreach ($modules as $r) {
                    if (isset($r['setting']) && $r['setting']) {
                        $this->db->update(array('setting'=>dr_array2string($this->string2array($r['setting']))),array('module'=>$r['module']));
                    }
                }

                $this->db->table_name = $prefix.'model';
                $models = $this->db->select();
                foreach ($models as $r) {
                    if (!$r['type']) {
                        $this->_alter_table($r['tablename']);
                    }
                    if (isset($r['setting']) && $r['setting']) {
                        $this->db->update(array('setting'=>dr_array2string($this->string2array($r['setting']))),array('modelid'=>$r['modelid']));
                    } else {
                        $this->db->update(array('setting'=>'{"pcatpost":"0","previous":"0","updatetime_select":"0","desc_auto":"0","desc_limit":"","desc_clear":"0","order":"listorder DESC,updatetime DESC","search_time":"updatetime","search_first_field":"title","list_field":{"title":{"use":"1","name":"主题","width":"","func":"title"},"username":{"use":"1","name":"用户名","width":"100","func":"author"},"updatetime":{"use":"1","name":"更新时间","width":"160","func":"datetime"},"listorder":{"use":"1","name":"排序","width":"100","center":"1","func":"save_text_value"}},"category_template":"category","list_template":"list","show_template":"show","admin_list_template":"","member_add_template":"","search":{"use":"0","catsync":"0","search_404":"0","search_catid":"0","search_param":"0","complete":"0","is_like":"0","is_double_like":"0","search_time":"2","length":"0","maxlength":"0","field":""}}'),array('modelid'=>$r['modelid'], 'type'=>0));
                    }
                }
                $model = $this->db->get_one(array('modelid' => 11, 'siteid' => 1, 'name' => '视频模型', 'tablename' => 'video'));
                if ($model) {
                    $video_count = $this->_table_counts('video');
                    if (!$video_count) {
                        $this->db->delete(array('modelid' => 11, 'siteid' => 1, 'name' => '视频模型', 'tablename' => 'video'));
                        $this->db->query('DROP TABLE IF EXISTS `'.$prefix.'video`');
                        $this->db->query('DROP TABLE IF EXISTS `'.$prefix.'video_data`');
                        $this->db->table_name = $prefix.'model_field';
                        $this->db->delete(array('modelid' => 11));
                    }
                }

                $this->db->table_name = $prefix.'linkage';
                if (!$this->db->field_exists('style')) {
                    $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `style` tinyint(1) unsigned NOT NULL COMMENT \'菜单风格\' AFTER `name`');
                }
                if ($this->db->field_exists('linkageid')) {
                    $this->db->query('DROP TABLE IF EXISTS `'.$this->db->table_name.'`');
                    $this->db->query(format_create_sql('CREATE TABLE `'.$this->db->table_name.'` (
                    `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL COMMENT \'菜单名称\',
                    `style` tinyint(1) unsigned NOT NULL COMMENT \'菜单风格\',
                    `type` tinyint(1) unsigned NOT NULL COMMENT \'站点\',
                    `code` char(20) NOT NULL  COMMENT \'别名\',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `code` (`code`),
                    KEY `module` (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'联动菜单表\''));
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'` (`id`, `name`, `style`, `type`, `code`) VALUES(1, \'中国地区\', 0, 0, \'address\')');
                    $this->db->query('DROP TABLE IF EXISTS `'.$this->db->table_name.'_data_1`');
                    $this->db->query(format_create_sql('CREATE TABLE `'.$this->db->table_name.'_data_1` (
                    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    `site` mediumint(5) unsigned NOT NULL COMMENT \'站点id\',
                    `pid` mediumint(8) unsigned NOT NULL DEFAULT \'0\' COMMENT \'上级id\',
                    `pids` varchar(255) DEFAULT NULL COMMENT \'所有上级id\',
                    `name` varchar(30) NOT NULL COMMENT \'栏目名称\',
                    `cname` varchar(30) NOT NULL COMMENT \'别名\',
                    `child` tinyint(1) unsigned DEFAULT NULL DEFAULT \'0\' COMMENT \'是否有下级\',
                    `hidden` tinyint(1) unsigned DEFAULT NULL DEFAULT \'0\' COMMENT \'前端隐藏\',
                    `childids` text DEFAULT NULL COMMENT \'下级所有id\',
                    `displayorder` mediumint(8) DEFAULT NULL DEFAULT \'0\',
                    PRIMARY KEY (`id`),
                    KEY `cname` (`cname`),
                    KEY `hidden` (`hidden`),
                    KEY `list` (`site`,`displayorder`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'联动菜单数据表\''));
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'_data_1` (`id`, `site`, `pid`, `pids`, `name`, `cname`, `child`, `hidden`, `childids`, `displayorder`) VALUES(1, 1, 0, \'0\', \'北京\', \'beijing\', 0, 0, \'1\', 0)');
                    $this->db->query('INSERT INTO `'.$this->db->table_name.'_data_1` (`id`, `site`, `pid`, `pids`, `name`, `cname`, `child`, `hidden`, `childids`, `displayorder`) VALUES(2, 1, 0, \'0\', \'天津\', \'tianjin\', 0, 0, \'2\', 0)');
                }

                $this->db->table_name = $prefix.'model_field';
                $this->db->update(array('setting'=>'{"width":"","fieldtype":"int","format":"1","format2":"0","is_left":"0","defaultvalue":"","color":""}', 'iscore'=>0, 'isbase'=>0),array('formtype'=>'datetime', 'field'=>'updatetime', 'iscore'=>1));
                $fields = $this->db->select();
                foreach ($fields as $r) {
                    if (isset($r['setting']) && $r['setting']) {
                        $this->db->update(array('setting'=>dr_array2string($this->string2array($r['setting']))),array('fieldid'=>$r['fieldid']));
                    }
                }
                $field_datetime = $this->db->select(array('formtype'=>'datetime'));
                foreach ($field_datetime as $r) {
                    if (isset($r['setting']) && (strstr($r['setting'], 'Y-m-d') || strstr($r['setting'], 'H:i:s') || strstr($r['setting'], 'Ah') || strstr($r['setting'], 'm-d') || strstr($r['setting'], 'defaulttype'))) {
                        $field_setting = dr_string2array($r['setting']);
                        if (isset($field_setting)) {
                            if ($field_setting['fieldtype']=='date') {
                                $this->db->update(array('setting'=>'{"width":"","fieldtype":"int","format":"0","format2":"0","is_left":"0","defaultvalue":"","color":""}'),array('fieldid'=>$r['fieldid']));
                            } else {
                                $this->db->update(array('setting'=>'{"width":"","fieldtype":"int","format":"1","format2":"0","is_left":"0","defaultvalue":"","color":""}'),array('fieldid'=>$r['fieldid']));
                            }
                        }
                    }
                }
                $field_editor = $this->db->select(array('formtype'=>'editor'));
                foreach ($field_editor as $r) {
                    if (isset($r['setting']) && (strstr($r['setting'], 'add_introduce') || strstr($r['setting'], 'introcude_length') || strstr($r['setting'], 'auto_thumb') || strstr($r['setting'], 'is_remove_a'))) {
                        $this->db->update(array('tips'=>'', 'setting'=>'{"width":"","height":"","toolbar":"full","toolvalue":"\'Bold\', \'Italic\', \'Underline\'","defaultvalue":"","enablekeylink":"1","replacenum":"2","link_mode":"0","show_bottom_boot":"1","tool_select_1":"1","tool_select_2":"1","tool_select_3":"1","tool_select_4":"1","color":"","theme":"default","autofloat":"0","div2p":"1","autoheight":"0","enter":"0","watermark":"1","attachment":"0","image_reduce":"","allowupload":"0","upload_number":"","upload_maxsize":"","enablesaveimage":"1","local_img":"1","local_watermark":"1","local_attachment":"0","local_image_reduce":"","disabled_page":"0"}'),array('modelid'=>$r['modelid']));
                    }
                }
                $field_keyword = $this->db->select(array('formtype'=>'keyword'));
                foreach ($field_keyword as $r) {
                    if (isset($r['tips']) && strstr($r['tips'], '空格或者')) {
                        $this->db->update(array('tips'=>'多关键词之间用“,”隔开', 'formattribute'=>'data-role=\'tagsinput\''),array('modelid'=>$r['modelid']));
                    }
                }
                $title_field = $this->db->get_one(array('modelid' => -2, 'siteid' => 1, 'field' => 'title', 'name' => '标题'));
                if (!$title_field) {
                    $this->db->insert(array('modelid' => -2, 'siteid' => 1, 'field' => 'title', 'name' => '标题', 'tips' => '', 'css' => 'inputtitle', 'minlength' => 1, 'maxlength' => '80', 'pattern' => '', 'errortips' => '请输入标题', 'formtype' => 'title', 'setting' => '', 'formattribute' => '', 'unsetgroupids' => '', 'unsetroleids' => '', 'iscore' => 0, 'issystem' => 1, 'isunique' => 0, 'isbase' => 1, 'issearch' => 0, 'isadd' => 0, 'isfulltext' => 0, 'isposition' => 0, 'listorder' => 0, 'disabled' => 0, 'isomnipotent' => 0));
                }
                $keywords_field = $this->db->get_one(array('modelid' => -2, 'siteid' => 1, 'field' => 'keywords', 'name' => '关键词'));
                if (!$keywords_field) {
                    $this->db->insert(array('modelid' => -2, 'siteid' => 1, 'field' => 'keywords', 'name' => '关键词', 'tips' => '多关键词之间用“,”隔开', 'css' => '', 'minlength' => 0, 'maxlength' => '40', 'pattern' => '', 'errortips' => '', 'formtype' => 'keyword', 'setting' => '', 'formattribute' => 'data-role=\'tagsinput\'', 'unsetgroupids' => '', 'unsetroleids' => '', 'iscore' => 0, 'issystem' => 1, 'isunique' => 0, 'isbase' => 1, 'issearch' => 0, 'isadd' => 0, 'isfulltext' => 0, 'isposition' => 0, 'listorder' => 0, 'disabled' => 0, 'isomnipotent' => 0));
                }
                $content_field = $this->db->get_one(array('modelid' => -2, 'siteid' => 1, 'field' => 'content', 'name' => '内容'));
                if (!$content_field) {
                    $this->db->insert(array('modelid' => -2, 'siteid' => 1, 'field' => 'content', 'name' => '内容', 'tips' => '', 'css' => '', 'minlength' => 1, 'maxlength' => '999999', 'pattern' => '', 'errortips' => '内容不能为空', 'formtype' => 'editor', 'setting' => '{"width":"","height":"","toolbar":"full","toolvalue":"\'Bold\', \'Italic\', \'Underline\'","defaultvalue":"","enablekeylink":"1","replacenum":"2","link_mode":"0","enablesaveimage":"1","show_bottom_boot":"1","tool_select_1":"0","tool_select_2":"0","tool_select_3":"1","tool_select_4":"1","color":"","theme":"default","autofloat":"0","div2p":"1","autoheight":"0","enter":"0","watermark":"1","attachment":"0","image_reduce":"","allowupload":"0","upload_number":"","upload_maxsize":"","local_img":"1","local_watermark":"1","local_attachment":"0","local_image_reduce":"","disabled_page":"1"}', 'formattribute' => '', 'unsetgroupids' => '', 'unsetroleids' => '', 'iscore' => 0, 'issystem' => 1, 'isunique' => 0, 'isbase' => 1, 'issearch' => 0, 'isadd' => 0, 'isfulltext' => 0, 'isposition' => 0, 'listorder' => 0, 'disabled' => 0, 'isomnipotent' => 0));
                }

                $this->db->table_name = $prefix.'session';
                if (!$this->db->table_exists('session')) {
                    $this->db->query(format_create_sql('CREATE TABLE `'.$this->db->table_name.'` (
                    `id` char(200) NOT NULL COMMENT \'ID\',
                    `ip_address` char(200) NOT NULL COMMENT \'IP\',
                    `timestamp` int(10) unsigned NOT NULL default \'0\' COMMENT \'时间\',
                    `data` blob NOT NULL COMMENT \'Session数据\',
                    PRIMARY KEY (`id`),
                    KEY `timestamp` (`timestamp`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'Session会话表\''));
                } else {
                    if (!$this->db->field_exists('id') || !$this->db->field_exists('ip_address') || !$this->db->field_exists('timestamp')) {
                        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->table_name.'`');
                        $this->db->query(format_create_sql('CREATE TABLE `'.$this->db->table_name.'` (
                        `id` char(200) NOT NULL COMMENT \'ID\',
                        `ip_address` char(200) NOT NULL COMMENT \'IP\',
                        `timestamp` int(10) unsigned NOT NULL default \'0\' COMMENT \'时间\',
                        `data` blob NOT NULL COMMENT \'Session数据\',
                        PRIMARY KEY (`id`),
                        KEY `timestamp` (`timestamp`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT=\'Session会话表\''));
                    }
                }

                $this->db->table_name = $prefix.'slider';
                if ($this->db->table_exists('slider')) {
                    if (!$this->db->field_exists('pic')) {
                        $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `pic` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'手机图片\' AFTER `image`');
                    }
                    if (!$this->db->field_exists('icon')) {
                        $this->db->query('ALTER TABLE `'.$this->db->table_name.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `pic`');
                    }
                }

                break;

            case '08':

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

            case '09':

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
                if ($rt) {
                    $this->halt(implode('<br>', $rt), 0);
                }

                dr_json(1,'正常');

                break;

            case '10':

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
                            $tips[] = '当前站点【'.$t['siteid'].'】没有绑定手机域名';
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

                if ($error) {
                    dr_json(0, implode('<br>', $error));
                } elseif ($tips) {
                    dr_json(1, implode('<br>', $tips));
                } else {
                    dr_json(1, '完成');
                }

                break;

            case '11':

                $version = CONFIGPATH.'version.php';
                if (is_file($version)) {
                    $app_version = file_get_contents($version);
                }
                $app = pc_base::load_config('version');
                $version_data = '<?php'.PHP_EOL.'if (!defined(\'IN_CMS\')) exit(\'No direct script access allowed\');'.PHP_EOL;
                $version_data .= 'return array('.PHP_EOL;
                $version_data .= '\'pc_version\' => \''.($app['pc_version'] ? $app['pc_version'] : 'V9.6.3').'\', //版本号
\'pc_release\' => \''.($app['pc_release'] ? $app['pc_release'] : '20170515').'\', //更新日期
\'cms_version\' => \''.($app['cms_version'] ? $app['cms_version'] : 'V10.0.0').'\', //cms 版本号
\'cms_release\' => \''.($app['cms_release'] ? $app['cms_release'] : dr_date(SYS_TIME, 'Ymd')).'\', //cms 更新日期
\'cms_updatetime\' => \''.($app['cms_updatetime'] ? $app['cms_updatetime'] : dr_date(SYS_TIME, 'Y-m-d')).'\', // 服务端最近更新时间
\'cms_downtime\' => \''.($app['cms_downtime'] ? $app['cms_downtime'] : dr_date(SYS_TIME, 'Y-m-d H:i:s')).'\', // 本网站程序下载时间
\'update\' => \'0\', //cms 更新';
                $version_data.= PHP_EOL.');'.PHP_EOL.'?>';
                if ($app['update'] || !strstr($app_version, 'IN_CMS') || !strstr($app_version, 'cms_version') || !strstr($app_version, 'cms_release') || !strstr($app_version, 'cms_updatetime') || !strstr($app_version, 'cms_downtime') || !strstr($app_version, 'update')) {
                    file_put_contents($version,$version_data);
                }

                $rt = CONFIGPATH.'system.php';
                $system = file_get_contents($rt);
                $system_data = '<?php'.PHP_EOL.'if (!defined(\'IN_CMS\')) exit(\'No direct script access allowed\');'.PHP_EOL;
                $system_data .= 'return array('.PHP_EOL;
                $system_data .= '//网站路径
\'web_path\' => \''.(pc_base::load_config('system','web_path') ? pc_base::load_config('system','web_path') : '/').'\',
//Session配置
\'session_storage\' => \''.(pc_base::load_config('system','session_storage')=='mysqli' ? 'mysqli' : 'file').'\',
\'session_ttl\' => '.((int)pc_base::load_config('system','session_ttl') ? (int)pc_base::load_config('system','session_ttl') : 1800).',
\'session_savepath\' => '.str_replace(CACHE_PATH, 'CACHE_PATH.\'' ,pc_base::load_config('system','session_savepath')).'\',
//Cookie配置
\'cookie_domain\' => \''.pc_base::load_config('system','cookie_domain').'\', //Cookie 作用域
\'cookie_path\' => \''.pc_base::load_config('system','cookie_path').'\', //Cookie 作用路径
\'cookie_pre\' => \''.pc_base::load_config('system','cookie_pre').'\', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
//模板相关配置
\'tpl_root\' => \''.pc_base::load_config('system','tpl_root').'\', //模板保存物理路径
\'tpl_name\' => \''.pc_base::load_config('system','tpl_name').'\', //当前模板方案目录
\'tpl_css\' => \''.pc_base::load_config('system','tpl_css').'\', //当前样式目录
\'tpl_referesh\' => '.(int)pc_base::load_config('system','tpl_referesh').',
\'tpl_edit\' => '.(int)pc_base::load_config('system','tpl_edit').', //是否允许在线编辑模板

//附件相关配置
\'attachment_stat\' => \''.(int)pc_base::load_config('system','attachment_stat').'\', //是否记录附件使用状态 0 统计 1 统计， 注意: 本功能会加重服务器负担
\'attachment_file\' => \''.(int)pc_base::load_config('system','attachment_file').'\', //附件是否使用分站 0 否 1 是
\'attachment_del\' => \''.(int)pc_base::load_config('system','attachment_del').'\', //是否同步删除附件 0 否 1 是
\'sys_attachment_save_id\' => '.(int)pc_base::load_config('system','sys_attachment_save_id').', //附件存储策略
\'sys_attachment_cf\' => '.(int)pc_base::load_config('system','sys_attachment_cf').', //重复上传控制
\'sys_attachment_pagesize\' => '.((int)pc_base::load_config('system','sys_attachment_pagesize') ? (int)pc_base::load_config('system','sys_attachment_pagesize') : 18).', //浏览附件分页
\'sys_attachment_safe\' => '.(int)pc_base::load_config('system','sys_attachment_safe').', //附件上传安全模式
\'sys_attachment_path\' => \''.(pc_base::load_config('system','sys_attachment_path') ? pc_base::load_config('system','sys_attachment_path') : '').'\', //附件上传路径
\'sys_attachment_save_type\' => '.(int)pc_base::load_config('system','sys_attachment_save_type').', //附件存储方式
\'sys_attachment_save_dir\' => \''.(pc_base::load_config('system','sys_attachment_save_dir') ? pc_base::load_config('system','sys_attachment_save_dir') : '').'\', //附件存储目录
\'sys_attachment_url\' => \''.(pc_base::load_config('system','sys_attachment_url') ? pc_base::load_config('system','sys_attachment_url') : '').'\', //附件访问地址
\'sys_avatar_path\' => \''.(pc_base::load_config('system','sys_avatar_path') ? pc_base::load_config('system','sys_avatar_path') : '').'\', //头像上传路径
\'sys_avatar_url\' => \''.(pc_base::load_config('system','sys_avatar_url') ? pc_base::load_config('system','sys_avatar_url') : '').'\', //头像访问地址
\'sys_thumb_path\' => \''.(pc_base::load_config('system','sys_thumb_path') ? pc_base::load_config('system','sys_thumb_path') : '').'\', //缩略图存储目录
\'sys_thumb_url\' => \''.(pc_base::load_config('system','sys_thumb_url') ? pc_base::load_config('system','sys_thumb_url') : '').'\', //缩略图访问地址

\'site_theme\' => \''.(int)pc_base::load_config('system','site_theme').'\', //风格模式    0 本站资源 1 远程地址
\'js_path\' => \''.pc_base::load_config('system','js_path').'\', //CDN JS
\'css_path\' => \''.pc_base::load_config('system','css_path').'\', //CDN CSS
\'img_path\' => \''.pc_base::load_config('system','img_path').'\', //CDN img
\'mobile_js_path\' => \''.(pc_base::load_config('system','mobile_js_path') ? pc_base::load_config('system','mobile_js_path') : pc_base::load_config('system','app_path').'mobile/statics/js/').'\', //CDN JS
\'mobile_css_path\' => \''.(pc_base::load_config('system','mobile_css_path') ? pc_base::load_config('system','mobile_css_path') : pc_base::load_config('system','app_path').'mobile/statics/css/').'\', //CDN CSS
\'mobile_img_path\' => \''.(pc_base::load_config('system','mobile_img_path') ? pc_base::load_config('system','mobile_img_path') : pc_base::load_config('system','app_path').'mobile/statics/images/').'\', //CDN img
\'app_path\' => \''.pc_base::load_config('system','app_path').'\', //动态域名配置地址
\'mobile_path\' => \''.(pc_base::load_config('system','mobile_path') ? pc_base::load_config('system','mobile_path') : pc_base::load_config('system','app_path').'mobile/').'\', //动态手机域名配置地址
\'bdmap_api\' => \''.pc_base::load_config('system','bdmap_api').'\', //百度地图API
\'sys_editor\' => \''.(int)pc_base::load_config('system','sys_editor').'\', //编辑器模式    0 UEditor 1 CKEditor
\'sys_admin_pagesize\' => \''.((int)pc_base::load_config('system','sys_admin_pagesize') ? (int)pc_base::load_config('system','sys_admin_pagesize') : 10).'\', //后台数据分页显示数量

\'charset\' => \''.(pc_base::load_config('system','charset') ? pc_base::load_config('system','charset') : 'utf-8').'\', //网站字符集
\'timezone\' => \''.(pc_base::load_config('system','timezone')=='Etc/GMT-8' ? 8 : (int)pc_base::load_config('system','timezone')).'\', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
\'sys_time_format\' => \''.pc_base::load_config('system','sys_time_format').'\', //网站时间显示格式与date函数一致，默认Y-m-d H:i:s
\'debug\' => '.(int)pc_base::load_config('system','debug').', //是否显示调试信息
\'sys_go_404\' => \''.(int)pc_base::load_config('system','sys_go_404').'\', //404页面跳转开关
\'sys_301\' => \''.(int)pc_base::load_config('system','sys_301').'\', //内容地址唯一模式
\'sys_url_only\' => \''.(int)pc_base::load_config('system','sys_url_only').'\', //地址匹配规则
\'token_name\' => \''.(pc_base::load_config('system','token_name') ? pc_base::load_config('system','token_name') : 'csrf_test_name').'\', //CSRF令牌名称
\'sys_csrf\' => \''.(int)pc_base::load_config('system','sys_csrf').'\', //开启跨站验证
\'sys_csrf_time\' => \''.(int)pc_base::load_config('system','sys_csrf_time').'\', //CSRF验证有效期
\'needcheckcomeurl\' => \''.(int)pc_base::load_config('system','needcheckcomeurl').'\', //是否需要检查外部访问，1为启用，0为禁用
\'admin_log\' => '.(int)pc_base::load_config('system','admin_log').', //是否记录后台操作日志
\'gzip\' => '.(int)pc_base::load_config('system','gzip').', //是否Gzip压缩后输出
\'auth_key\' => \''.pc_base::load_config('system','auth_key').'\', //安全密钥
\'lang\' => \''.pc_base::load_config('system','lang').'\', //网站语言包

\'admin_founders\' => \''.pc_base::load_config('system','admin_founders').'\', //网站创始人ID，多个ID逗号分隔

\'html_root\' => \''.pc_base::load_config('system','html_root').'\', //生成静态文件路径
\'mobile_root\' => \''.(pc_base::load_config('system','mobile_root') ? pc_base::load_config('system','mobile_root') : '/mobile').'\', //生成手机静态文件路径

\'connect_enable\' => \''.pc_base::load_config('system','connect_enable').'\', //是否开启外部通行证
\'sina_akey\' => \''.pc_base::load_config('system','sina_akey').'\', //sina AKEY
\'sina_skey\' => \''.pc_base::load_config('system','sina_skey').'\', //sina SKEY

\'qq_appkey\' => \''.pc_base::load_config('system','qq_appkey').'\', //QQ号码登录 appkey
\'qq_appid\' => \''.pc_base::load_config('system','qq_appid').'\', //QQ号码登录 appid
\'qq_callback\' => \''.pc_base::load_config('system','qq_callback').'\', //QQ号码登录 callback

\'keywordapi\' => \''.(int)pc_base::load_config('system','keywordapi').'\', //关键词提取    0 本地 1 百度 2 讯飞
\'baidu_aid\' => \''.pc_base::load_config('system','baidu_aid').'\', //百度关键词提取 APPID
\'baidu_skey\' => \''.pc_base::load_config('system','baidu_skey').'\', //百度关键词提取 APIKey
\'baidu_arcretkey\' => \''.pc_base::load_config('system','baidu_arcretkey').'\', //百度关键词提取 Secret Key
\'baidu_qcnum\' => \''.(int)pc_base::load_config('system','baidu_qcnum').'\', //分词数量
\'xunfei_aid\' => \''.pc_base::load_config('system','xunfei_aid').'\', //讯飞关键词提取 APPID
\'xunfei_skey\' => \''.pc_base::load_config('system','xunfei_skey').'\', //讯飞关键词提取 APIKey

\'admin_login_path\' => \''.pc_base::load_config('system','admin_login_path').'\', //自定义的后台登录地址';
                $system_data.= PHP_EOL.');'.PHP_EOL.'?>';
                if (!strstr($system, 'IN_CMS') || strstr($system, 'PHPCMS_PATH') || strstr($system, 'session_n') || strstr($system, 'cookie_ttl') || strstr($system, 'execution_sql') || strstr($system, 'admin_url') || strstr($system, 'safe_card') || strstr($system, 'phpsso') || strstr($system, 'phpsso_appid') || strstr($system, 'phpsso_api_url') || strstr($system, 'phpsso_auth_key') || strstr($system, 'phpsso_version') || strstr($system, '\'timezone\' => \'Etc/GMT-8\'') || strstr($system, 'lock_ex') || strstr($system, 'snda_akey') || strstr($system, 'snda_skey') || strstr($system, 'qq_akey') || strstr($system, 'qq_skey') || strstr($system, 'errorlog') || !strstr($system, 'sys_time_format') || !strstr($system, 'attachment_file') || !strstr($system, 'attachment_del') || !strstr($system, 'sys_attachment_save_id') || !strstr($system, 'sys_attachment_cf') || !strstr($system, 'sys_attachment_pagesize') || !strstr($system, 'sys_attachment_safe') || !strstr($system, 'sys_attachment_path') || !strstr($system, 'sys_attachment_save_type') || !strstr($system, 'sys_attachment_save_dir') || !strstr($system, 'sys_attachment_url') || !strstr($system, 'sys_avatar_path') || !strstr($system, 'sys_avatar_url') || !strstr($system, 'sys_thumb_path') || !strstr($system, 'sys_thumb_url') || !strstr($system, 'site_theme') || !strstr($system, 'mobile_js_path') || !strstr($system, 'mobile_css_path') || !strstr($system, 'mobile_img_path') || !strstr($system, 'mobile_path') || !strstr($system, 'bdmap_api') || !strstr($system, 'sys_editor') || !strstr($system, 'sys_admin_pagesize') || !strstr($system, 'sys_go_404') || !strstr($system, 'sys_301') || !strstr($system, 'sys_url_only') || !strstr($system, 'token_name') || !strstr($system, 'sys_csrf') || !strstr($system, 'sys_csrf_time') || !strstr($system, 'needcheckcomeurl') || !strstr($system, 'mobile_root') || !strstr($system, 'keywordapi') || !strstr($system, 'baidu_aid') || !strstr($system, 'baidu_skey') || !strstr($system, 'baidu_arcretkey') || !strstr($system, 'baidu_qcnum') || !strstr($system, 'xunfei_aid') || !strstr($system, 'xunfei_skey') || !strstr($system, 'admin_login_path')) {
                    file_put_contents($rt,$system_data);
                }

                if (pc_base::load_config('system','admin_login_path')) {
                    if (!is_dir(CMS_PATH.pc_base::load_config('system','admin_login_path'))) {
                        dr_mkdirs(CMS_PATH.pc_base::load_config('system','admin_login_path'));
                        $admin = file_get_contents(TEMPPATH.'web/admin.php');
                        $admin = str_replace('index.php','../index.php',$admin);
                        file_put_contents(CMS_PATH.pc_base::load_config('system','admin_login_path').'/index.php',$admin);
                    }
                    $index = file_get_contents(PC_PATH.'modules/admin/index.php');
                    if (!strstr($index, 'public function '.pc_base::load_config('system','admin_login_path').'()')) {
                        $admin_index = file_get_contents(TEMPPATH.'admin/index.php');
                        $admin_index = str_replace('public function login()','public function '.pc_base::load_config('system','admin_login_path').'()',$admin_index);
                        file_put_contents(PC_PATH.'modules/admin/index.php',$admin_index);
                    }
                }

                if (pc_base::load_config('system','tpl_edit')) {
                    dr_json(0, '系统开启了在线编辑模板权限，建议关闭此权限');
                }

                dr_json(1,'完成');

                break;

            case '12':
                // 服务器环境
                if (is_file(CMS_PATH.'test.php')) {
                    $error[] = '当网站正式上线后，根目录的test.php建议删除';
                }
                if (IS_DEV) {
                    $error[] = '当网站正式上线后，根目录的index.php中的开发者默认参数，建议关闭';
                }

                if ($error) {
                    dr_json(0, implode('<br>', $error));
                }

                dr_json(1, '完成');

                break;

            case '13':
                // 应用插件
                $func = array();
                $local = pc_base::load_sys_class('service')::apps();
                $extention = file_get_contents(PC_PATH.'libs/functions/extention.func.php');
                foreach ($local as $dir => $path) {
                    if (is_file($path.'install/config.inc.php')) {
                        // 变量重定义
                        if (is_file($path.'functions/global.func.php')) {
                            $code = file_get_contents($path.'functions/global.func.php');
                            if (preg_match_all("/\s+function (.+)\(/", $code, $arr)) {
                                foreach ($arr[1] as $a) {
                                    $name = trim($a);
                                    if (strpos($name, "'") !== false) {
                                        continue;
                                    }
                                    if (isset($func[$name]) && $func[$name]) {
                                        dr_json(0,'模块['.$dir.']中的函数['.$name.']存在于'.$func[$name].'之中，不能被重复定义');
                                    }
                                    $func[$name] = $dir;
                                    if (function_exists($name)) {
                                        if (preg_match("/\s+function ".$name."\(/", $extention)) {
                                            // 存在于自定义函数库中
                                        } else {
                                            dr_json(0,'模块['.$dir.']中的函数['.$name.']是系统函数，不能定义');
                                        }
                                    }
                                }
                            }
                        }
                    }
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
        if ($counts > 1000000) {
            return '<font color="green">数据表【'.$name.'/'.$this->db->db_tablepre.$table.'】数据量超过100万，会影响加载速度，建议对其进行数据优化</font>';
        }
    }

    private function _alter_table($table) {
        $this->content_db = pc_base::load_model('content_model');
        $this->content_db->table_name = $this->content_db->db_tablepre.$table;
        if (!$this->content_db->field_exists('tableid')) {
            $this->content_db->query('ALTER TABLE `'.$this->content_db->table_name.'` ADD `tableid` smallint(5) UNSIGNED NOT NULL COMMENT \'附表id\' AFTER `sysadd`');
        }
        $this->content_db->table_name = $this->content_db->db_tablepre.$table.'_data';
        if ($this->content_db->table_exists($table.'_data')) {
            $this->content_db->query('ALTER TABLE `'.$this->content_db->table_name.'` RENAME `'.$this->content_db->table_name.'_0`');
        }
    }

    private function _table_counts($table) {
        $this->content_db = pc_base::load_model('content_model');
        $this->content_db->table_name = $this->content_db->db_tablepre.$table;
        if (!$this->content_db->table_exists($table)) {
            return 0;
        }
        $counts = $this->content_db->count();
        return isset($counts) && $counts ? $counts : 0;
    }

    private function string2array($data) {
        $data = trim($data);
        if($data == '') return array();
        if(strpos($data, 'array')===0){
            @eval("\$array = $data;");
        }else{
            if(strpos($data, '{\\')===0) $data = stripslashes($data);
            $array=dr_string2array($data);
            if(strtolower(CHARSET)=='gbk'){
                $array = mult_iconv("UTF-8", "GBK//IGNORE", $array);
            }
        }
        return $array;
    }

}
?>