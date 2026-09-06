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

    /**
     * 获取最终配置。
     * 有个人配置时使用个人配置，否则使用默认配置。
     */
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

    /**
     * 获取当前实际显示字段。
     */
    public function getVisible() {
        $config = $this->getConfig();
        $result = array();

        foreach ($config as $field => $item) {
            if (!isset($this->candidates[$field]) || empty($item['use'])) {
                continue;
            }
            $result[$field] = $this->mergeField($field, $item);
        }

        return $result;
    }

    /**
     * 保存当前用户配置。userid=0 时保存默认配置。
     */
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

    /**
     * 删除个人配置，使用户恢复使用默认配置。
     */
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

    /**
     * 生成 required 字段的默认配置。
     */
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

    /**
     * 兼容旧配置并清理非法字段。
     */
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

        // 新增字段自动进入配置；required 字段强制显示。
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