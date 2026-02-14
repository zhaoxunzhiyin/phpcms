<?php
/**
 * 列表格式化函数库
 */

class function_list {

    protected $uid_data = array();
    protected $cid_data = array();
    protected $m;
    protected $c;

    public function __construct() {
        $this->m = defined('ROUTE_M') && ROUTE_M ? ROUTE_M : 'content';
        $this->c = defined('ROUTE_C') && ROUTE_C ? ROUTE_C : 'content';
    }

    // 用于列表显示栏目
    public function catid($catid, $param = array(), $data = array(), $field = array()) {

        if (!$catid) {
            return '';
        }

        $url = IS_ADMIN ? '?m='.$this->m.'&c='.$this->c.'&a=init&menuid='.$param['menuid'].'&catid='.$catid.'&pc_hash='.dr_get_csrf_token() : dr_cat_value($catid, 'url').'" target="_blank';
        $value = dr_cat_value($catid, 'catname');

        return '<a href="'.$url.'">'.$value.'</a>';
    }

    // 用于列表显示标题
    public function title($value, $param = array(), $data = array(), $field = array()) {

        $value = html2code(clearhtml($value));
        $title = ($data['thumb'] ? '<i class="fa fa-photo"></i> ' : '').dr_keyword_highlight($value, $param['keyword']);
        !$title && $title = '...';

        if($data['status']==99) {
            if($data['islink']) {
                $url = $data['url'];
            } elseif(strpos($data['url'],'http://')!==false || strpos($data['url'],'https://')!==false) {
                $url = $data['url'];
            } else {
                $release_siteurl = substr((string)dr_site_info('domain', dr_cat_value($data['catid'], 'siteid')),0,-1);
                $url = $release_siteurl.$data['url'];
            }
        } else {
            $url = '?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'';
        }

        $this->m!='content' && $this->c!='content' && $data['url'] = $url = '';

        return (isset($data['url']) && $data['url'] && $url ? '<a href="'.$url.'" target="_blank"'.($data['status']!=99 ? ' onclick=\'window.open("?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'","manage")\'' : '').' class="tooltips"'.title_style($data['style']).' data-container="body" data-placement="top" data-original-title="'.$value.'" title="'.$value.'">'.$title.'</a>' : $title).($data['islink'] > 0 ? ' <i class="fa fa-link font-green tooltips" data-container="body" data-placement="top" data-original-title="'.L('转向链接').'" title="'.L('转向链接').'"></i>' : '');
    }

    // 用于列表显示内容
    public function content($value, $param = array(), $data = array(), $field = array()) {

        $value = html2code(clearhtml($value));
        $title = dr_keyword_highlight($value, $param['keyword']);
        !$title && $title = '...';

        if($data['status']==99) {
            if($data['islink']) {
                $url = $data['url'];
            } elseif(strpos($data['url'],'http://')!==false || strpos($data['url'],'https://')!==false) {
                $url = $data['url'];
            } else {
                $release_siteurl = substr((string)dr_site_info('domain', dr_cat_value($data['catid'], 'siteid')),0,-1);
                $url = $release_siteurl.$data['url'];
            }
        } else {
            $url = '?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'';
        }

        $this->m!='content' && $this->c!='content' && $data['url'] = $url = '';

        return isset($data['url']) && $data['url'] && $url ? '<a href="'.$url.'" target="_blank"'.($data['status']!=99 ? ' onclick=\'window.open("?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'","manage")\'' : '').' class="tooltips" data-container="body" data-placement="top" data-original-title="'.$value.'" title="'.$value.'">'.$title.'</a>' : $title;
    }

    // 用于列表显示浏览数
    public function hits($value, $param = array(), $data = array(), $field = array()) {

        $hits_db = pc_base::load_model('hits_model');
        $hits_r = $hits_db->get_one(array('hitsid'=>'c-'.dr_cat_value($data['catid'], 'modelid').'-'.$data['id']));
        $value = L('today_hits', 'content').'：'.$hits_r['dayviews'].'<br>'.L('yestoday_hits').'：'.$hits_r['yesterdayviews'].'<br>'.L('week_hits').'：'.$hits_r['weekviews'].'<br>'.L('month_hits').'：'.$hits_r['monthviews'];
        $title = $hits_r['views'];

        return '<span class="tooltips" data-container="body" data-placement="top" data-html="true" data-original-title="'.$value.'" title="'.$value.'">'.$title.'</span>';
    }

    // 用于列表显示联动菜单值
    public function linkage_address($value, $param = array(), $data = array(), $field = array()) {
        if (!$value) {
            return '';
        }
        return dr_linkagepos('address', $value, '-');
    }

    // 用于列表显示状态
    public function status($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            $html = '<span class="label label-sm label-default">'.L('未通过');
        } elseif ($value == 99) {
            $html = '<span class="label label-sm label-success">'.L('已通过');
        } elseif ($value == 100) {
            $html = '<span class="label label-sm label-warning">'.L('回收站');
        } else {
            $html = '<span class="label label-sm label-danger">'.L('待审核');
        }

        return '<label>'.$html.'</span></label>';
    }

    // 用于列表显示时间日期格式
    public function datetime($value, $param = array(), $data = array(), $field = array()) {
        if (!$value) {
            return '';
        }
        if ($field && $field['setting']['fieldtype']=='int') {
            if ($field['setting']['format']) {
                return dr_date($value, null, 'red');
            } else {
                return dr_date($value, 'Y-m-d', 'red');
            }
        } else if ($field && $field['setting']['fieldtype']=='varchar') {
            return $value;
        }
        return dr_date($value, null, 'red');
    }

    // 用于列表显示日期格式
    public function date($value, $param = array(), $data = array(), $field = array()) {
        if (!$value) {
            return '';
        }
        if ($field && $field['setting']['fieldtype']=='varchar') {
            return $value;
        }
        return dr_date($value, 'Y-m-d', 'red');
    }

    // 用于列表显示作者
    public function author($value, $param = array(), $data = array(), $field = array()) {
        if (!$value) {
            return L('游客');
        }
        if (IS_ADMIN && ($this->m=='content' && $this->c=='content' && !$data['sysadd']) || $this->m=='member' && $this->c=='member' || $this->m=='formguide' && $this->c=='formguide_info') {
            return $value ? ($this->m=='member' && $this->c=='member' && dr_member_info($data['userid'], 'islock') ? '<i class="fa fa-lock"></i> ' : '').'<a href="javascript:dr_iframe_show(\'用户信息\', \'?m=member&c=member&a=memberinfo&username='.urlencode($value).'\', \'50%\')">'.$value.'</a>' : L('游客');
        }
        return $value ? $value : L('游客');
    }

    // 用于列表显示作者
    public function userid($userid, $param = array(), $data = array(), $field = array()) {
        // 查询username
        if (strlen($userid) > 12) {
            return L('游客');
        }
        if ($data['sysadd']) {
            $admin_db = pc_base::load_model('admin_model');
            $userinfo = $admin_db->get_one(array('userid'=>$userid));
            $username = $userinfo['realname'] ? $userinfo['realname'] : $userinfo['username'];
        } else {
            $member_db = pc_base::load_model('member_model');
            $userinfo = $member_db->get_one(array('userid'=>$userid));
            $username = $userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['username'];
        }
        $this->uid_data[$userid] = isset($this->uid_data[$userid]) && $this->uid_data[$userid] ? $this->uid_data[$userid] : $username;
        return $this->uid_data[$userid] ? $this->uid_data[$userid] : L('游客');
    }

    // 头像
    public function avatar($value, $param = array(), $data = array(), $field = array()) {
        return '<a href="javascript:dr_iframe_show(\'用户信息\', \'?m=member&c=member&a=memberinfo&userid='.intval($data['userid']).'\', \'50%\')"><img class="img-circle" src="'.get_memberavatar(intval($data['userid'])).'" style="width:30px;height:30px"></a>';
    }

    // 用于列表关联主题
    public function ctitle($cid, $param = array(), $data = array(), $field = array()) {
        if (!$cid) {
            return L('未关联');
        }
        $content_db = pc_base::load_model('content_model');
        $content_db->set_model(dr_cat_value($data['catid'], 'modelid'));
        $content_r = $content_db->get_one(array('id'=>$cid));
        $this->cid_data[$cid] = isset($this->cid_data[$cid]) && $this->cid_data[$cid] ? $this->cid_data[$cid] : $content_r;
        return $this->cid_data[$cid] ? $this->title($this->cid_data[$cid]['title'], $param, $this->cid_data[$cid]) : L('关联主题不存在');
    }

    // 标题带推荐位图标
    public function ptitle($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            return '';
        }

        $value = html2code(clearhtml($value));
        $title = ($data['thumb'] ? '<i class="fa fa-photo"></i> ' : '').dr_keyword_highlight($value, $param['keyword']);
        !$title && $title = '...';

        if($data['status']==99) {
            if($data['islink']) {
                $url = $data['url'];
            } elseif(strpos($data['url'],'http://')!==false || strpos($data['url'],'https://')!==false) {
                $url = $data['url'];
            } else {
                $release_siteurl = substr((string)dr_site_info('domain', dr_cat_value($data['catid'], 'siteid')),0,-1);
                $url = $release_siteurl.$data['url'];
            }
        } else {
            $url = '?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'';
        }

        $this->m!='content' && $this->c!='content' && $data['url'] = $url = '';

        if ($data['id']) {
            $position_db = pc_base::load_model('position_model');
            $position_data_db = pc_base::load_model('position_data_model');
            $flag = $position_data_db->count(array('id'=>$data['id'], 'catid'=>$data['catid']));
        }
        $html = (isset($data['url']) && $data['url'] && $url ? '<a href="'.$url.'" target="_blank"'.($data['status']!=99 ? ' onclick=\'window.open("?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'","manage")\'' : '').' class="tooltips"'.title_style($data['style']).' data-container="body" data-placement="top" data-original-title="'.$value.'" title="'.$value.'">'.$title.'</a>' : $title).($flag ? ' <i class="fa fa-flag font-blue tooltips" data-original-title="'.L('推荐位').'" title="'.L('推荐位').'"></i>' : '').($data['islink'] > 0 ? ' <i class="fa fa-link font-green tooltips" data-original-title="'.L('转向链接').'" title="'.L('转向链接').'"></i>' : '');
        return $html;
    }

    // 标题带推荐位
    public function position($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            return '';
        }

        $value = html2code(clearhtml($value));
        $title = ($data['thumb'] ? '<i class="fa fa-photo"></i> ' : '').dr_keyword_highlight($value, $param['keyword']);
        !$title && $title = '...';

        if($data['status']==99) {
            if($data['islink']) {
                $url = $data['url'];
            } elseif(strpos($data['url'],'http://')!==false || strpos($data['url'],'https://')!==false) {
                $url = $data['url'];
            } else {
                $release_siteurl = substr((string)dr_site_info('domain', dr_cat_value($data['catid'], 'siteid')),0,-1);
                $url = $release_siteurl.$data['url'];
            }
        } else {
            $url = '?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'';
        }

        $this->m!='content' && $this->c!='content' && $data['url'] = $url = '';

        $html = (isset($data['url']) && $data['url'] && $url ? '<a href="'.$url.'" target="_blank"'.($data['status']!=99 ? ' onclick=\'window.open("?m='.$this->m.'&c='.$this->c.'&a=public_preview&catid='.$data['catid'].'&id='.$data['id'].'","manage")\'' : '').' class="tooltips"'.title_style($data['style']).' data-container="body" data-placement="top" data-original-title="'.$value.'" title="'.$value.'">'.$title.'</a>' : $title).($data['islink'] > 0 ? ' <i class="fa fa-link font-green tooltips" data-original-title="'.L('转向链接').'" title="'.L('转向链接').'"></i>' : '');
        if ($data['id']) {
            $position_db = pc_base::load_model('position_model');
            $position_data_db = pc_base::load_model('position_data_model');
            $flag = $position_data_db->select(array('id'=>$data['id'], 'catid'=>$data['catid']));
            if ($flag) {
                $arr = $position_db->select();
                $ico = array(1 => 'success', 2 => 'danger', 3 => 'info', 4 => 'warning');
                foreach($flag as $t) {
                    $html .= '&nbsp;<span class="label label-'.($ico[$t['posid']] ? $ico[$t['posid']] : 'default').' tooltips" data-original-title="'.$arr[$t['posid']-1]['name'].'" title="'.$arr[$t['posid']-1]['name'].'">'.$arr[$t['posid']-1]['name'].'</span>';
                }
            }
        }
        return $html;
    }

    // 用于列表显示ip地址
    public function ip($value, $param = array(), $data = array(), $field = array()) {
        if ($value) {
            list($value) = explode('-', $value);
            return '<a href="https://www.baidu.com/s?wd='.$value.'&action=cms" target="_blank">'.pc_base::load_sys_class('ip_area')->address($value).'</a>';
        }
        return L('无');
    }

    // url链接输出
    public function url($value, $param = array(), $data = array(), $field = array()) {
        if (!$value) {
            return '';
        }
        return '<a href="'.$value.'" target="_blank">'.$value.'</a>';
    }

    // 用于列表显示单图片专用
    public function image($value, $param = array(), $data = array(), $field = array()) {

        if ($value) {
            $file = get_attachment($value);
            if ($file) {
                $value = $file['url'];
            }
            $url = 'javascript:dr_preview_image(\''.$value.'\');';
            return '<a class="thumbnail" style="display: inherit;" href="'.$url.'"><img style="width:30px" src="'.thumb($value, 100, 100).'"></a>';
        }

        return L('无');
    }

    // 用于列表显示多图片专用
    public function images($value, $param = array(), $data = array(), $field = array()) {

        if ($value) {
            $rt = array();
            $arr = dr_get_files($value);
            foreach ($arr as $t) {
                $file = get_attachment($t['file']);
                if ($file) {
                    $value = $file['url'];
                } else {
                    $value = (string)$t['file'];
                }
                $url = 'javascript:dr_preview_image(\''.$value.'\');';
                $rt[] = '<a class="thumbnail" style="display: inherit;" href="'.$url.'"><img style="width:30px" src="'.thumb($value, 100, 100).'"></a>';
            }
            return implode('', $rt);
        }

        return L('无');
    }

    // 用于列表显示单文件
    public function file($value, $param = array(), $data = array(), $field = array()) {
        if ($value) {
            $file = get_attachment($value);
            if ($file) {
                $value = $file['url'];
            }
            $ext = trim(strtolower(strrchr($value, '.')), '.');
            $file = WEB_PATH.'api.php?op=icon&fileext='.$ext;
            if (dr_is_image($ext)) {
                $url = 'javascript:dr_preview_image(\''.$value.'\');';
                return '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
            } elseif ($ext == 'mp4') {
                $url = 'javascript:dr_preview_video(\''.dr_file($value).'\');';
                return '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
            } elseif ($ext == 'mp3') {
                $url = 'javascript:dr_preview_audio(\''.dr_file($value).'\');';
                return '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
            } elseif (strpos($value, 'http://') === 0) {
                $url = 'javascript:dr_preview_url(\''.$value.'\');';
                return '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
            } else {
                return $value;
            }
        }
        return L('无');
    }

    // 用于列表显示多文件
    public function files($value, $param = array(), $data = array(), $field = array()) {

        if ($value) {
            $rt = array();
            $arr = dr_get_files($value);
            foreach ($arr as $t) {
                $file = get_attachment($t['file']);
                if ($file) {
                    $value = $file['url'];
                } else {
                    $value = (string)$t['file'];
                }
                $ext = trim(strtolower(strrchr($value, '.')), '.');
                $file = WEB_PATH.'api.php?op=icon&fileext='.$ext;
                if (dr_is_image($ext)) {
                    $url = 'javascript:dr_preview_image(\''.$value.'\');';
                    $rt[] = '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                } elseif ($ext == 'mp4') {
                    $url = 'javascript:dr_preview_video(\''.dr_file($value).'\');';
                    $rt[] = '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                } elseif ($ext == 'mp3') {
                    $url = 'javascript:dr_preview_audio(\''.dr_file($value).'\');';
                    $rt[] = '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                } elseif (strpos($value, 'http://') === 0) {
                    $url = 'javascript:dr_preview_url(\''.$value.'\');';
                    $rt[] = '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                } else {
                    $rt[] = $value;
                }
            }
            return implode('', $rt);
        }

        return L('无');
    }

    // 用于列表显示用户组
    public function group($value, $param = array(), $data = array(), $field = array()) {

        if (dr_is_empty($value)) {
            return L('无');
        }

        if ($value) {
            $value = explode(',',$value);
            $grouplist = getcache('grouplist', 'member');
            if ($grouplist) {
                $i = 0;
                $rt = '';
                $color = ['blue', 'red', 'green', 'dark', 'yellow'];
                foreach ($value as $v) {
                    $cs = isset($color[$i]) && $color[$i] ? $color[$i] : 'default';
                    $rt.= '<label class="btn btn-xs '.$cs.'">'.$grouplist[$v]['name'].'</label>';
                    $i++;
                }
                return $rt;
            }
        }

        return L('无');
    }

    // 用于列表显示价格
    public function price($value, $param = array(), $data = array(), $field = array()) {
        if (dr_is_empty($value)) {
            return '';
        }
        return '<span style="color:#ef4c2f">￥'.number_format(floatval($value), 2).'</span>';
    }

    // 用于列表显示价格
    public function money($value, $param = array(), $data = array(), $field = array()) {
        if (dr_is_empty($value)) {
            return '';
        }
        return '<span style="color:#ef4c2f">'.number_format(floatval($value), 2).'</span>';
    }

    // 用于列表显示积分
    public function score($value, $param = array(), $data = array(), $field = array()) {
        if (dr_is_empty($value)) {
            return '';
        }
        return '<span style="color:#2f5fef">'.intval($value).'</span>';
    }

    // 单选字段name
    public function radio_name($value, $param = array(), $data = array(), $field = array()) {

        if (dr_is_empty($value)) {
            return '';
        }

        if ($field) {
            $options = dr_format_option_array($field['setting']['options']);
            if ($options && isset($options[$value])) {
                return $options[$value];
            }
        }

        return $value;
    }

    // 下拉字段name值
    public function select_name($value, $param = array(), $data = array(), $field = array()) {

        if (dr_is_empty($value)) {
            return '';
        }

        if ($field) {
            $options = dr_format_option_array($field['setting']['options']);
            if ($options && isset($options[$value])) {
                return $options[$value];
            }
        }

        return $value;
    }

    // checkbox字段name值
    public function checkbox_name($value, $param = array(), $data = array(), $field = array()) {

        if (dr_is_empty($value)) {
            return '';
        }

        $arr = dr_string2array($value);
        if (!is_array($arr)) {
            $arr = strpos($value, ',') !== false ? explode(',', $value) : array($value);
        }
        if ($field && is_array($arr)) {
            $options = dr_format_option_array($field['setting']['options']);
            if ($options) {
                $rt = array();
                foreach ($options as $i => $v) {
                    if (dr_in_array($i, $arr)) {
                        $rt[] = $v;
                    }
                }
                return implode('、', $rt);
            }
        }

        return $value;
    }

    // 联动字段name值
    public function linkage_name($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            return '';
        }

        $setting = dr_string2array($field['setting']);
        if ($field && $setting['linkage']) {
            return dr_linkagepos($setting['linkage'], $value, $setting['space']);
        }

        return $value;
    }

    // 联动多项字段name值
    public function linkages_name($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            return '';
        }

        $setting = dr_string2array($field['setting']);
        if ($field && $setting['linkage']) {
            $rt = array();
            $values = dr_string2array($value);
            foreach ($values as $value) {
                $rt[] = dr_linkagepos($setting['linkage'], $value, $setting['space']);
            }
            return implode('、', $rt);
        }

        return $value;
    }

