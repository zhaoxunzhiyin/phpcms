<?php
defined('IN_CMS') or exit('No permission resources.');
class sync {
    private $input;
    private $fversion = '3.0'; // 客户端版本测试

    public function __construct() {
        $this->input = pc_base::load_sys_class('input');
    }

    // 客户端程序下载
    public function down_zip($data) {

        $rt = $this->_create_zip($data);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        $zfile = $rt['msg'];

        set_time_limit(0);  //大文件在读取内容未结束时会被超时处理，导致下载文件不全。


        $handle = fopen($zfile,"rb");
        if (FALSE === $handle) {
            return dr_return_data(0, L('no_zip_check'));
        }

        $filesize = filesize($zfile);
        header("Content-Type: application/zip"); //zip格式的
        header("Accept-Ranges:bytes");
        header("Accept-Length:".$filesize);
        header("Content-Disposition: attachment; filename=".dr_get_domain_name($data['domain']).".zip");

        $contents = '';
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            echo $contents;
            @ob_flush();  //把数据从PHP的缓冲中释放出来
            flush();      //把被释放出来的数据发送到浏览器
        }
        fclose($handle);

        unlink($zfile);
    }

    // 客户端后台登录地址
    public function sync_admin_url($data) {
        return trim($data['domain'], '/').'/api/fclient/index.php?at=admin&id='.md5($data['id']).'&sync='.$data['sn'];
    }

    // 客户端通信测试
    public function sync_test($data) {

        $setting = dr_string2array($data['setting']);
        if ($setting['mode']) {
            // 本地服务器
            if (!$setting['webpath']) {
                return dr_return_data(0, L('not_local_web_path'));
            }
            $path = dr_get_dir_path($setting['webpath']);
            if (is_dir($path)) {
                if (is_file($path.'index.php')) {
                    // 正常目录时
                    // 没有安装客户端
                    $rt = $this->_create_zip($data);
                    if (!$rt['code']) {
                        return dr_return_data(0, $rt['msg']);
                    }
                    // 解压压缩包到web目录
                    pc_base::load_sys_class('file')->unzip($rt['msg'], $path);
                    if (!is_file($path.'api/fclient/index.php')) {
                        return dr_return_data(0, L('local_not_web_path'));
                    }
                } else {
                    return dr_return_data(1, L('not_web_path_not_cms'));
                }
            } else {
                return dr_return_data(0, L('no_local_web_path'));
            }
            if (!is_file($path.'caches/configs/version.php')) {
                return dr_return_data(0, L('no_cms_web_version'));
            }
            $v = require $path.'caches/configs/version.php';
            return dr_return_data(1, L('web_cms_version').$v['cms_version'].' ['.$v['cms_release'].']');
        }

        $url = trim($data['domain'], '/').'/api/fclient/index.php?id='.md5($data['id']).'&sync='.$data['sn'];

        $p = array(
            'id' =>$data['id'],
            'sync' => $data['sn'],
            'data' => $this->get_sync_config($data)
        );
        $data = $this->_request_post($url, $p);
        $json = json_decode($data, true);
        if (!$json) {
            return dr_return_data(0, $data);
        } elseif (!$json['code']) {
            return dr_return_data(0, $json['msg']);
        }

        if (!$json['data'] || version_compare($json['data'], $this->fversion) < 0) {
            return dr_return_data(0, '客户端版本[v'.$json['data'].']需要更新升级到[v'.$this->fversion.']');
        }

        return dr_return_data(1, L('send_check'));
    }

    // 客户端配置生成文件
    private function get_sync_config($data) {
        //读取配置文件
        $appdata = array();
        $siteid = get_siteid();//当前站点 
        //更新模型数据库,重设setting 数据. 
        $m_db = pc_base::load_model('module_model');
        $appdata = $m_db->select(array('module'=>'fclient'));
        $setting = string2array($appdata[0]['setting']);
        $app = $setting[$siteid]; //当前站点配置
        $config = file_get_contents(PC_PATH.'modules/fclient/code/api/fclient/sync.php');
        return str_replace(array(
            '{id}',
            '{sn}',
            '{name}',
            '{domain}',
            '{endtime}',
            '{status}',
            '{pay_url}',
            '{close_url}',
            '{server_url}',
        ), array(
            $data['id'],
            $data['sn'],
            $data['name'],
            $data['domain'],
            $data['endtime'],
            $data['status'],
            $app['pay_url'],
            $app['close_url'],
            APP_PATH,
        ), $config);
    }

    // 客户端版本生成文件
    private function get_sync_version($data) {

        $config = file_get_contents(PC_PATH.'modules/fclient/code/api/fclient/version.php');
        return str_replace('{version}', $this->fversion, $config);
    }

    // 创建压缩包
    private function _create_zip($data) {

        if (!class_exists('ZipArchive')) {
            return dr_return_data(0, L('php_zip'));
        }

        // 解压到新目录
        $webpath = CACHE_PATH.'temp/fclient-'.$data['id'].'/';
        dr_dir_delete($webpath);

        // 复制过去
        $this->copy_dir(PC_PATH.'modules/fclient/code/', PC_PATH.'modules/fclient/code/', $webpath);

        $size = file_put_contents($webpath.'api/fclient/version.php', $this->get_sync_version($data));
        if (!$size) {
            return dr_return_data(0, L('write_no'));
        }

        $size = file_put_contents($webpath.'api/fclient/sync.php', $this->get_sync_config($data));
        if (!$size) {
            return dr_return_data(0, L('setting_no'));
        }

        $zfile = CACHE_PATH.'temp/fclient-'.$data['id'].'.zip';

        $zip = new ZipArchive();

        if(!$zip->open($zfile, ZipArchive::CREATE))
        {
            return dr_return_data(0, L('php_zip_no'));
        }

        $this->createZip(opendir($webpath), $zip, $webpath);
        $zip->close();
        dr_dir_delete($webpath);

        if (!is_file($zfile)) {
            return dr_return_data(0, L('client_no'));
        }

        return dr_return_data(1, $zfile);
    }

    // 复制目录
    private function copy_dir($basedir, $filepath, $savepath){
        if ($dh = opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (strpos($file, '.') !== 0){
                    if (!is_dir($basedir."/".$file)) {
                        $fl = str_replace($filepath, '', $basedir."/".$file);
                        dr_mkdirs(dirname($savepath.$fl));
                        $code = file_get_contents($basedir."/".$file);
                        file_put_contents($savepath.$fl, $code);
                    }else{
                        $dirname = $basedir."/".$file;
                        $this->copy_dir($dirname, $filepath, $savepath);
                    }
                }
            }
            closedir($dh);
        }
    }

    /*压缩多级目录
          $openFile:目录句柄
          $zipObj:Zip对象
          $sourceAbso:源文件夹路径
      */
    private function createZip($openFile,$zipObj,$sourceAbso,$newRelat = '')
    {
        while(($file = readdir($openFile)) != false)
        {
            if($file=="." || $file=="..")
                continue;

            /*源目录路径(绝对路径)*/
            $sourceTemp = $sourceAbso.'/'.$file;
            /*目标目录路径(相对路径)*/
            $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;
            if(is_dir($sourceTemp))
            {
                //echo '创建'.$newTemp.'文件夹<br/>';
                $zipObj->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/
                $this->createZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);
            }
            if(is_file($sourceTemp))
            {
                //echo '创建'.$newTemp.'文件<br/>';
                $zipObj->addFile($sourceTemp,$newTemp);
            }
        }
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    public function _request_post($url = '', $post_data = array()) {

        if (empty($url) || empty($post_data)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = http_build_query($post_data);
        $ch = curl_init();//初始化curl
        if (substr($url, 0, 8) == "https://") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }
}