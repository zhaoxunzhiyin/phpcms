<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * UEditor编辑器通用上传类
 */
class Uploader
{
    private $input;
    private $cache;
    private $grouplist;
    private $type;
    private $module;
    private $catid;
    private $siteid;
    private $userid;
    private $isadmin;
    private $groupid;
    private $is_wm;
    private $attachment;
    private $image_reduce;
    private $upload;
    private $rid; //存储标识
    private $watermark; //是否图片水印
    private $attachment_info; //附件存储信息

    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileUrl; //完整文件URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确",
        "INVALID_URL" => "非法 URL",
        "INVALID_IP" => "非法 IP"
    );

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config 配置项
     * @param bool $base64 是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function __construct($fileField, $config, $type = "upload") {
        pc_base::load_sys_class('upload','',0);
        $this->input = pc_base::load_sys_class('input');
        $this->grouplist = getcache('grouplist', 'member');
        $this->fileField = $fileField;
        $this->config = $config;
        $this->type = $type;
        $this->siteid = $this->config['siteid'] ? $this->config['siteid'] : 1;
        $this->module = $this->config['module'] ? $this->config['module'] : 'content';
        $this->catid = $this->config['catid'];
        $this->userid = $this->config['userid'];
        $this->isadmin = $this->config['isadmin'];
        $this->groupid = $this->config['groupid'];
        $this->is_wm = $this->config['is_wm'];
        $this->attachment = $this->config['attachment'];
        $this->image_reduce = $this->config['image_reduce'];
        $this->upload = new upload($this->module,$this->catid,$this->siteid);
        $this->upload->set_userid($this->userid);
        $this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().$this->userid);
        $this->watermark = dr_site_value('ueditor', $this->siteid) ? 1 : $this->is_wm;
        $this->attachment_info = $this->upload->get_attach_info((int)$this->attachment, (int)$this->image_reduce);

        if (defined('SYS_CSRF') && SYS_CSRF && csrf_hash() != (string)$_GET['token']) {
            // 错误提示
            $this->stateInfo = '跨站验证禁止上传文件';
            return;
        }

        if ($this->isadmin && !$this->userid) {
            // 错误提示
            $this->stateInfo = '请登录在操作';
            return;
        }

        if (!$this->isadmin && !$this->grouplist[$this->groupid]['allowattachment']) {
            // 错误提示
            $this->stateInfo = '您的用户组不允许上传文件';
            return;
        }

        if (!$this->isadmin && $this->check_upload($this->userid)) {
            // 错误提示
            $this->stateInfo = '用户存储空间已满';
            return;
        }

        if ($type == "remote") {
            $this->saveRemote();
        } else if($type == "base64") {
            $this->upBase64();
        } else {
            $this->upFile();
        }
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        } elseif (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
            return;
        } elseif ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        }

        $this->oriName = (string)$file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }

        // 安全检测
        $rt = $this->upload->_safe_check(trim($this->getFileExt(), '.'), $file["tmp_name"]);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $rt = $this->upload->save_file(
            'upload',
            $file["tmp_name"],
            $this->fullName,
            $this->attachment_info,
            $this->watermark
        );
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $this->fileUrl = $this->attachment_info['url'].$this->fullName;
        $this->stateInfo = $this->stateMap[0];

        // 存储附件
        $this->save_attach($rt);
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $this->input->post($this->fileField);
        $img = base64_decode($base64Data);

        $this->oriName = (string)$this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = $this->upload->_safe_check(trim($this->getFileExt(), '.'), $img);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $rt = $this->upload->save_file(
            'content',
            $img,
            $this->fullName,
            $this->attachment_info,
            $this->watermark
        );
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $this->fileUrl = $this->attachment_info['url'].$this->fullName;
        $this->stateInfo = $this->stateMap[0];

        // 存储附件
        $this->save_attach($rt);

    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = html2code($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }
        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");
            return;
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? (string)$m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = $this->upload->_safe_check(trim($this->getFileExt(), '.'), $img);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $rt = $this->upload->save_file(
            'content',
            $img,
            $this->fullName,
            $this->attachment_info,
            $this->watermark
        );
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $this->fileUrl = $this->attachment_info['url'].$this->fullName;
        $this->stateInfo = $this->stateMap[0];

        // 存储附件
        $this->save_attach($rt);

    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr((string)$this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", SYS_TIME, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr((string)$this->oriName, 0, strrpos((string)$this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = substr(md5($this->siteid.SYS_TIME.$oriName), rand(0, 20), 15); // 随机新名字
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();
        $this->fileName = str_replace($ext, '', $oriName);

        return trim($format . $ext, '/');
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

    /**
     * 存储归档
     * @return bool
     */
    private function save_attach($rt)
    {
        $data = array();
        if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
            $att_db = pc_base::load_model('attachment_model');
            $att = $att_db->get_one(array('userid'=>$this->userid,'filemd5'=>$rt['data']['md5'],'fileext'=>trim($this->fileType, '.'),'filesize'=>$this->fileSize));
            if ($att) {
                $data = dr_return_data($att['aid'], 'ok');
                // 删除现有附件
                // 开始删除文件
                $storage = new storage($this->module,$this->catid,$this->siteid);
                $storage->delete($this->upload->get_attach_info((int)$this->attachment), $this->fullName);
                $rt['data'] = get_attachment($att['aid']);
            }
        }
        if (!$data) {
            $data = $this->upload->save_data(array(
                'ext' => trim($this->fileType, '.'),
                'url' => $this->fileUrl,
                'md5' => $rt['data']['md5'],
                'file' => $this->fullName,
                'size' => $this->fileSize,
                'path' => $this->attachment_info['value']['path'].$this->fullName,
                'name' => strstr((string)$this->oriName, '.', true),
                'info' => $rt['data']['info'],
                'remote' => $this->attachment_info['id'],
                'isadmin' => $this->isadmin,
            ), 'ueditor:'.$this->rid);
        } else {
            $this->oriName = $rt['data']['filename'];
            $this->fileSize = $rt['data']['filesize'];
            $this->fileType = $rt['data']['fileext'];
            $this->fullName = $rt['data']['filepath'];
            $this->fileUrl = $this->attachment_info['url'].$rt['data']['filepath'];
        }
        if($rt && $data) {
            upload_json($data['code'],$this->fileUrl,strstr((string)$this->oriName, '.', true),format_file_size($this->fileSize));
        } else {
            $this->stateInfo = $rt['msg'];
        }
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "state" => $this->stateInfo,
            "url" => $this->fileUrl,
            "title" => strstr((string)$this->oriName, '.', true),
            "original" => strstr((string)$this->oriName, '.', true),
            "type" => $this->fileType,
            "size" => $this->fileSize
        );
    }

    // 验证附件上传权限，直接返回1 表示空间不够
    public function check_upload($uid) {
        if ($this->isadmin) {
            return;
        }
        // 获取用户总空间
        $total = abs((int)$this->grouplist[$this->groupid]['filesize']) * 1024 * 1024;
        if ($total) {
            // 判断空间是否满了
            $filesize = $this->get_member_filesize($uid);
            if ($filesize >= $total) {
                return 1;
            }
        }
        return;
    }

    // 用户已经使用附件空间
    public function get_member_filesize($uid) {
        $db = pc_base::load_model('attachment_model');
        $db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.$uid.' and isadmin='.$this->isadmin);
        $row = $db->fetch_array();
        return intval($row[0]['filesize']);
    }

}