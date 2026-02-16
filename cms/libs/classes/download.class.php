<?php
/**
 * 附件类
 */
class download {

    protected $module;
    protected $catid;
    protected $siteid;
    protected $input;
    protected $grouplist;
    protected $isadmin;
    protected $groupid;
    protected $userid;
    protected $rid;
    protected $sitedb;

    /**
     * 构造函数
     */
    public function __construct($module='', $catid = 0,$siteid = 0) {
        $this->catid = intval($catid);
        $this->siteid = intval($siteid)== 0 ? 1 : intval($siteid);
        $this->module = $module ? $module : 'content';
        $this->input = pc_base::load_sys_class('input');
        $this->grouplist = getcache('grouplist', 'member');
        $this->isadmin = defined('IS_ADMIN') && IS_ADMIN && param::get_session('roleid') ? 1 : 0;
        $this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
        $this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().intval($this->userid));
        $this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
    }

    /**
     * 下载远程文件
     * @param $value 传入下载内容
     * @param $watermark 是否加入水印
     * @param $attachment
     * @param $image_reduce  图片压缩大小
     * @param $catid
     */
    public function download($value, $watermark = 0, $attachment = 0, $image_reduce = '', $catid = 0) {
        pc_base::load_sys_class('upload','',0);
        $upload = new upload($this->module,$catid,$this->siteid);
        $upload->set_userid($this->userid);
        $base64 = strpos($value, ';base64,');
        if ($base64) {
            $value = str_replace('_"data:image', '"data:image', $value);
        }
        $temp = preg_replace('/<pre(.*)<\/pre>/siU', '', $value);
        $temp = preg_replace('/<code(.*)<\/code>/siU', '', $temp);
        if(!preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $temp, $imgs)) return $value;
        $downloadfiles = [];
        foreach ($imgs[3] as $img) {
            if ($base64 && preg_match('/^(data:\s*image\/(\w+);base64,)/i', $img, $result)) {
                // 处理图片
                $ext = strtolower($result[2]);
                if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    continue;
                }
                $content = base64_decode(str_replace($result[1], '', $img));
                if (strlen($content) > 30000000) {
                    continue;
                }
                $rt = $upload->base64_image([
                    'ext' => $ext,
                    'content' => $content,
                    'watermark' => $watermark,
                    'attachment' => $upload->get_attach_info(intval($attachment), intval($image_reduce)),
                ]);
                $data = array();
                if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
                    $att_db = pc_base::load_model('attachment_model');
                    $att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
                    if ($att) {
                        $data = dr_return_data($att['aid'], 'ok');
                        // 删除现有附件
                        // 开始删除文件
                        $storage = new storage($this->module,$catid,$this->siteid);
                        $storage->delete($upload->get_attach_info((int)$attachment), $rt['data']['file']);
                        $rt['data'] = get_attachment($att['aid']);
                    }
                }
                if (!$data) {
                    $rt['data']['isadmin'] = $this->isadmin;
                    $data = $upload->save_data($rt['data'], 'ueditor:'.$this->rid);
                }
                $value = str_replace($img, $rt['data']['url'], $value);
                // 标记附件
                $downloadfiles[] = $data['code'];
            } else {
                $ext = get_image_ext($img);
                if (!$ext) {
                    continue;
                }
                // 下载图片
                if (strpos($img, 'http') === 0) {
                    if (!$this->isadmin && $this->check_upload($this->userid)) {
                        //用户存储空间已满
                        log_message('debug', '用户存储空间已满');
                    } else {
                        // 正常下载
                        // 判断域名白名单
                        $arr = parse_url($img);
                        $domain = $arr['host'];
                        if ($domain) {
                            $this->sitedb = pc_base::load_model('site_model');
                            $data = $this->sitedb->select();
                            $sites = array();
                            foreach ($data as $t) {
                                $site_domain = parse_url($t['domain']);
                                if ($site_domain['port']) {
                                    $sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
                                } else {
                                    $sites[$site_domain['host']] = $t['siteid'];
                                }
                                if ($t['mobile_domain']) {
                                    $site_mobile_domain = parse_url($t['mobile_domain']);
                                    if ($site_mobile_domain['port']) {
                                        $sites[$site_mobile_domain['host'].':'.$site_mobile_domain['port']] = $t['siteid'];
                                    } else {
                                        $sites[$site_mobile_domain['host']] = $t['siteid'];
                                    }
                                }
                            }
                            if (isset($sites[$domain])) {
                                // 过滤站点域名
                            } elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
                                // 过滤附件白名单
                            } else {
                                if(strpos($img, '://') === false) continue;
                                $zj = 0;
                                $remote = get_cache('attachment');
                                if ($remote) {
                                    foreach ($remote as $t) {
                                        if (strpos($t['url'], $domain) !== false) {
                                            $zj = 1;
                                            break;
                                        }
                                    }
                                }
                                if ($zj == 0) {
                                    // 可以下载文件
                                    // 下载远程文件
                                    $rt = $upload->down_file(array(
                                        'url' => $img,
                                        'timeout' => 5,
                                        'watermark' => $watermark,
                                        'attachment' => $upload->get_attach_info(intval($attachment), intval($image_reduce)),
                                        'file_ext' => $ext,
                                    ));
                                    $data = array();
                                    if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
                                        $att_db = pc_base::load_model('attachment_model');
                                        $att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
                                        if ($att) {
                                            $data = dr_return_data($att['aid'], 'ok');
                                            // 删除现有附件
                                            // 开始删除文件
                                            $storage = new storage($this->module,$catid,$this->siteid);
                                            $storage->delete($upload->get_attach_info((int)$attachment), $rt['data']['file']);
                                            $rt['data'] = get_attachment($att['aid']);
                                        }
                                    }
                                    if (!$data) {
                                        $rt['data']['isadmin'] = $this->isadmin;
                                        $data = $upload->save_data($rt['data'], 'ueditor:'.$this->rid);
                                    }
                                    $value = str_replace($img, $rt['data']['url'], $value);
                                    // 标记附件
                                    $downloadfiles[] = $data['code'];
                                }
                            }
                        }
                    }
                }
            }
        }
        isset($downloadfiles) && $downloadfiles && pc_base::load_sys_class('cache')->set_data('downloadfiles-'.$this->siteid, $downloadfiles, 3600);
        return $value;
    }

    /**
     * 保存本地文件
     */
    public function upload_local($value, $watermark = 0, $attachment = 0, $image_reduce = '', $catid = 0) {
        pc_base::load_sys_class('upload','',0);
        $upload = new upload($this->module,$catid,$this->siteid);
        $upload->set_userid($this->userid);
        $temp = preg_replace('/<pre(.*)<\/pre>/siU', '', $value);
        $temp = preg_replace('/<code(.*)<\/code>/siU', '', $temp);
        if(!preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $temp, $imgs)) return $value;
        $downloadfiles = [];
        foreach ($imgs[3] as $img) {
            $ext = get_image_ext($img);
            if (!$ext) {
                continue;
            }
            // 保存本地文件
            if (strpos($img, 'file://')  === 0) {
                $rt = $upload->down_file(array(
                    'url' => $img,
                    'timeout' => 5,
                    'watermark' => $watermark,
                    'attachment' => $upload->get_attach_info(intval($attachment), intval($image_reduce)),
                    'file_ext' => $ext,
                ));
                $data = array();
                if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
                    $att_db = pc_base::load_model('attachment_model');
                    $att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
                    if ($att) {
                        $data = dr_return_data($att['aid'], 'ok');
                        // 删除现有附件
                        // 开始删除文件
                        $storage = new storage($this->module,$catid,$this->siteid);
                        $storage->delete($upload->get_attach_info((int)$attachment), $rt['data']['file']);
                        $rt['data'] = get_attachment($att['aid']);
                    }
                }
                if (!$data) {
                    $rt['data']['isadmin'] = $this->isadmin;
                    $data = $upload->save_data($rt['data'], 'ueditor:'.$this->rid);
                }
                $value = str_replace($img, $rt['data']['url'], $value);
                // 标记附件
                $downloadfiles[] = $data['code'];
            }
        }
        isset($downloadfiles) && $downloadfiles && pc_base::load_sys_class('cache')->set_data('downloadfiles-'.$this->siteid, $downloadfiles);
        return $value;
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
        $db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.intval($uid).' and isadmin='.intval($this->isadmin));
        $row = $db->fetch_array();
        return intval($row[0]['filesize']);
    }
}