    // 实时存储时间值
    public function save_time_value($value, $param = array(), $data = array(), $field = array()) {

        $url = '?m='.$this->m.'&c='.$this->c.'&a=public_save_value_edit&catid='.$data['catid'].'&id='.$data['id'].'&after='; //after是回调函数
        $html = '<input type="text" class="form-control" placeholder="" value="'.dr_date($value).'" onblur="dr_ajax_save(dr_strtotime(this.value), \''.$url.'\', \''.$field['field'].'\')">';

        pc_base::load_sys_class('cache')->set_auth_data('function_list_save_text_value', param::get_session('userid'), 1);

        return $html;
    }

    // 实时存储文本值
    public function save_text_value($value, $param = array(), $data = array(), $field = array()) {

        $url = '?m='.$this->m.'&c='.$this->c.'&a=public_save_value_edit&catid='.$data['catid'].'&id='.$data['id'].'&after='; //after是回调函数
        $html = '<input type="text" class="form-control" placeholder="" value="'.html2code($value).'" onblur="dr_ajax_save(this.value, \''.$url.'\', \''.$field['field'].'\')">';

        pc_base::load_sys_class('cache')->set_auth_data('function_list_save_text_value', param::get_session('userid'), 1);

        return $html;
    }

    // 实时存储选择值
    public function save_select_value($value, $param = array(), $data = array(), $field = array()) {

        $url = '?m='.$this->m.'&c='.$this->c.'&a=public_save_value_edit&catid='.$data['catid'].'&name='.$field['field'].'&id='.$data['id'].'&after='; //after是回调函数

        $html = '<a href="javascript:;" onclick="dr_ajax_list_open_close(this, \''.$url.'\');" value="'.(string)$value.'" class="badge badge-'.($value ? "yes" : "no").'"><i class="fa fa-'.($value ? "check" : "times").'"></i></a>';

        pc_base::load_sys_class('cache')->set_auth_data('function_list_save_text_value', param::get_session('userid'), 1);

        return $html;
    }

    // 文本显示
    public function text($value, $param = array(), $data = array(), $field = array()) {

        if (!$value) {
            return '';
        }

        return clearhtml($value);
    }
}