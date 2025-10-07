<?php
/**
 * 静态生成
 */
class html {
    private $db,$model_db;
    public $siteid;
    protected $psize = 20; // 每页生成多少条

    public function __construct() {
        $this->db = pc_base::load_model('content_model');
        $this->model_db = pc_base::load_model('sitemodel_model');
        $this->siteid = get_siteid();
    }

    // 栏目的数量统计
    public function get_category_data($cat, $maxsize) {

        // 获取生成栏目
        if (!$cat) {
            dr_json(0, '没有可用生成的栏目数据');
        }

        $name = 'category-html-file';
        $cache_class = pc_base::load_sys_class('cache');
        $cache_class->del_auth_data($name, $this->siteid);

        $list = array();
        foreach ($cat as $i => $t) {
            $setting = dr_string2array(dr_cat_value($t['catid'], 'setting'));
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
                // 模型
                // 判断模型表是否存在
                $module = $this->model_db->get_one(array('modelid'=>$t['modelid'],'type'=>0));
                $module['setting'] = dr_string2array($module['setting']);
                if (!$this->model_db->table_exists($module['tablename'])) {
                    unset($list[$t['modelid']]);
                    continue;
                }
                if (($t['child'] && isset($module['setting']['pcatpost']) && $module['setting']['pcatpost']) || !$t['child']) {
                    // 内容列表页面、支持分页的封面栏目
                    $this->db->set_model($t['modelid']);
                    if ($t['child'] && $t['catids']) {
                        if (!dr_in_array($t['catid'], $t['catids'])) {
                            $t['catids'][] = $t['catid'];
                        }
                        $total = $this->db->count(array('catid'=>$t['catids'], 'status'=>99)); // 统计栏目的数据量
                    } else {
                        $total = $this->db->count(array('catid'=>(int)$t['catid'], 'status'=>99)); // 统计栏目的数据量
                    }
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
                        $pagesize = (int)$setting['pagesize']; // 每页数量
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
                    // 判断是封面页面
                    $list[$t['modelid']][] = array(
                        'catid' => $t['catid'],
                        'modelid' => $t['modelid'],
                        'url' => $t['url'],
                        'page' => 1,
                        'catname' => $t['catname'],
                        'ishtml' => $setting['ishtml'],
                    );
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
        $models = getcache('model','commons');
        $html = '';
        if (isset($param['fromdate']) && $param['fromdate']) {
            $html .= '-' . strtotime($param['fromdate'].' 00:00:00') . '-' . ($param['todate'] ? strtotime($param['todate'].' 23:59:59') : SYS_TIME);
        } elseif (isset($param['todate']) && $param['todate']) {
            $html .= '-0-' . strtotime($param['todate'].' 23:59:59');
        }
        $html .= $param['pagesize'] ? '-'.$param['pagesize'] : '';
        $html .= $param['number'] ? '-'.$param['number'] : '';
        $html .= $param['fromid'] && $param['toid'] ? '-'.$param['fromid'].'-'.$param['toid'] : '';
        $name = 'show-'.$modelid.'-html-file'.$html;
        $cache_class->del_auth_data($name, $param['siteid']);

        // 获取生成栏目
        $cids = array();
        if ($param['catids']) {
            $catids = explode(',', $param['catids']);
            if ($catids) {
                $cats = get_category($param['siteid']);
                foreach ($catids as $id) {
                    if ($cats[$id]) {
                        $setting = dr_string2array(dr_cat_value($id, 'setting'));
                        if($param['siteid'] != $cats[$id]['siteid'] || ($cats[$id]['type']!=0 && $cats[$id]['child']==0)) continue;
                        if($modelid && $modelid != $cats[$id]['modelid']) continue;
                        if(!$setting['content_ishtml']) continue;
                        $cids = dr_array2array($cids, explode(',', $cats[$id]['arrchildid']));
                    }
                }
                $cids = array_unique($cids);
            }
        } else {
            $cats = get_category($param['siteid']);
            foreach($cats as $catid=>$r) {
                $setting = dr_string2array(dr_cat_value($catid, 'setting'));
                if($param['siteid'] != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
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
            $where .= ' AND `catid` IN ('. implode(',', $cids).')';
        } else {
            dr_json(0, '['.$models[$modelid]['name'].']没有可用生成的内容数据');
        }
        if ($param['ids']) {
            $where .= ' AND `id` IN ('. dr_safe_replace($param['ids']).')';
        }
        $count = $this->db->count($where);
        $pcount = $param['number'] && $param['number']<=$count ? $param['number'] : $count;
        $sql = 'select id,catid,title,url,islink,inputtime from `'.$this->db->table_name.'`';
        if ($where) {
            $sql .= ' where '.$where;
        }

        if (!$pcount) {
            dr_json(0, '['.$models[$modelid]['name'].']没有可用生成的内容数据');
        }

        $psize = $param['pagesize'] ? $param['pagesize'] : $this->psize;
        $cache_class->set_auth_data($name, ceil($pcount/$psize), $param['siteid']);
        $cache_class->set_auth_data($name.'-data', array(
            'sql' => $sql,
            'number' => $param['number'],
            'pagesize' => $psize>$pcount ? $pcount : $psize,
        ), $param['siteid']);

        dr_json(1, '共'.$pcount.'条，分'.ceil($pcount/$psize).'页');
    }

}