<?php
/**
 * 附件类
 */
class upload {

    protected $module;
    protected $catid;
    protected $siteid;
    protected $error;
    protected $notallowed;
    protected $down_file_ext;
    public $userid;
    public $uploadedfiles;

    /**
     * 构造函数
     */
    public function __construct($module='', $catid = 0,$siteid = 0) {
        $this->catid = intval($catid);
        $this->siteid = intval($siteid)== 0 ? 1 : intval($siteid);
        $this->module = $module ? $module : 'content';
        // 返回错误信息
        $this->error = array(
            "SUCCESS",
            L("文件大小超出 upload_max_filesize 限制"),
            L("文件大小超出 MAX_FILE_SIZE 限制"),
            L("文件未被完整上传"),
            L("没有文件被上传"),
            L("上传文件为空"),
            "ERROR_TMP_FILE" => L("临时文件错误"),
            "ERROR_TMP_FILE_NOT_FOUND" => L("找不到临时文件"),
            "ERROR_SIZE_EXCEED" => L("文件大小超出网站限制"),
            "ERROR_TYPE_NOT_ALLOWED" => L("文件类型不允许"),
            "ERROR_SYSTEM_TYPE_NOT_ALLOWED" => L("文件类型被系统禁止上传"),
            "ERROR_CREATE_DIR" => L("目录创建失败"),
            "ERROR_DIR_NOT_WRITEABLE" => L("目录没有写权限"),
            "ERROR_FILE_MOVE" => L("文件保存时出错"),
            "ERROR_FILE_NOT_FOUND" => L("找不到上传文件"),
            "ERROR_WRITE_CONTENT" => L("写入文件内容错误"),
            "ERROR_UNKNOWN" => L("未知错误"),
            "ERROR_DEAD_LINK" => L("链接不可用"),
            "ERROR_HTTP_LINK" => L("链接不是http链接"),
            "ERROR_ATTACH_TYPE" => L("未知的存储类型"),
            "ERROR_HTTP_CONTENTTYPE" => L("链接contentType不正确")
        );
        // 禁止以下文件上传
        $this->notallowed = array('php', 'php3', 'php4', 'php5', 'asp', 'jsp', 'jspx', 'aspx', 'exe', 'sh', 'phtml', 'dll', 'cer', 'asa', 'shtml', 'shtm', 'asax', 'cgi', 'fcgi', 'pl');
        // 下载文件扩展名白名单
        $this->down_file_ext = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'zip', 'rar');
        // 自定义白名单文件
        if (is_file(CONFIGPATH.'fileext.php')) {
            require CONFIGPATH.'fileext.php';
        }
    }

    // 安全验证
    public function _safe_check($file_ext, $data, $is_ext = 1) {

        // 检查系统保留文件格式
        if ($is_ext) {
            if (in_array($file_ext, $this->notallowed)) {
                return dr_return_data(0, $this->error['ERROR_SYSTEM_TYPE_NOT_ALLOWED']);
            } elseif (!$file_ext) {
                return dr_return_data(0, L('无法读取文件扩展名'));
            }
        }

        // 验证扩展名格式
        if (!preg_match('/^[a-z0-9]+$/i', $file_ext)) {
            return dr_return_data(0, L('此文件扩展名['.$file_ext.']不安全，禁止上传'));
        }

        // 是否进行严格验证
        if (defined('SYS_ATTACHMENT_SAFE') && SYS_ATTACHMENT_SAFE) {
            return dr_return_data(1, 'ok');
        }

        // 验证伪装图片
        if (in_array($file_ext, array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'))) {
            $data = strlen($data) < 50 && @is_file($data) ? file_get_contents($data) : strtolower($data);
            if (strlen($data) < 50) {
                return dr_return_data(0, L('图片文件不规范').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            } elseif (strpos($data, '<?php') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            } elseif (strpos($data, 'eval(') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            } elseif (strpos($data, '.php') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            } elseif (strpos($data, 'base64_decode(') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            } elseif (strpos($data, '<script') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传').(IS_DEV ? '后台-设置-附件设置-可选择附件验证为宽松模式' : ''));
            }
        }

        return dr_return_data(1, 'ok');
    }

    /**
     * 上传文件
     */
    public function upload_file($config) {

        $file = isset($_FILES[$config['form_name']]) ? $_FILES[$config['form_name']] : null;

        if (!$file) {
            return dr_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (isset($file['error']) && $file['error']) {
            return dr_return_data(0, $this->_error_msg($file['error']));
        } else if (!file_exists($file['tmp_name'])) {
            return dr_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (!is_uploaded_file($file['tmp_name'])) {
            return dr_return_data(0, $this->error['ERROR_TMPFILE']);
        }

        $file_ext = $this->_file_ext($file['name']); // 扩展名
        $file_name = $this->_file_name($file['name']); // 文件实际名字

        // 安全验证
        $rt = $this->_safe_check($file_ext, $file["tmp_name"]);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        if (!$config['file_size']) {
            return dr_return_data(0, L('系统没有设置可上传的文件大小'));
        } elseif ($file['size'] > $config['file_size']) {
            // 文件大小限制
            return dr_return_data(0, $this->error['ERROR_SIZE_EXCEED']. ' '.($config['file_size']/1024/1024).'MB');
        } elseif ($config['file_exts'][0] != '*' && !in_array($file_ext, $config['file_exts'])) {
            // 检查是文件格式
            return dr_return_data(0, $this->error['ERROR_TYPE_NOT_ALLOWED'] . $file_ext);
        }

        // 保存目录名称
        list($file_path, $config, $diy) = $this->_rand_save_file_path($config, $file_ext, $file);

        // 开始上传存储文件
        $rt = $this->save_file('upload', $file["tmp_name"], $file_path, $config['attachment'], (int)$config['watermark']);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        // 上传成功
        if ($diy) {
            $url = '自定义存储地址不提供URL';
        } else {
            $url = $config['attachment']['url'].$file_path;
        }

        // 文件预览
        $preview = dr_file_preview_html($url);

        return dr_return_data(1, 'ok', array(
            'ext' => $file_ext,
            'url' => $url,
            'md5' => $rt['data']['md5'],
            'file' => $file_path,
            'size' => isset($rt['data']['size']) && $rt['data']['size'] ? $rt['data']['size'] : $file['size'],
            'path' => ($config['attachment']['value']['path'] && $config['attachment']['value']['path'] != 'null' ? $config['attachment']['value']['path'] : '').$file_path,
            'name' => $file_name,
            'info' => $rt['data']['info'],
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ));
    }

    /**
     * 更新文件
     */
    public function update_file($config) {

        $file = isset($_FILES[$config['form_name']]) ? $_FILES[$config['form_name']] : null;

        if (!$file) {
            return dr_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (!file_exists($file['tmp_name'])) {
            return dr_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (!is_uploaded_file($file['tmp_name'])) {
            return dr_return_data(0, $this->error['ERROR_TMPFILE']);
        } else if (isset($file['error']) && $file['error']) {
            return dr_return_data(0, $this->_error_msg($file['error']));
        }

        $file_ext = $this->_file_ext($file['name']); // 扩展名
        // 安全验证
        $rt = $this->_safe_check($file_ext, $file["tmp_name"]);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        // 检查是文件格式
        if ($config['file_exts'][0] != '*' && !in_array($file_ext, $config['file_exts'])) {
            return dr_return_data(0, $this->error['ERROR_TYPE_NOT_ALLOWED'] . $file_ext);
        }

        if (!(move_uploaded_file($file["tmp_name"], $config['file_name']) || !is_file($config['file_name']))) {
            return dr_return_data(0, $this->error['ERROR_FILE_MOVE']);
        }

        return dr_return_data(1, 'ok');
    }

    /**
     * 下载文件
     */
    public function down_file($config) {

        if (isset($config['file_content']) && $config['file_content']) {
            // 表示已经下载好了的文件
            $data = $config['file_content'];
        } else {
            $data = dr_catcher_data($config['url'], (int)$config['timeout']);
            if (!$data) {
                log_message('error', '服务器无法下载文件：'.$config['url']);
                return dr_return_data(0, L('文件下载失败'));
            }
        }

        $file_ext = isset($config['file_ext']) && $config['file_ext'] ? $config['file_ext'] : $this->_file_ext($config['url']); // 扩展名
        if (!in_array($file_ext, $this->down_file_ext)) {
            return dr_return_data(0, L('此扩展名被禁止下载'));
        }

        // 安全验证
        $rt = $this->_safe_check($file_ext, $data);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        $file_name = $this->_file_name($config['url']); // 文件实际名字
        if (!$file_ext) {
            log_message('error', '无法获取文件扩展名：'.$config['url']);
            return dr_return_data(0, L('无法获取文件扩展名'));
        }

        // 保存目录名称
        list($file_path, $config, $diy) = $this->_rand_save_file_path($config, $file_ext, $data);

        $file_name = $file_name ? $file_name : $this->_file_name($file_path); // 文件实际名字

        // 开始上传存储文件
        $rt = $this->save_file('content', $data, $file_path, $config['attachment'], (int)$config['watermark']);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        // 上传成功
        if ($diy) {
            $url = '自定义存储地址不提供URL';
        } else {
            $url = $config['attachment']['url'].$file_path;
        }

        // 文件预览
        $preview = dr_file_preview_html($url);

        return dr_return_data(1, 'ok', array(
            'ext' => $file_ext,
            'url' => $url,
            'md5' => md5($data),
            'file' => $file_path,
            'size' => (int)$rt['data']['size'],
            'path' => ($config['attachment']['value']['path'] && $config['attachment']['value']['path'] != 'null' ? $config['attachment']['value']['path'] : '').$file_path,
            'name' => $file_name,
            'info' => $rt['data']['info'],
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ));
    }

    // base64模式
    public function base64_image($config) {

        $data = $config['content'];
        $file_ext = $config['ext'] ? $config['ext'] : 'jpg'; // 扩展名
        $file_name = isset($config['save_name']) && $config['save_name'] ? $config['save_name'] : 'base64_image'; // 文件实际名字

        // 安全验证
        $rt = $this->_safe_check($file_ext, $data);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        // 保存目录名称
        list($file_path, $config, $diy) = $this->_rand_save_file_path($config, $file_ext, $data);

        // 开始上传存储文件
        $rt = $this->save_file('content', $data, $file_path, $config['attachment'], (int)$config['watermark']);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        // 上传成功
        if ($diy) {
            $url = '自定义存储地址不提供URL';
        } else {
            $url = $config['attachment']['url'].$file_path;
        }

        // 文件预览
        $preview = dr_file_preview_html($url);

        return dr_return_data(1, 'ok', array(
            'ext' => $file_ext,
            'url' => $url,
            'md5' => md5($data),
            'file' => $file_path,
            'size' => $rt['data']['size'],
            'path' => ($config['attachment']['value']['path'] && $config['attachment']['value']['path'] != 'null' ? $config['attachment']['value']['path'] : '').$file_path,
            'name' => $file_name,
            'info' => $rt['data']['info'],
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ));
    }

    public function set_userid($userid) {
        $this->userid = intval($userid);
    }

    // 附件归档存储
    public function save_data($data, $related = '') {
        $related = $related ? $related : 'rand';
        $data['name'] = dr_safe_filename($data['name']);

        // 入库索引表
        $att_db = pc_base::load_model('attachment_model');
        $uploadedfile['module'] = $this->module;
        $uploadedfile['catid'] = $this->catid;
        $uploadedfile['siteid'] = $this->siteid;
        $uploadedfile['userid'] = $this->userid;
        $uploadedfile['isadmin'] = $data['isadmin'];
        $uploadedfile['uploadtime'] = SYS_TIME;
        $uploadedfile['uploadip'] = ip();
        $uploadedfile['status'] = SYS_ATTACHMENT_STAT ? 0 : 1;
        $uploadedfile['authcode'] = md5((string)$data['file']);
        $uploadedfile['filemd5'] = $data['md5'] ? $data['md5'] : 0;
        $uploadedfile['remote'] = $data['remote'];
        $uploadedfile['attachinfo'] = dr_array2string($data['info']);
        $uploadedfile['related'] = $related;
        $uploadedfile['filename'] = $data['name'];
        $uploadedfile['filepath'] = $data['file'];
        $uploadedfile['filesize'] = $data['size'];
        $uploadedfile['fileext'] = $data['ext'];
        $uploadedfile['downloads'] = 0;
        $uploadedfile['isimage'] = in_array($data['ext'], array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp')) ? 1 : 0;
        $aid = $att_db->api_add($uploadedfile);
        $this->uploadedfiles[] = $uploadedfile;
        
        // 入库失败 无主键id 返回msg为准
        if (!$aid) {
            // 删除附件索引
            unlink($data['path']);
            return dr_return_data(0, '附件归档存储失败');
        }

        return dr_return_data($aid, 'ok');
    }

    // 附件存储信息
    public function get_attach_info($id = 0, $image_reduce = 0) {

        // 全局存储
        if ((!$id || $id == 'null') && SYS_ATTACHMENT_SAVE_ID) {
            $id = SYS_ATTACHMENT_SAVE_ID;
        }

        $remote = get_cache('attachment');
        if (isset($remote[$id]) && $remote[$id]) {
            $rt = $remote[$id];
            $rt['image_reduce'] = $image_reduce;
            return $rt;
        }

        return array(
            'id' => 0,
            'url' => SYS_UPLOAD_URL,
            'type' => 0,
            'image_reduce' => $image_reduce,
            'value' => array(
                'path' => SYS_UPLOAD_PATH
            )
        );
    }

    // 头像附件存储信息
    public function get_attach_member($id = 0, $image_reduce = 0) {

        // 全局存储
        if ((!$id || $id == 'null') && SYS_ATTACHMENT_SAVE_ID) {
            $id = SYS_ATTACHMENT_SAVE_ID;
        }

        $remote = get_cache('attachment');
        if (isset($remote[$id]) && $remote[$id]) {
            $rt = $remote[$id];
            $rt['image_reduce'] = $image_reduce;
            return $rt;
        }

        return array(
            'id' => 0,
            'url' => SYS_AVATAR_URL,
            'type' => 0,
            'image_reduce' => $image_reduce,
            'value' => array(
                'path' => SYS_AVATAR_PATH
            )
        );
    }

    // 开始删除文件
    public function _delete_file($index) {
        $cache = pc_base::load_sys_class('cache');
        $att_db = pc_base::load_model('attachment_model');
        $att_index_db = pc_base::load_model('attachment_index_model');

        // 获取文件信息
        $info = $att_db->get_one(array('aid'=>$index['aid']));
        if (!$info) {
            return dr_return_data(0, L('文件数据不存在'));
        }

        upload_json_del($index['aid'],dr_get_file_url($info),$info['filename'],format_file_size($info['filesize']));

        $att_db->delete(array('aid'=>$index['aid']));

        // 删除记录
        $att_index_db->delete(array('aid'=>$index['aid']));

        // 开始删除文件
        $storage = new storage($this->module, $this->catid, $this->siteid);
        if ($info['module']=='member' && !$info['catid']) {
            $storage->delete($this->get_attach_member($info['remote']), $info['filepath']);
        } else {
            $storage->delete($this->get_attach_info($info['remote']), $info['filepath']);
        }

        // 删除缩略图的缓存
        if (in_array($info['fileext'], array('png', 'jpeg', 'jpg', 'gif', 'webp'))) {
            dr_dir_delete(SYS_THUMB_PATH.md5($index['aid']).'/', true);
        }

        // 删除附件进行记录
        if (CI_DEBUG) {
            log_message('debug', '删除附件（#'.$index['aid'].'）'.dr_get_file_url($info));
        }

        // 删除缓存
        $cache->del_file('attach-info-'.$index['aid'], 'attach');

        return dr_return_data(1, L('删除成功'));
    }

    /**
     * 存储文件
     */
    public function save_file($type, $data, $file_path, $attachment, $watermark = 0) {

        // 存储目录安全验证
        if ($attachment['value']['path']
            && strpos($attachment['value']['path'], 'config') !== false) {
            return dr_return_data(0, L('存储目录不能包含config文字'));
        }

        // 按照附件存储类型来保存文件
        $storage = new storage($this->module, $this->catid, $this->siteid);
        $rt = $storage->upload($type == 'upload' ? 1 : 0, $data, $file_path, $attachment, $watermark);
        if ($rt['code']) {
            pc_base::load_sys_class('hooks')::trigger('upload_file', [
                'type' => $type,
                'data' => $data,
                'file_name' => $file_path,
                'file_path' => $attachment['value']['path'].$file_path,
                'attachment' => $attachment
            ]);
        }

        return $rt;
    }


    /**
     * 上传错误
     */
    protected function _error_msg($code) {
        return !$this->error[$code] ? '上传错误('.$code.')' : $this->error[$code];
    }

    /**
     * 获取文件名
     */
    protected function _file_name($name) {
        strpos($name, '/') !== false && $name = trim(strrchr($name, '/'), '/');
        return substr($name, 0, strrpos($name, '.'));
    }

    /**
     * 获取文件扩展名
     */
    protected function _file_ext($name) {

        if (strlen($name) > 300) {
            return '';
        }

        $ext = str_replace('.', '', trim(strtolower(strrchr($name, '.')), '.'));

        if (strlen($ext) > 10) {
            foreach (array('gif', 'jpg', 'jpeg', 'png', 'webp') as $t) {
                if (stripos($name, $t) !== false) {
                    return $t;
                }
            }
        }

        return $ext;
    }

    /**
     * 随机存储的文件名
     */
    protected function _rand_save_file_name($file) {
        return substr(md5(SYS_TIME.(is_array($file) ? dr_array2string($file) : $file).uniqid()), rand(0, 20), 15);
    }

    /**
     * 随机存储的文件路径
     */
    protected function _rand_save_file_path($config, $file_ext, $file) {
        $pinyin = pc_base::load_sys_class('pinyin');

        $diy = 0;
        $name = '';
        if (isset($config['save_name']) && $config['save_name']) {
            if ($config['save_name'] == 'null') {
                // 按原始名称
                if (is_array($file) && isset($file['name']) && $file['name']) {
                    $name = trim($pinyin->result(dr_safe_filename($file['name'])), '.'.$file_ext);
                }
            } else {
                $name = $config['save_name'];
            }
        }

        // 随机新名字
        !$name && $name = $this->_rand_save_file_name($file);

        if (isset($config['save_file']) && $config['save_file']) {
            // 指定存储名称
            $diy = 1;
            $file_path = $config['save_file'];
            $config['save_file'] = dirname($file_path);
            $config['attachment']['value']['path'] = 'null';
        } else {
            if (isset($config['save_path']) && $config['save_path']) {
                // 指定存储路径
                $diy = 1;
                $path = $config['save_path'];
                $config['save_file'] = $path;
                $config['attachment']['value']['path'] = 'null';
            } else {
                if (isset($config['path']) && $config['path']) {
                    $path = $config['path'].'/'; // 按开发自定义参数
                } elseif (defined('SYS_ATTACHMENT_SAVE_TYPE') && SYS_ATTACHMENT_SAVE_TYPE) {
                    // 按后台设置目录
                    if (SYS_ATTACHMENT_SAVE_DIR) {
                        $path = str_replace(
                                array('{y}', '{m}', '{d}', '{yy}', '.'),
                                array(date('Y', SYS_TIME), date('m', SYS_TIME), date('d', SYS_TIME), date('y', SYS_TIME), ''),
                                trim(SYS_ATTACHMENT_SAVE_DIR, '/')).'/';
                    } else {
                        $path = '';
                    }
                } else {
                    // 默认目录格式
                    $path = date('Y/md/', SYS_TIME);
                }
            }
            $file_path = (SYS_ATTACHMENT_FILE ? $this->siteid.'/' : '').$path.$name.'.'.$file_ext;
        }

        return array($file_path, $config, $diy);
    }
}

/**
 * 存储工厂类
 */
class storage {

    // 存储对象
    protected $module;
    protected $catid;
    protected $siteid;
    protected $object;
    
    function __construct($module='', $catid = 0,$siteid = 0) {
        $this->catid = intval($catid);
        $this->siteid = intval($siteid)== 0 ? 1 : intval($siteid);
        $this->module = $module ? $module : 'content';
    }

    private function _init($attachment) {

        // 选择存储策略
        if ($attachment['type']) {
            // 云存储
            $path = PC_PATH.'plugin/storage/';
            $local = dr_dir_map($path, 1);
            foreach ($local as $dir) {
                if (is_file($path.$dir.'/app.php')) {
                    $cfg = require $path.$dir.'/app.php';
                    if ($cfg['id'] && $cfg['id'] == $attachment['type']) {
                        require_once $path.$dir.'.php';
                        $this->object = new $dir($this->module, $this->catid, $this->siteid);
                    }
                }
            }
            if (!$this->object) {
                exit(dr_array2string(dr_return_data(0, '云存储类型['.$attachment['type'].']对应的程序不存在')));
            }
        } else {
            // 本地存储
            $this->object = new local($this->module, $this->catid, $this->siteid);
        }
    }

    // 文件上传入口
    public function upload($type, $data, $file_path, $attachment, $watermark) {

        $this->_init($attachment);
        return $this->object->init($attachment, $file_path)->upload($type, $data, $watermark);
    }

    // 文件删除入口
    public function delete($attachment, $filename) {

        $this->_init($attachment);
        return $this->object->init($attachment, $filename)->delete();
    }

    // 文件上传到本地目录
    public function uploadfile($type, $data, $fullname, $watermark, $attachment) {

        if ($type) {
            // 移动失败
            if (!(dr_move_uploaded_file($data, $fullname) || !is_file($fullname))) {
                return dr_return_data(0, L('文件移动失败'));
            }
            $filesize = filesize($fullname);
        } else {
            $filesize = file_put_contents($fullname, $data);
        }

        if (!$filesize || !is_file($fullname)) {
            log_message('error', '文件创建失败：'.$fullname);
            return dr_return_data(0, L('文件创建失败'));
        }

        $info = array();
        // 图片处理
        if (dr_is_image($fullname)) {
            $image = pc_base::load_sys_class('image');
            // 获取图片尺寸
            $img = getimagesize($fullname);
            if (!$img) {
                // 删除文件
                unlink($fullname);
                return dr_return_data(0, L('此图片不是一张可用的图片'));
            }
            // 图片压缩处理
            if ($attachment['image_reduce']) {
                // 处理图片大小是否溢出内存
                if ($image->memory_limit($img)) {
                    CI_DEBUG && log_message('debug', '图片['.$fullname.']分辨率太大导致服务器内存溢出，无法进行压缩处理，已按原图存储');
                } else {
                    $image->reduce($fullname, $attachment['image_reduce']);
                }
            }
            // 强制水印
            if ($watermark && dr_site_value('watermark_enable', $this->siteid) && ($config = get_cache('site', $this->siteid, 'param'))) {
                // 处理图片大小是否溢出内存
                if ($image->memory_limit($img)) {
                    CI_DEBUG && log_message('debug', '图片['.$fullname.']分辨率太大导致服务器内存溢出，无法进行压缩处理，已按原图存储');
                } else {
                    $config['source_image'] = $fullname;
                    $config['dynamic_output'] = false;
                    $image->watermark($config);
                }
            }
            $info = array(
                'width' => $img[0],
                'height' => $img[1],
            );
        }

        return dr_return_data(1, $filesize, $info);
    }
}

/**
 * 本地文件存储
 */
class local {

    protected $module;
    protected $catid;
    protected $siteid;
    // 存储内容
    protected $data;

    // 文件存储路径
    protected $filename;

    // 文件存储目录
    protected $filepath;

    // 附件存储的信息
    protected $attachment;

    // 是否进行图片水印
    protected $watermark;

    // 完整的文件目录
    protected $fullpath;

    // 完整的文件路径
    protected $fullname;

    // 是否指定路径
    protected $is_diy_save_path = 0;
    
    function __construct($module='', $catid = 0,$siteid = 0) {
        $this->catid = intval($catid);
        $this->siteid = intval($siteid)== 0 ? 1 : intval($siteid);
        $this->module = $module ? $module : 'content';
    }

    // 初始化参数
    public function init($attachment, $filename) {

        if ($attachment['value']['path'] == 'null') {
            // 表示自定义save_path
            $attachment['value']['path'] = '';
            $this->filename = $filename;
            $this->filepath = dirname($filename);
            $this->is_diy_save_path = 1;
        } else {
            $this->filename = trim($filename, DIRECTORY_SEPARATOR);
            $this->filepath = dirname($filename);
            $this->filepath == '.' && $this->filepath = '';
            $this->is_diy_save_path = 0;
            if (is_dir(SYS_UPLOAD_PATH.$attachment['value']['path'])) {
                // 相对路径
                $attachment['value']['path'] = SYS_UPLOAD_PATH.$attachment['value']['path'];
            }
        }

        $this->attachment = $attachment;
        $this->fullpath = $this->attachment['value']['path'].$this->filepath;
        $this->fullname = $this->attachment['value']['path'].$this->filename;

        return $this;
    }

    // 文件上传模式
    public function upload($type, $data, $watermark) {

        $this->data = $data;
        $this->watermark = $watermark;

        // 目录不存在先创建它
        !is_dir($this->fullpath) && dr_mkdirs($this->fullpath);
        if (!is_dir($this->fullpath)) {
            log_message('error', '目录创建失败：'.$this->fullpath);
            return dr_return_data(0, L('创建目录'.(IS_ADMIN ? $this->fullpath : '').'失败'));
        }

        $storage = new storage($this->module, $this->catid, $this->siteid);
        $rt = $storage->uploadfile($type, $this->data, $this->fullname, $watermark, $this->attachment);
        if (!$rt['code']) {
            return $rt;
        }

        // 上传成功
        return dr_return_data(1, 'ok', array(
            'url' => $this->is_diy_save_path ? '指定存储路径时无法获取到访问URL地址' : $this->attachment['url'].$this->filename,
            'md5' => md5_file($this->fullname),
            'size' => $rt['msg'],
            'info' => $rt['data']
        ));
    }

    // 删除文件
    public function delete() {
        @unlink($this->fullname);
    }

}
?>