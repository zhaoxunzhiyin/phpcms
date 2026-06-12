<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class debugs extends admin {
    private $input,$db;
    public function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->db = pc_base::load_model('admin_model');
    }

    public function init() {
        define('INSTALL', TRUE);

        $this->_echo_msg(IS_DEV ? 1 : 0, '开发者模式：'.(IS_DEV ? '已开启' : '未开启'));
        $this->_echo_msg(1, '客户端字符串：'.$_SERVER['HTTP_USER_AGENT']);
        $this->_echo_msg(1, 'PHP版本：'.PHP_VERSION.'');
        $this->_echo_msg(1, 'MySQL版本：'.$this->db->version());

        $this->_echo_msg(1, 'CMS版本：'.pc_base::load_config('version','cms_version').' - '.dr_date(pc_base::load_config('version','cms_downtime'), 'Y-m-d H:i:s'));
        $local = pc_base::load_sys_class('service')::apps();
        foreach ($local as $dir => $path) {
            if (is_file($path.'/install/config.inc.php')) {
                require $path.'/install/config.inc.php';
                $this->_echo_msg(1, $modulename.' - 版本：'.$version);
            }
        }
    }


    function _echo_msg($code, $msg) {
        echo '<div style="border-bottom: 1px dashed #9699a2; padding: 5px;">';
        if (!$code) {
            echo '<b style="color:red;text-decoration:none;">'.$msg.'</b>';
        } else {
            echo '<font color=green>'.$msg.'</font>';
        }
        echo '</div>';
    }
}
