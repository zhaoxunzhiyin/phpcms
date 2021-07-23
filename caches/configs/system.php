<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');
return array(
//网站路径
'web_path' => '/',
//Session配置
'session_storage' => 'mysqli',
'session_ttl' => 1800,
'session_savepath' => CACHE_PATH.'sessions/',
'session_n' => 0,
//Cookie配置
'cookie_domain' => '', //Cookie 作用域
'cookie_path' => '', //Cookie 作用路径
'cookie_pre' => 'gOeBd_', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
'cookie_ttl' => 0, //Cookie 生命周期，0 表示随浏览器进程
//模板相关配置
'tpl_root' => 'templates/', //模板保存物理路径
'tpl_name' => 'default', //当前模板方案目录
'tpl_css' => 'default', //当前样式目录
'tpl_referesh' => 1,
'tpl_edit'=> 0,//是否允许在线编辑模板

//附件相关配置
'attachment_stat' => '1',//是否记录附件使用状态 0 统计 1 统计， 注意: 本功能会加重服务器负担
'attachment_file' => '0',//附件是否使用分站 0 否 1 是
'sys_attachment_save_id' => 0, //附件存储策略
'sys_attachment_safe' => 0, //附件上传安全模式
'sys_attachment_path' => '', //附件上传路径
'sys_attachment_save_type' => 0, //附件存储方式
'sys_attachment_save_dir' => '', //附件存储目录
'sys_attachment_url' => '', //附件访问地址
'sys_avatar_path' => '', //头像上传路径
'sys_avatar_url' => '', //头像访问地址
'sys_thumb_path' => '', //缩略图存储目录
'sys_thumb_url' => '', //缩略图访问地址

'js_path' => 'http://localhost/statics/js/', //CDN JS
'css_path' => 'http://localhost/statics/css/', //CDN CSS
'img_path' => 'http://localhost/statics/images/', //CDN img
'mobile_js_path' => 'http://localhost/mobile/statics/js/', //CDN JS
'mobile_css_path' => 'http://localhost/mobile/statics/css/', //CDN CSS
'mobile_img_path' => 'http://localhost/mobile/statics/images/', //CDN img
'app_path' => 'http://localhost/',//动态域名配置地址
'mobile_path' => 'http://localhost/mobile/',//动态手机域名配置地址
'editor' => '0', //编辑器模式 0 UEditor 1 CKEditor

'charset' => 'utf-8', //网站字符集
'timezone' => '8', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
'debug' => 0, //是否显示调试信息
'needcheckcomeurl' => 1, //是否需要检查外部访问，1为启用，0为禁用
'admin_log' => 1, //是否记录后台操作日志
'errorlog' => 1, //1、保存错误日志到 cache/error_log.php | 0、在页面直接显示
'gzip' => 1, //是否Gzip压缩后输出
'auth_key' => 'vOvQfBSCqW4pORChZd7G', //密钥
'lang' => 'zh-cn',  //网站语言包
'lock_ex' => '1',  //写入缓存时是否建立文件互斥锁定（如果使用nfs建议关闭）

'admin_founders' => '1', //网站创始人ID，多个ID逗号分隔
'execution_sql' => 0, //EXECUTION_SQL

'html_root' => '/html',//生成静态文件路径
'mobile_root' => '/mobile',//生成手机静态文件路径

'connect_enable' => '1', //是否开启外部通行证
'sina_akey' => '', //sina AKEY
'sina_skey' => '', //sina SKEY

'snda_akey' => '', //盛大通行证 akey
'snda_skey' => '', //盛大通行证 skey

'qq_akey' => '', //qq skey
'qq_skey' => '', //qq skey

'qq_appkey' => '', //QQ号码登录 appkey
'qq_appid' => '', //QQ号码登录 appid
'qq_callback' => '', //QQ号码登录 callback

'keywordapi' => '0', //关键词提取 0 百度 1 讯飞
'baidu_aid' => '', //百度关键词提取 APPID
'baidu_skey' => '', //百度关键词提取 APIKey
'baidu_arcretkey' => '', //百度关键词提取 Secret Key
'baidu_qcnum' => '10', //百度关键词提取 百度分词数量
'xunfei_aid' => '', //讯飞关键词提取 APPID
'xunfei_skey' => '', //讯飞关键词提取 APIKey

'admin_login_path' => '',//自定义的后台登录地址
);
?>