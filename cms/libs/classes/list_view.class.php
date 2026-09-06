<?php
/**
 * 通用列表视图配置
 *
 * 业务类负责 candidates 定义，本类负责默认配置、用户配置、校验、排序及保存。
 */

defined('IN_CMS') or exit('No permission resources.');

class list_view {

    private $scene;
    private $candidates;
    private $userid;
    private $username;
    private $model;

    public function __construct($scene, $candidates, $userid = 0, $username = '') {
        $this->scene = trim($scene);
        $this->candidates = is_array($candidates) ? $candidates : array();
        $this->userid = intval($userid);
        $this->username = (string)$username;
        $this->model = pc_base::load_model('list_view_config_model');
    }

    public function getCandidates() {
        return $this->candidates;
    }

    public function getConfig() {
        $default = $this->load(0);
        $config = $default ? $default : $this->buildDefault();

        if ($this->userid > 0) {
            $user = $this->load($this->userid);
            if ($user) {
                $config = $user;
            }
        }

        return $this->normalize($config);
    }

    public function getAll() {
        $config = $this->getConfig();
        $result = array();

        foreach ($this->candidates as $field => $candidate) {
            $item = isset($config[$field]) ? $config[$field] : array();
            $result[$field] = $this->mergeField($field, $item);
        }

        return $result;
    }

    public function getVisible() {
        $all = $this->getAll();
        $result = array();

        foreach ($all as $field => $item) {
            if (empty($item['use'])) {
                continue;
            }
            $result[$field] = $item;
        }

        return $result;
    }

    public function validate($config) {
        if (!is_array($config)) {
            return array('code' => 0, 'msg' => '列表字段配置格式错误');
        }

        foreach ($config as $field => $item) {
            if (!isset($this->candidates[$field]) || !is_array($item)) {
                continue;
            }
            if (!empty($item['func'])) {
                $func = trim($item['func']);
                if (!method_exists(pc_base::load_sys_class('function_list'), $func) && !function_exists($func)) {
                    return array('code' => 0, 'msg' => L('列表回调函数['.$func.']未定义'));
                }
            }
        }

        return array('code' => 1, 'msg' => '');
    }

    public function save($config, $userid = null, $username = null) {
        if ($userid === null) {
            $userid = $this->userid;
        }
        $userid = intval($userid);

        if ($username === null) {
            $username = $userid > 0 ? $this->username : '0';
        }

        $config = $this->normalize($config);
        $now = SYS_TIME;
        $row = $this->model->get_one(array(
            'scene' => $this->scene,
            'userid' => $userid,
        ));

        $data = array(
            'scene' => $this->scene,
            'userid' => $userid,
            'username' => $userid > 0 ? (string)$username : '0',
            'config' => array2string($config),
            'updatetime' => $now,
        );

        if ($row) {
            return $this->model->update($data, array('id' => $row['id']));
        }

        $data['addtime'] = $now;
        return $this->model->insert($data, true);
    }

    public function reset($userid = null) {
        if ($userid === null) {
            $userid = $this->userid;
        }
        $userid = intval($userid);
        if ($userid <= 0) {
            return false;
        }
        return $this->model->delete(array(
            'scene' => $this->scene,
            'userid' => $userid,
        ));
    }

    private function buildDefault() {
        $config = array();
        $order = 0;
        foreach ($this->candidates as $field => $item) {
            if (!empty($item['required'])) {
                $config[$field] = array(
                    'use' => 1,
                    'order' => $order++,
                );
            }
        }
        return $config;
    }

    public function normalize($config) {
        if (!is_array($config)) {
            $config = array();
        }

        $result = array();
        $order = 0;

        foreach ($config as $field => $item) {
            if (!isset($this->candidates[$field])) {
                continue;
            }
            if (!is_array($item)) {
                $item = array();
            }

            $result[$field] = array(
                'use' => !empty($item['use']) ? 1 : 0,
                'order' => isset($item['order']) ? intval($item['order']) : $order,
            );

            foreach (array('width', 'center', 'func', 'name') as $key) {
                if (array_key_exists($key, $item)) {
                    $result[$field][$key] = $item[$key];
                }
            }
            $order++;
        }

        foreach ($this->candidates as $field => $item) {
            if (!isset($result[$field])) {
                $result[$field] = array(
                    'use' => !empty($item['required']) ? 1 : 0,
                    'order' => $order++,
                );
            }
            if (!empty($item['required'])) {
                $result[$field]['use'] = 1;
            }
        }

        uasort($result, function($a, $b) {
            $ao = isset($a['order']) ? intval($a['order']) : 0;
            $bo = isset($b['order']) ? intval($b['order']) : 0;
            if ($ao == $bo) {
                return 0;
            }
            return $ao < $bo ? -1 : 1;
        });

        $order = 0;
        foreach ($result as $field => $item) {
            $result[$field]['order'] = $order++;
        }

        return $result;
    }

    private function load($userid) {
        $row = $this->model->get_one(array(
            'scene' => $this->scene,
            'userid' => intval($userid),
        ));
        if (!$row || !$row['config']) {
            return array();
        }
        $config = string2array($row['config']);
        return is_array($config) ? $config : array();
    }

    private function mergeField($field, $item) {
        $candidate = $this->candidates[$field];
        $result = $candidate;

        foreach (array('use', 'order', 'width', 'center', 'func', 'name') as $key) {
            if (array_key_exists($key, $item)) {
                $result[$key] = $item[$key];
            }
        }

        if (!empty($candidate['required'])) {
            $result['use'] = 1;
        }

        return $result;
    }
}
?>