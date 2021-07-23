<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class check_bom extends admin {
    private $phpfile = array();

    public function __construct() {
        parent::__construct();
		$this->input = pc_base::load_sys_class('input');
    }

    public function init() {
        include $this->admin_tpl('check_bom_index');
    }

    // php文件个数
    public function public_php_count() {

        // 读取文件到缓存
        $this->_file_map(CMS_PATH, 1);
        $this->_file_map(CACHE_PATH);
        $this->_file_map(PC_PATH);

        $cache = array();
        $count = $this->phpfile ? count($this->phpfile) : 0;
        if ($count > 100) {
            $pagesize = ceil($count/100);
            for ($i = 1; $i <= 100; $i ++) {
                $cache[$i] = array_slice($this->phpfile, ($i - 1) * $pagesize, $pagesize);
            }
        } else {
            for ($i = 1; $i <= $count; $i ++) {
                $cache[$i] = array_slice($this->phpfile, ($i - 1), 1);
            }
        }

        // 存储文件
        setcache('check-index', $cache, 'commons');

        dr_json($cache ? count($cache) : 0, 'ok');
    }

    public function public_php_check() {

        $page = max(1, intval($this->input->get('page')));
        $cache = getcache('check-index', 'commons');
        if (!$cache) {
            dr_json(0, '数据缓存不存在');
        }

        $data = $cache[$page];
        if ($data) {
            $html = '';
            foreach ($data as $filename) {
                // 避免自杀
                if (strpos($filename, 'cms/modules/admin/check_bom.php') !== false) {
                    continue;
                } elseif (strpos($filename, 'cms\modules\admin\check_bom.php') !== false) {
                    continue;
                }
                $contents = file_get_contents ( $filename );
                $charset [1] = substr ( $contents, 0, 1 );
                $charset [2] = substr ( $contents, 1, 1 );
                $charset [3] = substr ( $contents, 2, 1 );
                $class = '';
                if (ord ( $charset [1] ) == 239 && ord ( $charset [2] ) == 187 && ord ( $charset [3] ) == 191) {
                    // BOM 的前三个字符的ASCII 码分别为 239 187 191
                    $ok = "<span class='error'>BOM异常</span>";
                    $class = ' p_error';
                } elseif (strpos($contents, '$_POST[')) {
                    if (strpos($contents, '=$_POST[') || strpos($contents, '= $_POST[')) {
                        $ok = "<span class='error'>POST不安全</span>";
                        $class = ' p_error';
                    } else {
                        $ok = "<span class='ok'>正常</span>";
                    }
                } elseif (strpos($contents, '$_GET[')) {
                    if (strpos($contents, '=$_GET[') || strpos($contents, '= $_GET[')) {
                        $ok = "<span class='error'>GET不安全</span>";
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
        delcache('check-index', 'commons');
        dr_json(100, '');
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