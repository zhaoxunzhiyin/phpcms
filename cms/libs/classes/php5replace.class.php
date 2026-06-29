<?php
// 正则替换方法
class php5replace {

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    // 替换常量值
    function php55_replace_var($value) {
        if (defined($value[1])) {
            // 常量
            return constant($value[1]);
        } else {
            // 数组
            $val = dr_site_info($value[1], SITE_ID);
            return call_user_func(function($arg) {
                return $arg;
            }, ($val ? $val : '""'));
        }
    }

    // 替换数组变量值
    function php55_replace_data($value) {
        if (isset($value[2]) && $value[2] && isset($this->data[$value[1]]) && is_array($this->data[$value[1]])
            && isset($this->data[$value[1]][$value[2]])) {
            return $this->data[$value[1]][$value[2]];
        }
        return $this->data[$value[1]];
    }

    // 替换数组变量值
    function php55_replace_or_data($value) {
        if (isset($value[1]) && $value[1]
            && isset($this->data[$value[1]]) && $this->data[$value[1]]) {
            return $this->data[$value[1]];
        } elseif (isset($value[2]) && $value[2]
            && isset($this->data[$value[2]]) && $this->data[$value[2]]) {
            return $this->data[$value[2]];
        }
        return '';
    }

    // 替换函数值
    function php55_replace_function($value) {
        if (!dr_is_safe_function($value[1])) {
            return '函数['.$value[1].']不安全，禁止在此处使用';
        } elseif (function_exists($value[1])) {
            // 执行函数体
            $param = '';
            if ($value[2]) {
                $p = $value[2] == '$data' ? $this->data : $value[2];
                $param = is_array($p) ? ['data' => $p] : explode(',', $p);
                foreach ($param as $i => $t) {
                    if (!is_array($t) && strpos($t, '$') === 0) {
                        $param[$i] = $this->data[substr($t, 1)];
                    }
                }
            }
            return $param ? call_user_func_array($value[1], $param) : call_user_func($value[1]);
        } else {
            return '函数['.$value[1].']未定义';
        }

        return $value[0];
    }

    // 替换全部
    function replace($value) {

        $value = preg_replace_callback('#{\$([a-z_0-9]+)}#U', [$this, 'php55_replace_data'], $value);
        $value = preg_replace_callback('#{\$([a-z_0-9]+)\.([a-z_0-9]+)}#U', [$this, 'php55_replace_data'], $value);
        $value = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', [$this, 'php55_replace_function'], $value);
        $value = preg_replace_callback('#{([A-Z_]+)}#U', [$this, 'php55_replace_var'], $value);
        $value = preg_replace_callback('#{([a-z_0-9]+)}#U', [$this, 'php55_replace_data'], $value);
        $value = preg_replace_callback('#{([a-z_0-9]+)\|\|([a-z_0-9]+)}#U', [$this, 'php55_replace_or_data'], $value);
        $value = preg_replace_callback('#{([a-z_0-9]+)\.([a-z_0-9]+)}#U', [$this, 'php55_replace_data'], $value);

        return $value;
    }

}