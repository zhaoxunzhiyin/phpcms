<?php
/**
 * 统一返回json格式并退出程序
 */
function _json($code, $msg, $data = []){
    echo dr_array2string(dr_return_data($code, $msg, $data));exit;
}

/**
 * 数据返回统一格式
 */
function dr_return_data($code, $msg = '', $data = []) {
    return array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    );
}


/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function dr_array2string($data) {
    return $data ? json_encode($data, JSON_UNESCAPED_UNICODE | 320) : '';
}

/**
 * 获取cms域名部分
 */
function dr_cms_domain_name($url) {

    $param = parse_url($url);
    if (isset($param['host']) && $param['host']) {
        return $param['host'];
    }

    return $url;
}

/**
 * 目录扫描
 *
 * @param	string	$source_dir		Path to source
 * @param	int	$directory_depth	Depth of directories to traverse
 *						(0 = fully recursive, 1 = current dir, etc)
 * @param	bool	$hidden			Whether to show hidden files
 * @return	array
 */
function dr_file_map($source_dir) {

    if ($fp = @opendir($source_dir)) {

        $filedata = [];
        $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        while (FALSE !== ($file = readdir($fp))) {
            if ($file === '.' OR $file === '..'
                OR $file[0] === '.'
                OR !@is_file($source_dir.$file)) {
                continue;
            }
            $filedata[] = $file;
        }
        closedir($fp);
        return $filedata;
    }

    return FALSE;
}