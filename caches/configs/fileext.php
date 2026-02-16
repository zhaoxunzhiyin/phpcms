<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

// 禁止以下文件上传
$this->notallowed = [
    'php',
    'php3',
    'php4',
    'php5',
    'asp',
    'jsp',
    'jspx',
    'aspx',
    'exe',
    'sh',
    'phtml',
    'dll',
    'cer',
    'asa',
    'shtml',
    'shtm',
    'asax',
    'cgi',
    'fcgi',
    'pl',
];

// 允许远程下载文件扩展名
$this->down_file_ext = [
    'jpg',
    'jpeg',
    'gif',
    'png',
    'webp',
    'zip',
    'rar',
];