<?php
/**
 * 静态生成
 */
class html {
    protected $webpath;
    protected $psize = 20; // 每页生成多少条

    public function __construct() {
        $this->db = pc_base::load_model('content_model');
        $this->siteid = get_siteid();
    }

    // 栏目的数量统计
    public function get_category_data($cat, $pagesize, $maxsize) {

        // 获取生成栏目
        if (!$cat) {
            dr_json(0, '没有可用生成的栏目数据');
        }

        $name = 'category-html-file';
        $cache_class = pc_base::load_sys_class('cache');
        $cache_class->del_auth_data($name, $this->siteid);

        $list = array();
        foreach ($cat as $i => $t) {
            $setting = dr_string2array($t['setting']);
            if(!$setting['ishtml']) continue;
            if ($t['modelid'] == 0 && $t['type'] == 1) {
                // 单网页
                $list[$t['modelid']][] = array(
                    'catid' => $t['catid'],
                    'modelid' => $t['modelid'],
                    'url' => $t['url'],
                    'page' => 1,
                    'catname' => $t['catname'],
                    'ishtml' => $setting['ishtml'],
                );
            } elseif ($t['type'] == 0) {
                // 模块
                // 判断模块表是否存在被安装
                $this->model_db = pc_base::load_model('sitemodel_model');
                $module = $this->model_db->get_one(array('modelid'=>$t['modelid'],'type'=>0));
                if (!$this->model_db->table_exists($module['tablename'])) {
                    unset($list[$t['modelid']]);
                    continue;
                }
                if ($t['child']) {
                    $template = $setting['category_template'] ? $setting['category_template'] : 'category';
                    $template_list = $setting['template_list'];
                    if(file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$template_list.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$template.'.html')) {
                        $html = file_get_contents(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$template_list.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$template.'.html');
                    }
                    if (strstr($html, '{$pages}')) {
                        $this->db->set_model($t['modelid']);
                        $where = 'catid IN ('.$t['arrchildid'].')';
                        $total = $this->db->count($where); // 统计栏目的数据量
                    }
                    // 判断是封面页面
                    $list[$t['modelid']][] = array(
                        'catid' => $t['catid'],
                        'url' => $t['url'],
                        'modelid' => $t['modelid'],
                        'page' => 1,
                        'catname' => $t['catname'],
                        'ishtml' => $setting['ishtml'],
                    );
                    if ($total) {
                        // 分页
                        !$pagesize && $pagesize = 10; // 默认10条分页
                        $count = ceil($total/$pagesize); // 计算总页数
                        if ($maxsize && $count > $maxsize) {
                            $count = $maxsize;
                        }
                        if ($count > 1) {
                            for ($i = 1; $i <= $count; $i++) {
                                $list[$t['modelid']][] = array(
                                    'catid' => $t['catid'],
                                    'modelid' => $t['modelid'],
                                    'url' => $t['url'],
                                    'page' => $i,
                                    'catname' => $t['catname'].'【第'.$i.'页】',
                                    'ishtml' => $setting['ishtml'],
                                );
                            }
                        }
                    }
                } else {
                    $this->db->set_model($t['modelid']);
                    $total = $this->db->count(array('catid'=>$t['catid'])); // 统计栏目的数据量
                    $list[$t['modelid']][] = array(
                        'catid' => $t['catid'],
                        'modelid' => $t['modelid'],
                        'url' => $t['url'],
                        'page' => 1,
                        'catname' => $t['catname'],
                        'ishtml' => $setting['ishtml'],
                    );
                    if ($total) {
                        // 分页
                        !$pagesize && $pagesize = 10; // 默认10条分页
                        $count = ceil($total/$pagesize); // 计算总页数
                        if ($maxsize && $count > $maxsize) {
                            $count = $maxsize;
                        }
                        if ($count > 1) {
                            for ($i = 1; $i <= $count; $i++) {
                                $list[$t['modelid']][] = array(
                                    'catid' => $t['catid'],
                                    'modelid' => $t['modelid'],
                                    'url' => $t['url'],
                                    'page' => $i,
                                    'catname' => $t['catname'].'【第'.$i.'页】',
                                    'ishtml' => $setting['ishtml'],
                                );
                            }
                        }
                    }
                }
            }
        }

