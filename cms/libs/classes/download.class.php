<?php
/**
 * 附件类
 */
class download {

    protected $module;
    protected $catid;
    protected $siteid;

    /**
     * 构造函数
     */
    public function __construct($module='', $catid = 0,$siteid = 0) {
        $this->catid = intval($catid);
        $this->siteid = intval($siteid)== 0 ? 1 : intval($siteid);
        $this->module = $module ? $module : 'content';
        $this->userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : sys_auth($_POST['userid_h5'],'DECODE'));
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
        $temp = preg_replace('/<pre(.*)<\/pre>/siU', '', $value);
        $temp = preg_replace('/<code(.*)<\/code>/siU', '', $temp);
        if(!preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $temp, $imgs)) return $value;
        foreach ($imgs[3] as $img) {
            $ext = get_file_ext($img);
            if (!$ext) {
                continue;
            }
            // 下载图片
            if (strpos($img, 'http') === 0) {
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
                    }
                    if (isset($sites[$domain])) {
                        // 过滤站点域名
                    } elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
                        // 过滤附件白名单
                    } else {
                        if(strpos($img, '://') === false) continue;
                        $zj = 0;
                        $remote = getcache('attachment', 'commons');
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
                            if ($rt['code']) {
                                $att = $upload->save_data($rt['data']);
                                if ($att['code']) {
                                    // 归档成功
                                    $value = str_replace($img, $rt['data']['url'], $value);
                                    $img = $att['code'];
                                    // 标记附件
                                    $GLOBALS['downloadfiles'][] = $rt['data']['url'];
                                }
                            }
                        }
                    }
                }
            }
        }
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
        foreach ($imgs[3] as $img) {
            $ext = get_file_ext($img);
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
                if ($rt['code']) {
                    $att = $upload->save_data($rt['data']);
                    if ($att['code']) {
                        // 归档成功
                        $value = str_replace($img, $rt['data']['url'], $value);
                        $img = $att['code'];
                        // 标记附件
                        $GLOBALS['downloadfiles'][] = $rt['data']['url'];
                    }
                }
            }
        }
        return $value;
    }
}