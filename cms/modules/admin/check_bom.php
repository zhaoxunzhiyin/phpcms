<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class check_bom extends admin {
    private $input,$cache;
    private $phpfile = array();

    public function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
        $this->cache = pc_base::load_sys_class('cache');
    }

    public function init() {
        $show_header = true;
        include $this->admin_tpl('check_bom_index');
    }

    // php文件个数
    public function public_php_count() {

        // 读取文件到缓存
        $this->_file_map(CMS_PATH, 1);
        $this->_file_map(CACHE_PATH);
        $this->_file_map(PC_PATH);

        $cache = dr_save_bfb_data($this->phpfile);

        // 存储文件
        $this->cache->set_data('check-index', $cache, 3600);

        dr_json($cache ? count($cache) : 0, 'ok');
    }

    public function public_php_check() {

        $page = max(1, intval($this->input->get('page')));
        $cache = $this->cache->get_data('check-index');
        if (!$cache) {
            dr_json(0, '数据缓存不存在');
        }

        $data = $cache[$page];
        if ($data) {
            $html = '';
            foreach ($data as $filename) {
                // 避免自杀
                if (in_array(basename($filename), [
                    'check_bom.php',
                    'error_exception.php'
                ])) {
                    continue;
                }
                $contents = file_get_contents ( $filename );
                $class = '';
                if ($this->_is_bom($contents)) {
                    $ok = "<span class='error'>存在Bom字符</span>";
                    $class = ' p_error';
                } elseif ($this->_is_muma($contents)) {
                    $ok = "<span class='error'>可能存在问题</span>";
                    $class = ' p_error';
                } elseif (strpos($contents, '$_POST[')) {
                    if (strpos($contents, '=$_POST[') || strpos($contents, '= $_POST[')) {
                        $ok = "<span class='error'>POST可能不安全</span>";
                        $class = ' p_error';
                    } else {
                        $ok = "<span class='ok'>正常</span>";
                    }
                } elseif (strpos($contents, '$_GET[')) {
                    if (strpos($contents, '=$_GET[') || strpos($contents, '= $_GET[')) {
                        $ok = "<span class='error'>GET可能不安全</span>";
                        $class = ' p_error';
                    } else {
                        $ok = "<span class='ok'>正常</span>";
                    }
                } else {
                    $ok = "<span class='ok'>正常</span>";
                }
                $html.= '<p class="'.$class.'"><label class="rleft">'.dr_safe_replace_path($filename).'</label><label class="rright">'.$ok.'</label></p>';
                if ($class) {
                    $html.= '<p class="rbf" style="display: none"><label class="rleft">'.$filename.'</label><label class="rright">'.$ok.'</label></p>';
                }
            }
            dr_json($page + 1, $html);
        }

        // 完成
        $this->cache->clear('check-index');
        dr_json(100, '');
    }

    private function _is_muma($contents) {
        if (!$contents) {
            return 0;
        }
        $keys = [
            'eval($_POST',
            'eval($_GET',
            'eval($_REQUEST',
            'set_time_limit(0);header(',
            'function papa($h)',
            'xysword',
            'IsSpider',
        ];
        foreach ($keys as $t) {
            if (stripos($contents, $t) !== false) {
                return $t;
            }
        }
        return 0;
    }

    private function _is_bom($contents) {
        $charset [1] = substr ( $contents, 0, 1 );
        $charset [2] = substr ( $contents, 1, 1 );
        $charset [3] = substr ( $contents, 2, 1 );
        if (ord ( $charset [1] ) == 239 && ord ( $charset [2] ) == 187 && ord ( $charset [3] ) == 191) {
            return 1;
        }
        return 0;
    }

    private function _file_map($source_dir, $exit = 0) {
        if ($fp = @opendir($source_dir)) {
            $source_dir    = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            while (false !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' || $file === '..') {
                    continue;
                }
                is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;
                if (is_dir($source_dir.$file) && !$exit) {
                    $this->_file_map($source_dir.$file, $exit);
                } else {
                    trim(strtolower(strrchr($file, '.')), '.') == 'php' && $this->phpfile[] = $source_dir.$file;
                }
            }
            closedir($fp);
        }
    }
}
?>