        if (!dr_count($list)) {
            dr_json(0, '没有可用生成的栏目数据');
        }

        $ct = 0;

        $cache = array();
        foreach ($list as $data) {
            $ct+= dr_count($data);
            $arr = array_chunk($data, $this->psize);
            $cache = dr_array2array($cache, $arr);
        }
        foreach ($cache as $i => $t) {
            $cache_class->set_auth_data($name.'-'.($i+1), $t, $this->siteid);
        }

        $count = dr_count($cache);

        $cache_class->set_auth_data($name, $count, $this->siteid);

        dr_json(1, '共'.$ct.'个，分'.$count.'页');
    }

    // 内容的数量统计
    public function get_show_data($modelid, $param) {

        $cache_class = pc_base::load_sys_class('cache');
        $name = 'show-'.$modelid.'-html-file';
        $cache_class->del_auth_data($name, $param['siteid']);

        // 获取生成栏目
        $cids = array();
        if ($param['catids']) {
            $catids = explode(',', $param['catids']);
            if ($catids) {
                $cats = getcache('category_content_'.$param['siteid'],'commons');
                foreach ($catids as $id) {
                    if ($cats[$id]) {
						$setting = string2array($cats[$id]['setting']);
						if ($setting['disabled']) continue;
						if($param['siteid'] != $cats[$id]['siteid'] || $cats[$id]['type']!=0) continue;
						if($modelid && $modelid != $cats[$id]['modelid']) continue;
						if(!$setting['content_ishtml']) continue;
                        $cids = dr_array2array($cids, explode(',', $cats[$id]['arrchildid']));
                    }
                }
                $cids = array_unique($cids);
            }
        } else {
            $cats = getcache('category_content_'.$param['siteid'],'commons');
            foreach($cats as $catid=>$r) {
                $setting = string2array($r['setting']);
                if ($setting['disabled']) continue;
                if($param['siteid'] != $r['siteid'] || $r['type']!=0) continue;
                if($modelid && $modelid != $r['modelid']) continue;
                if(!$setting['content_ishtml']) continue;
                $cids[] = $catid;
            }
        }

        $this->db->set_model($modelid);
        $where = '`status`=99';
        if (isset($param['fromdate']) && $param['fromdate']) {
            $where .= ' AND `updatetime` BETWEEN ' . strtotime($param['fromdate'].' 00:00:00') . ' AND ' . ($param['todate'] ? strtotime($param['todate'].' 23:59:59') : SYS_TIME);
        } elseif (isset($param['todate']) && $param['todate']) {
            $where .= ' AND `updatetime` BETWEEN 0 AND ' . strtotime($param['todate'].' 23:59:59');
        }
        if (isset($param['toid']) && $param['fromid']) {
            $where .= ' AND `id` BETWEEN '.(int)$param['fromid'].' AND ' . (int)$param['toid'];
        }
        if ($cids) {
            $where .= ' AND catid IN ('. implode(',', $cids).')';
        }
        if (!$cids) {
            dr_json(0, '没有可用生成的内容数据');
        }
        if($param['number']) {
            $data = $this->db->select($where, 'id,catid,title,url,islink,inputtime', $param['number'], 'id DESC'); // 获取需要生成的内容索引
        } else {
            $data = $this->db->select($where, 'id,catid,title,url,islink,inputtime'); // 获取需要生成的内容索引
        }

        if (!dr_count($data)) {
            dr_json(0, '没有可用生成的内容数据');
        }

        $arr = array_chunk($data, $param['pagesize'] ? $param['pagesize'] : $this->psize);
        $count = dr_count($arr);
        foreach ($arr as $i => $t) {
            $cache_class->set_auth_data($name.'-'.($i+1), $t, $param['siteid']);
        }

        $cache_class->set_auth_data($name, $count, $param['siteid']);

        dr_json(1, '共'.dr_count($data).'条，分'.$count.'页');
    }

}